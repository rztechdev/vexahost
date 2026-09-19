<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\PaymentGateway;
use App\Models\PaymentTransaction;
use App\Models\TermsAcceptance;
use App\Models\User;
use App\Models\VpsSpec;
use App\Models\WebhookEvent;
use App\Services\OrderStateMachine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public function __construct(protected OrderStateMachine $orderStateMachine)
    {
    }

    public function checkout(Request $request, $spec_id = null)
    {
        // Harga modal disembunyikan: halaman ini mengirim data paket ke browser
        // lewat Js::from(), sehingga seluruh atribut dapat dibaca dari view-source.
        // Tidak disembunyikan di level model karena form edit paket admin membutuhkannya.
        $specs = VpsSpec::where('is_active', true)->orderBy('sell_price', 'asc')->get()
            ->each->makeHidden(['cost_price']);
        $selectedSpecId = $spec_id ?? $request->query('spec_id', $specs->first()->id ?? 1);
        $selectedSpec = $specs->firstWhere('id', $selectedSpecId) ?? $specs->first();

        return view('order.checkout', compact('specs', 'selectedSpec'));
    }

    public function quickLogin(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $login = trim($request->input('login'));
        $user = str_contains($login, '@')
            ? User::where('email', $login)->first()
            : User::where('username', $login)->first();

        if (!$user || !Hash::check($request->input('password'), $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email/username atau password tidak sesuai.',
            ], 422);
        }

        if ($user->hasTwoFactorEnabled()) {
            $request->session()->put('2fa.pending_user_id', $user->id);
            $request->session()->put('2fa.remember', true);
            return response()->json([
                'success' => false,
                'requires_2fa' => true,
                'redirect' => route('two-factor.challenge'),
                'message' => 'Autentikasi dua faktor diperlukan.',
            ], 200);
        }

        Auth::login($user, true);
        $request->session()->regenerate();
        $request->session()->put('2fa.passed', true);

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'full_name' => $user->full_name,
                'email' => $user->email,
                'phone' => $user->phone ?? '',
            ],
            'csrf_token' => csrf_token(),
            'message' => 'Login berhasil.',
        ]);
    }

    public function store(Request $request)
    {
        $specId = $request->input('vps_spec_id');
        $spec = VpsSpec::find($specId);
        $isAiPackage = $spec && $spec->isAiPackage();
        $isDbPackage = $spec && $spec->isDatabasePackage();

        if ($isAiPackage) {
            $cleanName = Str::slug($spec->name);
            $randomSuffix = Str::lower(Str::random(4));
            $request->merge([
                'hostname' => $request->input('hostname') ?: ('vx-ai-' . substr($cleanName, 0, 16) . '-' . $randomSuffix),
                'control_panel' => $request->input('control_panel') ?: ($spec->default_stack ?: 'vscode_server'),
                'provider' => $request->input('provider') ?: $spec->defaultProvider(),
                'datacenter_location' => $request->input('datacenter_location') ?: 'indonesia',
                'os' => $request->input('os') ?: 'ubuntu2404',
                'billing_cycle' => $request->input('billing_cycle') ?: 'monthly',
            ]);
        } elseif ($isDbPackage) {
            $cleanName = Str::slug($spec->name);
            $randomSuffix = Str::lower(Str::random(4));
            $request->merge([
                'hostname' => $request->input('hostname') ?: ('vx-db-' . substr($cleanName, 0, 16) . '-' . $randomSuffix),
                'control_panel' => $request->input('control_panel') ?: ($spec->default_stack ?: 'managed_database'),
                'provider' => $request->input('provider') ?: $spec->defaultProvider(),
                'datacenter_location' => $request->input('datacenter_location') ?: 'indonesia',
                'os' => $request->input('os') ?: 'ubuntu2404',
                'billing_cycle' => $request->input('billing_cycle') ?: 'monthly',
                'db_engine' => $request->input('db_engine') ?: 'postgres',
                'db_manager' => $request->input('db_manager') ?: 'cloudbeaver',
            ]);
        }

        $validated = $request->validate([
            'vps_spec_id' => 'required|exists:vps_specs,id',
            'control_panel' => 'required|in:none,coolify,dokploy,aapanel,cloudpanel,docker,cyberpanel,hestiacp,hermes_agent,openclaw,omniroute,9router,agent_zero,n8n,ollama,anythingllm,librechat,vscode_server,gitea_forgejo,uptime_kuma,netdata_beszel,wordpress,ghost,strapi_directus,prestashop_bagisto,claude_opencode,dify_ollama,managed_database',
            'provider' => 'required|in:tencent,cloudeka',
            'datacenter_location' => 'required|in:singapore,indonesia',
            'os' => 'required|string',
            'billing_cycle' => 'nullable|string|in:monthly',
            'hostname' => ['required', 'string', 'max:63', 'regex:/^[A-Za-z0-9][A-Za-z0-9-]*$/'],
            'root_password' => 'required|string|min:8|max:255',
            'payment_method' => 'required|string|in:midtrans_snap,qris,lynk,bca_va,mandiri_va,bni_va,bri_va,cimb_va,permata_va,gopay,shopeepay,ovo,dana',
            'db_engine' => 'nullable|string|in:postgres,mysql,redis,mongodb,vector',
            'db_manager' => 'nullable|string|in:cloudbeaver,cli_only',
            // Guest fields if not logged in
            'full_name' => 'nullable|string|max:255',
            'username' => 'nullable|string|min:3|max:50|alpha_dash|unique:users,username',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:25',
            'password' => 'nullable|string|min:8',
            'redirect_to_dashboard' => 'nullable',
            // PHASE 2 - persetujuan ketentuan bersifat WAJIB (clickwrap).
            // Divalidasi di sisi server agar tidak dapat dilewati dari peramban.
            'terms_accepted' => 'accepted',
        ], [
            'terms_accepted.accepted' => 'Anda wajib menyetujui Ketentuan Layanan dan Ketentuan Penggunaan sebelum melanjutkan pemesanan.',
        ]);

        // PHASE 4 - metode pembayaran wajib milik gateway yang aktif. UI checkout
        // sudah mengunci metode nonaktif, pemeriksaan ini menutup jalur kirim langsung.
        if (!PaymentGateway::isMethodActive($validated['payment_method'])) {
            $methodMessage = 'Metode pembayaran ini sedang tidak tersedia. Silakan pilih metode lain.';
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $methodMessage,
                    'errors' => ['payment_method' => [$methodMessage]],
                ], 422);
            }
            return back()->withInput()->withErrors(['payment_method' => $methodMessage]);
        }

        $spec = VpsSpec::findOrFail($validated['vps_spec_id']);
        if (!$spec->isProviderAllowed($validated['provider'])) {
            $msg = in_array('cloudeka', $spec->allowedProviders(), true)
                ? "Paket {$spec->name} hanya tersedia di provider Cloudeka by Lintasarta (Datacenter Indonesia)."
                : "Paket {$spec->name} hanya tersedia di provider Tencent Cloud.";
            return back()->withInput()->withErrors([
                'provider' => $msg,
            ]);
        }

        $providerRegions = ['tencent' => ['indonesia', 'singapore'], 'cloudeka' => ['indonesia']];
        $providerOs = Order::operatingSystems()[$validated['provider']] ?? [];
        validator($validated, [
            'datacenter_location' => [Rule::in($providerRegions[$validated['provider']])],
            'os' => [Rule::in(array_keys($providerOs))],
        ])->validate();

        $user = Auth::user();

        // If not logged in, register or authenticate
        if (!$user) {
            $request->validate([
                'full_name' => 'required|string|max:255',
                'username' => 'nullable|string|min:3|max:50|alpha_dash|unique:users,username',
                'email' => 'required|email|max:255',
                'phone' => 'nullable|string|max:25',
                'password' => 'required|string|min:8',
            ]);

            $existingUser = User::where('email', $validated['email'])->first();
            if ($existingUser) {
                if (!Hash::check($validated['password'], $existingUser->password)) {
                    if ($request->expectsJson()) {
                        return response()->json([
                            'message' => 'Email ini sudah terdaftar. Silakan pilih tab "Sudah Punya Akun" untuk login atau masukkan password yang sesuai.',
                            'errors' => [
                                'email' => ['Email ini sudah terdaftar. Silakan login atau gunakan password yang sesuai.']
                            ]
                        ], 422);
                    }
                    return back()->withInput()->withErrors([
                        'email' => 'Email ini sudah terdaftar. Silakan login terlebih dahulu atau gunakan password yang benar.',
                    ]);
                }
                $user = $existingUser;
            } else {
                $requestedUsername = $request->input('username');
                if (!empty($requestedUsername)) {
                    $generatedUsername = strtolower(trim($requestedUsername));
                } else {
                    $generatedUsername = 'vx_' . Str::lower(Str::random(7));
                    while (User::where('username', $generatedUsername)->exists()) {
                        $generatedUsername = 'vx_' . Str::lower(Str::random(7));
                    }
                }

                $user = User::create([
                    'full_name' => $validated['full_name'],
                    'username' => $generatedUsername,
                    'email' => strtolower($validated['email']),
                    'phone' => $validated['phone'] ?? null,
                    'password' => Hash::make($validated['password']),
                    'channel' => 'website',
                ]);
            }

            Auth::login($user);
            $request->session()->regenerate();
            $request->session()->put('2fa.passed', true);
        } else {
            // Update phone if previously empty
            if (!empty($validated['phone']) && empty($user->phone)) {
                $user->update(['phone' => $validated['phone']]);
            }
        }

        // Checkout is public (guest route), so ensure a tenant context exists
        // before creating any billable resource.
        $organization = $user->currentOrganization;
        if (!$organization) {
            $organization = $user->organizations()->first() ?? $user->createPersonalOrganization();
            $user->switchToOrganization($organization);
        }

        if ($organization->quota && !$organization->quota->canAddVps()) {
            return back()->withInput()->with('error', 'Kuota VPS organisasi sudah penuh. Upgrade kuota atau pilih organisasi lain.');
        }

        $spec = VpsSpec::findOrFail($validated['vps_spec_id']);

        $cycle = 'monthly';
        $netAmount = (float) $spec->sell_price;
        $setupFee = 0;

        // PHASE 2 - bukti persetujuan dicatat bersama order dalam satu transaksi.
        $termsVersion = config('legal.terms_version');
        $termsIp = $request->ip();
        $termsAgent = $request->userAgent();

        // Bungkus dalam transaction agar order + invoice + status history atomic.
        $order = DB::transaction(function () use (
            $user, $organization, $spec, $validated, $cycle, $netAmount, $setupFee,
            $termsVersion, $termsIp, $termsAgent
        ) {
            $order = Order::create([
                'customer_id' => $user->id,
                'organization_id' => $organization->id,
                'vps_spec_id' => $spec->id,
                'control_panel' => $validated['control_panel'],
                'db_engine' => $spec->isDatabasePackage() ? ($validated['db_engine'] ?? 'postgres') : null,
                'db_manager' => $spec->isDatabasePackage() ? ($validated['db_manager'] ?? 'cloudbeaver') : null,
                'db_name' => $spec->isDatabasePackage() ? 'vexadb_production' : null,
                'db_user' => $spec->isDatabasePackage() ? 'admin_vexa' : null,
                'db_password' => $spec->isDatabasePackage() ? ($validated['root_password'] ?? Str::password(16, true, true, false, false)) : null,
                'db_port' => $spec->isDatabasePackage() ? match($validated['db_engine'] ?? 'postgres') {
                    'mysql' => 3306,
                    'redis' => 6379,
                    'mongodb' => 27017,
                    'vector' => 6333,
                    default => 5432,
                } : null,
                'provider' => $validated['provider'],
                'hostname' => strtolower($validated['hostname']),
                'root_password' => $validated['root_password'],
                'datacenter_location' => $validated['datacenter_location'],
                'os' => $validated['os'],
                'billing_cycle' => $cycle,
                'status' => 'pending',
                'channel' => 'website',
                'payment_method' => $validated['payment_method'],
                'amount' => $netAmount,
                'setup_fee' => $setupFee,
                'paid_at' => null,
                'starts_at' => null,
                'expires_at' => null,
                'last_status_change_at' => now(),
                'terms_version' => $termsVersion,
            ]);

            // Baris ini adalah alat bukti. Tanpa versi yang tersimpan, kita tidak
            // dapat membuktikan naskah mana yang disetujui bila isinya berubah.
            TermsAcceptance::create([
                'user_id' => $user->id,
                'order_id' => $order->id,
                'terms_version' => $termsVersion,
                'accepted_at' => now(),
                'ip_address' => $termsIp,
                'user_agent' => $termsAgent,
            ]);

            $invoiceNumber = 'INV-' . date('Ym') . '-' . str_pad($order->id, 4, '0', STR_PAD_LEFT);
            Invoice::create([
                'order_id' => $order->id,
                'organization_id' => $organization->id,
                'invoice_number' => $invoiceNumber,
                'amount' => $netAmount,
                'status' => 'sent',
                'issued_at' => now(),
                'due_at' => now()->addDays(1),
                'paid_at' => null,
            ]);

            // Catat entry awal di history (from_status = null berarti "created").
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'from_status' => null,
                'to_status' => 'pending',
                'reason' => 'Order dibuat, menunggu pembayaran.',
                'actor_user_id' => $user->id,
                'actor_type' => 'customer',
                'ip_address' => request()->ip(),
                'metadata' => [
                    'amount' => $netAmount,
                    'payment_method' => $validated['payment_method'],
                    'billing_cycle' => $cycle,
                ],
                'created_at' => now(),
            ]);

            return $order;
        });

        // Simpan ID order aktif di session browser agar bisa dipulihkan saat kembali dari Lynk/gateway
        session(['last_order_id' => $order->id]);

        // VPS Instance TIDAK dibuat di sini.
        // Instance hanya dibuat oleh Admin setelah payment terkonfirmasi via webhook.

        $redirectUrl = ($order->payment_method === 'lynk' && !empty($spec->payment_url))
            ? $spec->payment_url
            : route('order.payment', $order->id);

        // If user explicitly chose to postpone payment and view dashboard (locked state)
        if ($request->filled('redirect_to_dashboard') && $request->input('redirect_to_dashboard') == '1') {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'order_id' => $order->id,
                    'redirect_url' => route('dashboard.index'),
                ]);
            }

            return redirect()->route('dashboard.index')
                ->with('warning', 'Pesanan VPS Anda telah tersimpan! Layanan saat ini TERKUNCI menunggu pembayaran. Silakan selesaikan pembayaran untuk mengaktifkan.');
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'order_id' => $order->id,
                'redirect_url' => $redirectUrl,
            ]);
        }

        // Jika metode pembayaran Lynk dan paket memiliki payment_url, langsung alihkan ke Lynk checkout
        if ($order->payment_method === 'lynk' && !empty($spec->payment_url)) {
            return redirect()->away($spec->payment_url);
        }

        return redirect()->route('order.payment', $order->id)
            ->with('info', 'Pesanan berhasil dibuat. Silakan selesaikan pembayaran sesuai metode yang Anda pilih.');
    }

    public function payment($id)
    {
        $order = Order::with(['customer', 'vpsSpec', 'invoice'])->findOrFail($id);

        if (!Auth::check() || ($order->customer_id !== Auth::id() && !Auth::user()->is_admin)) {
            abort(403);
        }

        // If order already paid, redirect directly to success callback view
        if ($order->paid_at) {
            return redirect()->route('order.success', $order->id);
        }

        $paymentMethodNames = [
            'lynk' => 'Lynk.id Checkout',
            'qris' => 'QRIS',
            'bca_va' => 'BCA Virtual Account',
            'mandiri_va' => 'Mandiri Virtual Account',
            'bni_va' => 'BNI Virtual Account',
            'bri_va' => 'BRI Virtual Account',
            'gopay' => 'GoPay',
            'ovo' => 'OVO',
            'dana' => 'DANA',
            'shopeepay' => 'ShopeePay',
            'midtrans_snap' => 'Kartu Kredit/Debit',
        ];

        $devSimulateEnabled = config('app.env') === 'local' && (bool) env('APP_DEV_SIMULATE_PAYMENT', false);

        // Generate dynamic QRIS khusus nominal pesanan ini
        $qrisService = app(\App\Services\QrisService::class);
        $qrisDataUri = $qrisService->generateDataUri((float) $order->amount);
        $qrisPayload = $qrisService->generatePayload((float) $order->amount);
        $elapsedSeconds = (int) max(0, now()->diffInSeconds($order->created_at));

        return view('order.payment-gateway', compact('order', 'devSimulateEnabled', 'qrisDataUri', 'qrisPayload', 'elapsedSeconds'));
    }

    /**
     * Halaman "menunggu konfirmasi pembayaran" — read-only.
     * Frontend polling paymentStatusJson secara berkala; ketika paid_at terisi
     * (oleh webhook) baru redirect ke success page.
     */
    public function paymentStatus($id)
    {
        $order = Order::with(['customer', 'vpsSpec', 'invoice'])->findOrFail($id);

        if (!Auth::check() && session('last_order_id') == $order->id && $order->customer_id) {
            Auth::loginUsingId($order->customer_id);
        }

        if (!Auth::check() || ($order->customer_id !== Auth::id() && !Auth::user()->is_admin)) {
            abort(403);
        }

        if ($order->paid_at) {
            return redirect()->route('order.success', $order->id);
        }

        return view('order.payment-pending', compact('order'));
    }

    /**
     * JSON endpoint untuk polling status.
     * Tidak pernah mengubah status — hanya membaca.
     */
    public function paymentStatusJson($id)
    {
        $order = Order::with('invoice')->findOrFail($id);

        if (!Auth::check() && session('last_order_id') == $order->id && $order->customer_id) {
            Auth::loginUsingId($order->customer_id);
        }

        if (!Auth::check() || ($order->customer_id !== Auth::id() && !Auth::user()->is_admin)) {
            abort(403);
        }

        return response()->json([
            'order_id' => $order->id,
            'status' => $order->status,
            'paid_at' => optional($order->paid_at)?->toIso8601String(),
            'invoice_status' => $order->invoice?->status,
            'redirect_to' => $order->paid_at ? route('order.success', $order->id) : null,
        ]);
    }

    /**
     * DEV ONLY: simulasi payment untuk testing lokal tanpa gateway asli.
     * Ini menggantikan lubang keamanan `completePayment` yang lama.
     *
     * Endpoint ini:
     *   1. Hanya aktif di APP_ENV=local + env APP_DEV_SIMULATE_PAYMENT=true.
     *   2. Hanya bisa dipanggil oleh admin ATAU pemilik order.
     *   3. Rate-limited (5/min).
     *   4. Membuat payment_transactions record + transisi order via state machine.
     *   5. Ditandai jelas sebagai "dev simulation" di metadata & log.
     *
     * Di production, endpoint ini otomatis abort(403) karena guard di atas.
     */
    public function devSimulatePayment(Request $request, $id)
    {
        // Guard 1: environment
        if (!app()->environment('local')) {
            abort(404, 'Endpoint ini hanya tersedia di environment lokal.');
        }
        // Guard 2: feature flag
        if (!env('APP_DEV_SIMULATE_PAYMENT', false)) {
            abort(404, 'Simulasi pembayaran dinonaktifkan. Set APP_DEV_SIMULATE_PAYMENT=true di .env untuk mengaktifkan.');
        }

        $order = Order::with('invoice')->findOrFail($id);

        // Guard 3: hanya pemilik order atau admin
        if (!Auth::check() || ($order->customer_id !== Auth::id() && !Auth::user()->is_admin)) {
            abort(403);
        }

        if ($order->paid_at) {
            return redirect()->route('order.success', $order->id)
                ->with('info', 'Order ini sudah tercatat lunas sebelumnya.');
        }

        try {
            DB::transaction(function () use ($order) {
                // Catat transaksi simulasi.
                $tx = PaymentTransaction::create([
                    'order_id' => $order->id,
                    'invoice_id' => $order->invoice?->id,
                    'provider' => 'dev_simulator',
                    'provider_transaction_id' => 'DEV-' . Str::upper(Str::random(12)),
                    'provider_order_ref' => 'ORDER-' . $order->id,
                    'payment_method' => $order->payment_method,
                    'amount' => $order->amount,
                    'currency' => 'IDR',
                    'status' => 'settled',
                    'raw_payload' => [
                        'note' => 'Dev simulation, not a real payment.',
                        'triggered_by_user_id' => Auth::id(),
                        'ip' => request()->ip(),
                    ],
                    'settled_at' => now(),
                ]);

                // Transisi order pending → paid via state machine.
                $this->orderStateMachine->transition(
                    $order,
                    'paid',
                    [
                        'reason' => 'Dev simulator marked payment settled.',
                        'actor_type' => 'system',
                        'metadata' => [
                            'source' => 'dev_simulator',
                            'transaction_id' => $tx->id,
                        ],
                        'onLocked' => function (Order $locked) use ($tx) {
                            $locked->paid_at = now();
                            $locked->save();

                            if ($locked->invoice) {
                                $locked->invoice->update([
                                    'status' => 'paid',
                                    'paid_at' => now(),
                                    'paid_via_transaction_id' => $tx->id,
                                ]);
                            }
                        },
                    ]
                );
            });

            Log::warning('order.payment.dev_simulated', [
                'order_id' => $order->id,
                'user_id' => Auth::id(),
                'ip' => $request->ip(),
            ]);

            // Kirim notifikasi email ke pelanggan & admin
            try {
                $order->loadMissing(['customer', 'vpsSpec', 'invoice']);
                if ($order->customer) {
                    $order->customer->notify(new \App\Notifications\PaymentReceivedNotification($order, $order->invoice));
                }
                $adminEmail = env('VEXAHOST_ADMIN_EMAIL', config('mail.from.address'));
                if ($adminEmail) {
                    \Illuminate\Support\Facades\Notification::route('mail', $adminEmail)
                        ->notify(new \App\Notifications\AdminNewPaidOrderNotification($order));
                }
            } catch (\Throwable $e) {
                Log::error('order.payment.dev_simulated_notify_failed', ['order_id' => $order->id, 'error' => $e->getMessage()]);
            }
        } catch (\Throwable $e) {
            Log::error('order.payment.dev_simulate_failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
            return back()->with('error', 'Simulasi pembayaran gagal: ' . $e->getMessage());
        }

        return redirect()->route('order.success', $order->id)
            ->with('success', '[DEV] Pembayaran disimulasi. Order menunggu provisioning oleh admin.');
    }

    public function success($id)
    {
        $order = Order::with(['customer', 'vpsSpec', 'invoice', 'vpsInstance'])->findOrFail($id);

        if (!Auth::check() && session('last_order_id') == $order->id && $order->customer_id) {
            Auth::loginUsingId($order->customer_id);
        }

        if (!Auth::check() || ($order->customer_id !== Auth::id() && !Auth::user()->is_admin)) {
            abort(403);
        }

        return view('order.success', compact('order'));
    }

    /**
     * Callback handler setelah pembayaran dari payment gateway (Lynk, Shopee, dsb).
     */
    public function paymentCallback(Request $request)
    {
        $orderId = $request->query('order_id')
            ?? $request->query('refId')
            ?? $request->query('ref_id')
            ?? $request->query('reference')
            ?? $request->query('transaction_id')
            ?? $request->query('order_number')
            ?? session('last_order_id');

        $user = Auth::user();
        $order = null;

        if ($orderId) {
            // 1. Cek numeric primary key (orders.id)
            if (is_numeric($orderId)) {
                $order = Order::find($orderId);
            }

            // 2. Cek Shopee order ID
            if (!$order) {
                $order = Order::where('shopee_order_id', $orderId)->first();
            }

            // 3. Cek via PaymentTransaction (misal refId Lynk)
            if (!$order) {
                $tx = PaymentTransaction::where('provider_transaction_id', $orderId)
                    ->orWhere('provider_order_ref', $orderId)
                    ->latest()
                    ->first();
                if ($tx && $tx->order_id) {
                    $order = Order::find($tx->order_id);
                }
            }

            // 4. Cek via WebhookEvent (misal event_id Lynk)
            if (!$order) {
                $evt = WebhookEvent::where('event_id', $orderId)
                    ->whereNotNull('order_id')
                    ->latest()
                    ->first();
                if ($evt && $evt->order_id) {
                    $order = Order::find($evt->order_id);
                }
            }
        }

        // 5. Fallback pencarian order terbaru user yang sedang login atau session
        if (!$order && $user) {
            $order = Order::where('customer_id', $user->id)->latest()->first();
        }

        if (!$order && session('last_order_id')) {
            $order = Order::find(session('last_order_id'));
        }

        // 6. Pemulihan Sesi Pengguna secara aman:
        // Jika user belum login di browser ini tapi browser memiliki session order yang cocok,
        // login-kan kembali agar user tidak terlempar ke form login kosong
        if (!$user && $order && $order->customer_id) {
            if (session('last_order_id') == $order->id) {
                Auth::loginUsingId($order->customer_id);
                $user = Auth::user();
            }
        }

        // 7. Penanganan Redirect:
        // A. Pesanan sudah lunas / diproses
        if ($order && in_array($order->status, ['paid', 'provisioning', 'active'], true) && $order->paid_at) {
            if ($user) {
                return redirect()->route('dashboard.index', [
                    'payment_success' => 1,
                    'order_id' => $order->id,
                ])->with('payment_success_order_id', $order->id);
            }

            return redirect()->route('order.success', $order->id);
        }

        // B. Pesanan masih pending (webhook sedang berjalan / menunggu pembayaran)
        if ($order && $order->status === 'pending') {
            return redirect()->route('order.payment.status', $order->id)
                ->with('info', 'Pembayaran sedang diproses atau menunggu verifikasi sistem. Silakan pantau status pembayaran Anda.');
        }

        // C. User terotentikasi tapi order tidak spesifik
        if ($user) {
            return redirect()->route('dashboard.index')
                ->with('info', 'Selamat datang di Dashboard. Pantau status pesanan dan layanan server Anda di sini.');
        }

        return redirect()->route('login')
            ->with('info', 'Silakan masuk ke akun VexaHost Anda untuk memantau status pesanan.');
    }
}
