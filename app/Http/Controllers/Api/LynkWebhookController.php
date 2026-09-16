<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\InvalidStateTransitionException;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Models\User;
use App\Models\VpsSpec;
use App\Models\WebhookEvent;
use App\Notifications\PaymentReceivedNotification;
use App\Services\OrderStateMachine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LynkWebhookController extends Controller
{
    public function __construct(protected OrderStateMachine $orderStateMachine)
    {
    }

    /**
     * Handle incoming webhook from Lynk.id
     * Spesifikasi: https://documenter.getpostman.com/view/43601478/2sBXc8o3kn
     */
    public function handle(Request $request)
    {
        // 1. Handle HTTP GET / HEAD ping check
        if ($request->isMethod('get') || $request->isMethod('head')) {
            return response()->json([
                'success' => true,
                'message' => 'Lynk webhook endpoint is active.',
            ], 200);
        }

        $payload = $request->all();
        $event = strtolower(trim((string) ($payload['event'] ?? $payload['action'] ?? $payload['type'] ?? '')));

        // 2. Handle Test Ping / Test Event dari Dashboard Lynk.id
        if (
            empty($payload) ||
            in_array($event, ['test_event', 'ping', 'test', 'test_webhook', 'test_notification', 'webhook.test', 'webhook_test'], true) ||
            str_contains($event, 'test') ||
            str_contains($event, 'ping') ||
            $request->query('test') == '1' ||
            (($request->header('User-Agent') && str_contains(strtolower($request->header('User-Agent')), 'lynk')) && empty($payload['data']))
        ) {
            Log::info('Lynk test webhook received successfully', [
                'ip' => $request->ip(),
                'payload' => $payload,
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Lynk test webhook received successfully.',
            ], 200);
        }

        $merchantKey = config('services.lynk.merchant_key');

        // Guard 1: Merchant key harus terkonfigurasi di server
        if (empty($merchantKey)) {
            Log::critical('Lynk webhook rejected: LYNK_MERCHANT_KEY not configured in .env');
            return response()->json([
                'success' => false,
                'message' => 'Lynk payment gateway not configured on server.',
            ], 500);
        }

        // Ekstraksi header signature
        $receivedSignature = (string) ($request->header('X-Lynk-Signature')
            ?? $request->header('x-lynk-signature')
            ?? '');

        if (empty($receivedSignature)) {
            Log::warning('Lynk webhook missing X-Lynk-Signature header', [
                'ip' => $request->ip(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Missing X-Lynk-Signature header.',
            ], 401);
        }

        $payload = $request->all();
        $event = $payload['event'] ?? '';
        $data = $payload['data'] ?? [];
        $messageAction = strtoupper((string) ($data['message_action'] ?? ''));
        $messageData = $data['message_data'] ?? [];

        $refId = (string) ($messageData['refId'] ?? '');
        $grandTotal = (string) ($messageData['totals']['grandTotal'] ?? '');
        $messageId = (string) ($data['message_id'] ?? '');

        // Guard 2: Validasi signature
        // Rumus: sha256(amount + ref_id + message_id + secret_key)
        $signatureString = $grandTotal . $refId . $messageId . $merchantKey;
        $expectedSignature = hash('sha256', $signatureString);

        if (!hash_equals($expectedSignature, $receivedSignature)) {
            Log::warning('Lynk webhook signature mismatch', [
                'refId' => $refId,
                'messageId' => $messageId,
                'grandTotal' => $grandTotal,
                'ip' => $request->ip(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Invalid signature.',
            ], 403);
        }

        // Guard 3: Idempotency check (Cegah proses ulang refId yang sama)
        $existingEvent = WebhookEvent::where('provider', 'lynk')
            ->where('event_id', $refId)
            ->first();

        if ($existingEvent && $existingEvent->processing_status === 'processed') {
            Log::info("Lynk webhook already processed (Idempotent skip): refId={$refId}");
            return response()->json([
                'success' => true,
                'message' => 'Event already processed.',
                'order_id' => $existingEvent->order_id,
            ], 200);
        }

        // Hanya proses jika event payment.received dan action SUCCESS
        if ($event !== 'payment.received' || $messageAction !== 'SUCCESS') {
            Log::info("Lynk webhook event ignored: event={$event}, action={$messageAction}");
            return response()->json([
                'success' => true,
                'message' => 'Event ignored.',
            ], 200);
        }

        $customer = $messageData['customer'] ?? [];
        $customerEmail = strtolower(trim($customer['email'] ?? ''));
        $customerName = trim($customer['name'] ?? 'Pelanggan Lynk');
        $customerPhone = trim($customer['phone'] ?? '');

        if (empty($customerEmail)) {
            Log::warning('Lynk webhook missing customer email', ['refId' => $refId]);
            return response()->json([
                'success' => false,
                'message' => 'Customer email is required in payload.',
            ], 422);
        }

        $items = $messageData['items'] ?? [];
        $firstItem = $items[0] ?? [];
        $itemTitle = (string) ($firstItem['title'] ?? '');
        $itemPrice = (float) ($firstItem['price'] ?? 0);
        $amountPaid = !empty($grandTotal) ? (float) $grandTotal : $itemPrice;

        // Cari spec VPS/AI/DB yang cocok berdasarkan judul atau harga
        $spec = $this->resolveVpsSpec($itemTitle, $itemPrice, $amountPaid);

        try {
            $result = DB::transaction(function () use (
                $customerEmail,
                $customerName,
                $customerPhone,
                $spec,
                $amountPaid,
                $refId,
                $messageId,
                $receivedSignature,
                $request
            ) {
                // 1. Cari atau buat User
                $user = User::where('email', $customerEmail)->first();
                $isNewUser = false;
                $generatedPassword = null;

                if (!$user) {
                    $isNewUser = true;
                    $generatedUsername = 'vx_' . Str::lower(Str::random(8));
                    while (User::where('username', $generatedUsername)->exists()) {
                        $generatedUsername = 'vx_' . Str::lower(Str::random(8));
                    }
                    $generatedPassword = Str::password(16, true, true, false, false);

                    $user = User::create([
                        'username' => $generatedUsername,
                        'email' => $customerEmail,
                        'password' => Hash::make($generatedPassword),
                        'full_name' => $customerName,
                        'channel' => 'website',
                    ]);
                }

                // Pastikan user memiliki organisasi
                $organization = $user->currentOrganization
                    ?? $user->organizations()->first()
                    ?? $user->createPersonalOrganization();

                $user->switchToOrganization($organization);

                // 2. Cek apakah ada order pending yang cocok, atau buat baru
                $order = Order::where('customer_id', $user->id)
                    ->where('vps_spec_id', $spec->id)
                    ->where('status', 'pending')
                    ->latest()
                    ->lockForUpdate()
                    ->first();

                if (!$order) {
                    $controlPanel = $spec->default_stack ?: 'none';
                    $order = Order::create([
                        'customer_id' => $user->id,
                        'organization_id' => $organization->id,
                        'vps_spec_id' => $spec->id,
                        'control_panel' => $controlPanel,
                        'datacenter_location' => 'indonesia',
                        'os' => 'ubuntu2404',
                        'billing_cycle' => 'monthly',
                        'status' => 'pending',
                        'channel' => 'website',
                        'payment_method' => 'lynk',
                        'amount' => $amountPaid > 0 ? $amountPaid : $spec->sell_price,
                        'currency' => 'IDR',
                        'hostname' => 'vx-' . Str::lower(Str::random(6)) . '.vexahost.cloud',
                    ]);
                }

                // 3. Transisi status Order ke paid lewat OrderStateMachine
                $this->orderStateMachine->transition($order, 'paid', [
                    'reason' => "Pembayaran via Lynk.id berhasil (Ref: {$refId})",
                    'actor_type' => 'webhook',
                    'metadata' => [
                        'provider' => 'lynk',
                        'refId' => $refId,
                        'message_id' => $messageId,
                        'customer_name' => $customerName,
                        'customer_phone' => $customerPhone,
                    ],
                    'onLocked' => function (Order $locked) use ($amountPaid) {
                        $locked->payment_method = 'lynk';
                        $locked->paid_at = now();
                        if ($amountPaid > 0) {
                            $locked->amount = $amountPaid;
                        }
                        $locked->save();
                    },
                ]);

                $order->refresh();

                // 4. Update Invoice yang sudah ada, atau buat baru jika belum dibuat
                $invoice = Invoice::where('order_id', $order->id)->first();
                if ($invoice) {
                    $invoice->update([
                        'status' => 'paid',
                        'paid_at' => now(),
                        'amount' => $order->amount,
                    ]);
                } else {
                    $invoice = Invoice::create([
                        'order_id' => $order->id,
                        'organization_id' => $order->organization_id,
                        'invoice_number' => 'INV-LYNK-' . strtoupper(Str::random(8)),
                        'amount' => $order->amount,
                        'status' => 'paid',
                        'issued_at' => now(),
                        'due_at' => now(),
                        'paid_at' => now(),
                    ]);
                }

                // 5. Catat PaymentTransaction
                $tx = PaymentTransaction::create([
                    'order_id' => $order->id,
                    'invoice_id' => $invoice->id,
                    'provider' => 'lynk',
                    'provider_transaction_id' => $refId,
                    'provider_order_ref' => $messageId,
                    'payment_method' => 'lynk',
                    'amount' => $order->amount,
                    'currency' => 'IDR',
                    'status' => 'settled',
                    'signature_key' => $receivedSignature,
                    'raw_payload' => $request->all(),
                    'settled_at' => now(),
                ]);

                // 6. Catat WebhookEvent untuk Idempotency & Forensik
                WebhookEvent::updateOrCreate(
                    [
                        'provider' => 'lynk',
                        'event_id' => $refId,
                    ],
                    [
                        'event_type' => 'payment.received',
                        'signature' => $receivedSignature,
                        'ip_address' => $request->ip(),
                        'payload' => $request->all(),
                        'processing_status' => 'processed',
                        'processed_at' => now(),
                        'order_id' => $order->id,
                        'payment_transaction_id' => $tx->id,
                    ]
                );

                // 7. Notifikasi pembayaran
                try {
                    $user->notify(new PaymentReceivedNotification($order, $invoice));
                } catch (\Throwable $notifEx) {
                    Log::error("Failed to send payment notification for Order #{$order->id}: " . $notifEx->getMessage());
                }

                return [
                    'order' => $order,
                    'is_new_user' => $isNewUser,
                ];
            });

            Log::info("Lynk webhook successfully processed for Order #{$result['order']->id} (Ref: {$refId})");

            return response()->json([
                'success' => true,
                'message' => 'Lynk payment processed successfully.',
                'order_id' => $result['order']->id,
                'is_new_user' => $result['is_new_user'],
            ], 200);

        } catch (InvalidStateTransitionException $stateEx) {
            Log::warning("Lynk webhook state transition error: " . $stateEx->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'State transition error: ' . $stateEx->getMessage(),
            ], 409);
        } catch (\Throwable $e) {
            Log::error("Lynk webhook unexpected error: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Internal server error processing webhook: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Helper untuk memetakan nama item atau harga dari Lynk ke VpsSpec VexaHost
     */
    protected function resolveVpsSpec(string $itemTitle, float $itemPrice, float $grandTotal): VpsSpec
    {
        $normalizedTitle = strtolower(trim($itemTitle));

        // 1. Coba pencarian berdasarkan nama paket spesifik
        $specs = VpsSpec::where('is_active', true)->get();

        foreach ($specs as $spec) {
            if (!empty($spec->name) && str_contains($normalizedTitle, strtolower($spec->name))) {
                return $spec;
            }
        }

        // 2. Pemetaan keyword umum
        $keywords = [
            'terminal' => 7,
            'workstation' => 8,
            'hermes' => 9,
            'rag' => 10,
            'private ai' => 10,
            'db micro' => 11,
            'db standard' => 12,
            'db enterprise' => 13,
            'vector' => 13,
            'student' => 1,
            'mahasiswa' => 6,
            'standard' => 2,
            'premium' => 3,
            'startup' => 4,
            'business' => 5,
        ];

        foreach ($keywords as $key => $specId) {
            if (str_contains($normalizedTitle, $key)) {
                $found = VpsSpec::find($specId);
                if ($found) return $found;
            }
        }

        // 3. Pencarian berdasarkan harga yang cocok
        $priceMatch = VpsSpec::where('is_active', true)
            ->where(function ($q) use ($itemPrice, $grandTotal) {
                $q->where('sell_price', $itemPrice)
                  ->orWhere('sell_price', $grandTotal);
            })
            ->first();

        if ($priceMatch) {
            return $priceMatch;
        }

        // 4. Default: Standard Spec (ID: 2) atau spec pertama
        return VpsSpec::find(2) ?? VpsSpec::first();
    }
}
