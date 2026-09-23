<?php

namespace App\Services\Payments;

use App\Models\Order;
use App\Models\PaymentGateway;
use App\Models\PaymentTransaction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    /**
     * Cek apakah Midtrans sudah dikonfigurasi (Server Key tersedia).
     */
    public static function isConfigured(): bool
    {
        return !empty(self::getServerKey());
    }

    /**
     * Ambil status environment (Production atau Sandbox).
     */
    public static function isProduction(): bool
    {
        $gateway = PaymentGateway::where('code', 'midtrans')->first();
        if ($gateway && !empty($gateway->mode)) {
            return $gateway->mode === 'production';
        }

        return (bool) config('services.midtrans.is_production', false);
    }

    /**
     * Ambil Server Key dari PaymentGateway registry atau fallback ke .env.
     */
    public static function getServerKey(): ?string
    {
        return PaymentGateway::credential('midtrans', 'server_key', config('services.midtrans.server_key'));
    }

    /**
     * Ambil Client Key dari PaymentGateway registry atau fallback ke .env.
     */
    public static function getClientKey(): ?string
    {
        return PaymentGateway::credential('midtrans', 'client_key', config('services.midtrans.client_key'));
    }

    /**
     * URL script Snap JS untuk frontend.
     */
    public static function getSnapJsUrl(): string
    {
        return self::isProduction()
            ? 'https://app.midtrans.com/snap/snap.js'
            : 'https://app.sandbox.midtrans.com/snap/snap.js';
    }

    /**
     * Endpoint API Snap Transactions.
     */
    public static function getSnapApiUrl(): string
    {
        return self::isProduction()
            ? 'https://app.midtrans.com/snap/v1/transactions'
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';
    }

    /**
     * Mapping payment method kode internal VexaHost ke enabled_payments Midtrans.
     */
    public static function mapEnabledPayment(string $method): ?array
    {
        return match ($method) {
            'mandiri_va' => ['echannel'],
            'bni_va' => ['bni_va'],
            'bri_va' => ['bri_va'],
            'permata_va' => ['permata_va'],
            'cimb_va' => ['cimb_va'],
            'other_va' => ['other_va', 'permata_va'],
            'gopay' => ['gopay'],
            'midtrans_snap' => ['qris', 'other_qris', 'gopay'],
            default => null, // null berarti tidak membatasi (Midtrans menampilkan semua channel aktif)
        };
    }

    /**
     * Buat atau pulihkan transaksi Midtrans Snap untuk pesanan tertentu.
     *
     * @return array{success: bool, token?: string, redirect_url?: string, error?: string}
     */
    public static function createSnapTransaction(Order $order, ?string $paymentMethod = null): array
    {
        $serverKey = self::getServerKey();
        if (empty($serverKey)) {
            Log::error('Midtrans Snap error: MIDTRANS_SERVER_KEY tidak ditemukan.');
            return [
                'success' => false,
                'error' => 'Server Key Midtrans belum dikonfigurasi.',
            ];
        }

        $method = $paymentMethod ?: $order->payment_method;

        // 1. Cek apakah sudah pernah generate token yang masih aktif (dalam 12 jam terakhir)
        $existingTx = PaymentTransaction::where('order_id', $order->id)
            ->where('provider', 'midtrans')
            ->where('status', 'pending')
            ->where('created_at', '>=', now()->subHours(12))
            ->latest()
            ->first();

        if ($existingTx && !empty($existingTx->raw_payload['snap_token']) && !empty($existingTx->raw_payload['snap_redirect_url'])) {
            return [
                'success' => true,
                'token' => $existingTx->raw_payload['snap_token'],
                'redirect_url' => $existingTx->raw_payload['snap_redirect_url'],
                'midtrans_order_id' => $existingTx->provider_order_ref,
            ];
        }

        // 2. Format Order ID unik untuk Midtrans
        // Gunakan timestamp agar pesanan yang diulang tidak ditolak oleh Midtrans dengan error "duplicate order_id"
        $midtransOrderId = 'ORDER-' . $order->id . '-' . time();
        $grossAmount = (int) round($order->amount);

        // 3. Susun data customer
        $customer = $order->customer;
        $customerName = $customer ? ($customer->full_name ?: $customer->username ?: 'Pelanggan') : 'Pelanggan';
        $customerEmail = $customer ? $customer->email : 'support@vexahost.id';
        $customerPhone = $customer ? ($customer->phone ?: '') : '';

        // 4. Susun item details
        $specName = $order->vpsSpec ? $order->vpsSpec->name : 'Layanan Cloud VPS';
        $itemDetails = [
            [
                'id' => (string) ($order->vps_spec_id ?: $order->id),
                'price' => $grossAmount,
                'quantity' => 1,
                'name' => mb_substr($specName . ' (' . ($order->hostname ?: 'Server') . ')', 0, 50),
            ]
        ];

        // 5. Parameter transaksi Midtrans Snap
        $params = [
            'transaction_details' => [
                'order_id' => $midtransOrderId,
                'gross_amount' => $grossAmount,
            ],
            'customer_details' => [
                'first_name' => $customerName,
                'email' => $customerEmail,
                'phone' => $customerPhone,
            ],
            'item_details' => $itemDetails,
            'callbacks' => [
                'finish' => route('order.payment.status', $order->id),
            ],
            'expiry' => [
                'unit' => 'days',
                'duration' => 1,
            ],
        ];

        // Filter channel jika pelanggan memilih bank / e-wallet spesifik
        $enabledPayments = self::mapEnabledPayment($method);
        if (!empty($enabledPayments)) {
            $params['enabled_payments'] = $enabledPayments;
        }

        // 6. Panggil Snap API Midtrans
        try {
            $response = Http::withBasicAuth(trim($serverKey), '')
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->timeout(15)
                ->post(self::getSnapApiUrl(), $params);

            if (!$response->successful()) {
                Log::error('Midtrans Snap API call failed', [
                    'order_id' => $order->id,
                    'status' => $response->status(),
                    'body' => $response->json() ?: $response->body(),
                ]);

                $errMsg = $response->json('error_messages.0')
                    ?? $response->json('message')
                    ?? 'Gagal menghubungi server Midtrans (HTTP ' . $response->status() . ').';

                return [
                    'success' => false,
                    'error' => $errMsg,
                ];
            }

            $responseData = $response->json();
            $token = $responseData['token'] ?? null;
            $redirectUrl = $responseData['redirect_url'] ?? null;

            if (empty($token)) {
                return [
                    'success' => false,
                    'error' => 'Respon Midtrans tidak menyertakan Snap Token.',
                ];
            }

            // Catat transaksi pending di PaymentTransaction untuk idempotency & pelacakan
            PaymentTransaction::create([
                'order_id' => $order->id,
                'invoice_id' => $order->invoice?->id,
                'provider' => 'midtrans',
                'provider_transaction_id' => null, // akan diisi oleh webhook setelah transaksi dibuat
                'provider_order_ref' => $midtransOrderId,
                'payment_method' => $method,
                'amount' => $grossAmount,
                'currency' => 'IDR',
                'status' => 'pending',
                'raw_payload' => [
                    'snap_token' => $token,
                    'snap_redirect_url' => $redirectUrl,
                    'request_params' => $params,
                ],
            ]);

            return [
                'success' => true,
                'token' => $token,
                'redirect_url' => $redirectUrl,
                'midtrans_order_id' => $midtransOrderId,
            ];
        } catch (\Throwable $e) {
            Log::error('Midtrans Snap Exception: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'error' => 'Terjadi kendala koneksi ke server Midtrans: ' . $e->getMessage(),
            ];
        }
    }
}
