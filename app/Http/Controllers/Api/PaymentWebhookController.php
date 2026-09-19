<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\InvalidStateTransitionException;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PaymentGateway;
use App\Models\PaymentTransaction;
use App\Models\WebhookEvent;
use App\Services\OrderStateMachine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Webhook payment gateway (Midtrans-compatible).
 *
 * Kontrak keamanan (P0):
 *   1. MIDTRANS_SERVER_KEY WAJIB di-set. Tanpa itu, endpoint langsung 500.
 *      Tidak boleh ada mode "skip signature" seperti sebelumnya.
 *   2. Signature diverifikasi sebelum apapun lainnya.
 *   3. Idempotent: webhook duplikat (provider + transaction_id sama) tidak
 *      pernah memproses ulang. Sekali sukses = terkunci.
 *   4. Amount verify: gross_amount di webhook harus == order.amount.
 *      Kalau tidak match → tolak, catat sebagai anomaly.
 *   5. Semua write dalam DB::transaction + lockForUpdate untuk cegah race.
 *   6. Perubahan status order lewat OrderStateMachine (validated transitions).
 *   7. Raw payload disimpan permanen di webhook_events + payment_transactions
 *      untuk forensik.
 */
class PaymentWebhookController extends Controller
{
    public function __construct(protected OrderStateMachine $orderStateMachine)
    {
    }

    public function handle(Request $request)
    {
        // PHASE 4 - registry gateway lebih dulu, jatuh kembali ke .env.
        $serverKey = PaymentGateway::credential('midtrans', 'server_key', config('services.midtrans.server_key'));

        // Guard 1: server key WAJIB dikonfigurasi.
        if (empty($serverKey)) {
            Log::critical('Payment webhook rejected: MIDTRANS_SERVER_KEY not configured');
            return response()->json([
                'success' => false,
                'message' => 'Payment gateway not configured on server.',
            ], 500);
        }

        // Validasi struktur minimal payload.
        $validated = $request->validate([
            'order_id' => 'required|string',
            'status_code' => 'nullable|string',
            'gross_amount' => 'nullable|string',
            'signature_key' => 'required|string',
            'transaction_status' => 'nullable|string',
            'status' => 'nullable|string',
            'transaction_id' => 'nullable|string',
            'payment_type' => 'nullable|string',
            'fraud_status' => 'nullable|string',
        ]);

        $rawOrderId = (string) $validated['order_id'];
        $statusCode = (string) ($validated['status_code'] ?? '');
        $grossAmount = (string) ($validated['gross_amount'] ?? '');
        $incomingSignature = (string) $validated['signature_key'];

        // Guard 2: verifikasi signature.
        $expectedSignature = hash('sha512', $rawOrderId . $statusCode . $grossAmount . $serverKey);
        if (!hash_equals($expectedSignature, $incomingSignature)) {
            Log::warning('Payment webhook signature mismatch', [
                'order_id' => $rawOrderId,
                'ip' => $request->ip(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Invalid signature key.',
            ], 403);
        }

        // Deteksi event id (untuk idempotency). Prioritas: transaction_id -> signature.
        $providerTxId = $validated['transaction_id']
            ?? $incomingSignature; // fallback: signature unik per (order,status,amount).
        $provider = 'midtrans';

        // Status mentah dari gateway.
        $statusField = $validated['transaction_status'] ?? $validated['status'] ?? null;
        if (!$statusField) {
            return response()->json([
                'success' => false,
                'message' => 'Missing transaction status.',
            ], 422);
        }
        $paymentStatus = strtolower($statusField);

        // Parse order id dari format "INV-YYYYMM-XXXX" atau "ORDER-123".
        $cleanId = preg_replace('/[^0-9]/', '', $rawOrderId);
        $orderIdInt = !empty($cleanId) ? (int) $cleanId : null;

        // Guard 3: order harus ada.
        $order = $orderIdInt ? Order::with('invoice')->find($orderIdInt) : null;
        if (!$order) {
            // Tetap catat webhook untuk forensik.
            $this->recordWebhook($provider, $providerTxId, $paymentStatus, $incomingSignature, $request, null, null, 'ignored', 'Order not found');
            return response()->json([
                'success' => false,
                'message' => 'Order not found.',
            ], 404);
        }

        // Guard 4: amount match.
        // Midtrans mengirim gross_amount seperti "150000.00". Bandingkan sebagai decimal.
        $webhookAmount = (float) $grossAmount;
        $orderAmount = (float) $order->amount;
        if (abs($webhookAmount - $orderAmount) > 0.01) {
            Log::critical('Payment webhook amount mismatch', [
                'order_id' => $order->id,
                'webhook_amount' => $webhookAmount,
                'order_amount' => $orderAmount,
                'ip' => $request->ip(),
            ]);
            $this->recordWebhook($provider, $providerTxId, $paymentStatus, $incomingSignature, $request, $order->id, null, 'failed', 'Amount mismatch');
            return response()->json([
                'success' => false,
                'message' => 'Amount mismatch.',
            ], 422);
        }

        // Idempotency check + processing di dalam transaction.
        try {
            $result = DB::transaction(function () use (
                $provider, $providerTxId, $paymentStatus, $incomingSignature,
                $request, $order, $validated, $grossAmount
            ) {
                // Cek apakah webhook ini sudah pernah diproses (idempotency).
                $existingEvent = WebhookEvent::where('provider', $provider)
                    ->where('event_id', $providerTxId)
                    ->lockForUpdate()
                    ->first();

                if ($existingEvent && $existingEvent->processing_status === 'processed') {
                    return [
                        'status' => 'duplicate',
                        'message' => 'Webhook already processed (idempotent).',
                        'order_status' => $order->status,
                    ];
                }

                // Buat / update webhook_events (state: received).
                $event = $existingEvent ?: WebhookEvent::create([
                    'provider' => $provider,
                    'event_id' => $providerTxId,
                    'event_type' => $paymentStatus,
                    'signature' => $incomingSignature,
                    'ip_address' => $request->ip(),
                    'payload' => $request->all(),
                    'processing_status' => 'received',
                    'order_id' => $order->id,
                ]);

                // Cari transaksi payment yang sudah ada (untuk idempotency di layer transaction).
                $tx = PaymentTransaction::where('provider', $provider)
                    ->where('provider_transaction_id', $providerTxId)
                    ->lockForUpdate()
                    ->first();

                if (!$tx) {
                    $tx = PaymentTransaction::create([
                        'order_id' => $order->id,
                        'invoice_id' => $order->invoice?->id,
                        'provider' => $provider,
                        'provider_transaction_id' => $providerTxId,
                        'provider_order_ref' => $order->id,
                        'payment_method' => $validated['payment_type'] ?? $order->payment_method,
                        'amount' => $grossAmount,
                        'currency' => 'IDR',
                        'status' => 'pending',
                        'fraud_status' => $validated['fraud_status'] ?? null,
                        'signature_key' => $incomingSignature,
                        'raw_payload' => $request->all(),
                    ]);
                }

                // Fraud challenge → tidak transisi, tunggu review manual.
                $fraudStatus = $validated['fraud_status'] ?? null;
                if ($fraudStatus === 'challenge') {
                    $tx->update(['fraud_status' => 'challenge']);
                    $event->update([
                        'processing_status' => 'ignored',
                        'processing_error' => 'Fraud challenge - awaiting manual review',
                        'processed_at' => now(),
                        'payment_transaction_id' => $tx->id,
                    ]);
                    return [
                        'status' => 'challenge',
                        'message' => 'Payment on fraud challenge, awaiting manual review.',
                        'order_status' => $order->status,
                    ];
                }

                // Route berdasarkan payment status.
                if (in_array($paymentStatus, ['settlement', 'capture', 'paid'], true)) {
                    $this->handleSettlement($order, $tx, $event, $validated);
                    return [
                        'status' => 'settled',
                        'message' => 'Payment recorded. Order transitioned to paid.',
                        'order_status' => 'paid',
                    ];
                }

                if (in_array($paymentStatus, ['cancel', 'deny', 'expire', 'failure'], true)) {
                    $this->handleFailure($order, $tx, $event, $paymentStatus);
                    return [
                        'status' => 'cancelled',
                        'message' => 'Payment cancelled/denied recorded.',
                        'order_status' => 'cancelled',
                    ];
                }

                // Status pending/authorize/lainnya → hanya update tx.
                $tx->update([
                    'status' => $this->mapProviderStatus($paymentStatus),
                    'raw_payload' => $request->all(),
                ]);
                $event->update([
                    'processing_status' => 'processed',
                    'processed_at' => now(),
                    'payment_transaction_id' => $tx->id,
                ]);

                return [
                    'status' => 'pending',
                    'message' => 'Payment status pending recorded.',
                    'order_status' => $order->status,
                ];
            });
        } catch (InvalidStateTransitionException $e) {
            // Order sudah bukan di status yang bisa transisi (misal sudah cancelled).
            // Ini bukan error webhook — catat & return 200 supaya gateway tidak retry.
            Log::warning('Payment webhook state transition rejected', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
            $this->recordWebhook($provider, $providerTxId, $paymentStatus, $incomingSignature, $request, $order->id, null, 'ignored', $e->getMessage());
            return response()->json([
                'success' => true,
                'message' => 'Webhook received but state transition ignored: ' . $e->getMessage(),
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Payment webhook processing failed', [
                'order_id' => $order->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->recordWebhook($provider, $providerTxId, $paymentStatus, $incomingSignature, $request, $order->id ?? null, null, 'failed', $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Webhook processing error.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => $result['message'],
            'order_id' => $order->id,
            'order_status' => $result['order_status'],
        ], 200);
    }

    /**
     * Sukses payment: transisi order pending → paid, tandai invoice paid.
     */
    protected function handleSettlement(Order $order, PaymentTransaction $tx, WebhookEvent $event, array $validated): void
    {
        $this->orderStateMachine->transition(
            $order,
            'paid',
            [
                'reason' => 'Payment gateway webhook settlement received.',
                'actor_type' => 'webhook',
                'metadata' => [
                    'provider' => $tx->provider,
                    'provider_tx_id' => $tx->provider_transaction_id,
                    'payment_method' => $validated['payment_type'] ?? null,
                ],
                'onLocked' => function (Order $locked) use ($tx, $validated) {
                    $locked->paid_at = now();
                    $locked->payment_method = $validated['payment_type'] ?? $locked->payment_method;
                    $locked->save();

                    if ($locked->invoice && $locked->invoice->status !== 'paid') {
                        $locked->invoice->update([
                            'status' => 'paid',
                            'paid_at' => now(),
                            'paid_via_transaction_id' => $tx->id,
                        ]);
                    }
                },
                'allowSame' => false,
            ]
        );

        $tx->update([
            'status' => 'settled',
            'settled_at' => now(),
            'fraud_status' => $validated['fraud_status'] ?? $tx->fraud_status,
        ]);

        $event->update([
            'processing_status' => 'processed',
            'processed_at' => now(),
            'payment_transaction_id' => $tx->id,
        ]);

        // Kirim email pembayaran diterima ke customer
        try {
            $order->loadMissing(['customer', 'vpsSpec', 'invoice']);
            $customer = $order->customer;
            if ($customer) {
                $customer->notify(new \App\Notifications\PaymentReceivedNotification($order, $order->invoice));
            }
        } catch (\Throwable $e) {
            Log::error('payment.notify_customer_failed', ['order_id' => $order->id, 'error' => $e->getMessage()]);
        }

        // Kirim notifikasi pesanan baru lunas ke admin
        try {
            $adminEmail = env('VEXAHOST_ADMIN_EMAIL', config('mail.from.address'));
            if ($adminEmail) {
                \Illuminate\Support\Facades\Notification::route('mail', $adminEmail)
                    ->notify(new \App\Notifications\AdminNewPaidOrderNotification($order));
            }
        } catch (\Throwable $e) {
            Log::error('payment.notify_admin_failed', ['order_id' => $order->id, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Failure/cancel/deny/expire: transisi ke cancelled + tandai invoice cancelled.
     */
    protected function handleFailure(Order $order, PaymentTransaction $tx, WebhookEvent $event, string $paymentStatus): void
    {
        // Hanya transisi kalau order masih pending. Kalau sudah paid, ignore.
        if ($order->status === 'pending') {
            $this->orderStateMachine->transition(
                $order,
                'cancelled',
                [
                    'reason' => "Payment {$paymentStatus} from gateway webhook.",
                    'actor_type' => 'webhook',
                    'metadata' => [
                        'gateway_status' => $paymentStatus,
                        'provider_tx_id' => $tx->provider_transaction_id,
                    ],
                    'onLocked' => function (Order $locked) {
                        if ($locked->invoice && $locked->invoice->status !== 'cancelled') {
                            $locked->invoice->update(['status' => 'cancelled']);
                        }
                    },
                ]
            );
        }

        $tx->update([
            'status' => in_array($paymentStatus, ['expire'], true) ? 'expired' : 'failed',
            'failed_at' => now(),
            'failure_reason' => $paymentStatus,
        ]);

        $event->update([
            'processing_status' => 'processed',
            'processed_at' => now(),
            'payment_transaction_id' => $tx->id,
        ]);
    }

    /**
     * Helper: catat webhook forensik walau gagal (untuk debugging & audit).
     * Best-effort — tidak throw kalau unique key konflik.
     */
    protected function recordWebhook(
        string $provider,
        string $eventId,
        string $eventType,
        string $signature,
        Request $request,
        ?int $orderId,
        ?int $paymentTxId,
        string $status,
        ?string $error
    ): void {
        try {
            WebhookEvent::updateOrCreate(
                ['provider' => $provider, 'event_id' => $eventId],
                [
                    'event_type' => $eventType,
                    'signature' => $signature,
                    'ip_address' => $request->ip(),
                    'payload' => $request->all(),
                    'processing_status' => $status,
                    'processing_error' => $error,
                    'processed_at' => now(),
                    'order_id' => $orderId,
                    'payment_transaction_id' => $paymentTxId,
                ]
            );
        } catch (\Throwable $e) {
            Log::error('Failed to record webhook event', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Map raw provider status → internal PaymentTransaction status.
     */
    protected function mapProviderStatus(string $providerStatus): string
    {
        return match ($providerStatus) {
            'settlement', 'capture', 'paid' => 'settled',
            'authorize' => 'authorized',
            'pending' => 'pending',
            'cancel', 'deny', 'failure' => 'failed',
            'expire' => 'expired',
            'refund', 'partial_refund' => 'refunded',
            default => 'pending',
        };
    }
}
