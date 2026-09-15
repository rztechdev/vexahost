<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidStateTransitionException;
use App\Models\Invoice;
use App\Models\SupportTicket;
use App\Models\Subscription;
use App\Models\TicketMessage;
use App\Models\User;
use App\Models\VpsInstance;
use App\Services\VpsStateMachine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class DashboardController extends Controller
{
    public function __construct(protected VpsStateMachine $vpsStateMachine)
    {
    }

    private function organizationId(): int
    {
        return (int) Auth::user()->current_organization_id;
    }

    public function index()
    {
        $user = Auth::user();
        $organizationId = $this->organizationId();
        $vps = VpsInstance::where('organization_id', $organizationId)->orderBy('created_at', 'desc')->get();
        $invoicesCount = Invoice::where('organization_id', $organizationId)->count();
        $openTicketsCount = SupportTicket::where('organization_id', $organizationId)
            ->whereIn('status', ['open', 'in_progress'])
            ->count();

        // Unpaid orders (Layanan terkunci menunggu pembayaran)
        $unpaidOrders = \App\Models\Order::where('organization_id', $organizationId)
            ->whereNull('paid_at')
            ->with(['vpsSpec', 'invoice'])
            ->latest()
            ->get();

        // Pending orders (sudah bayar tapi belum di-provision)
        $pendingOrdersCount = \App\Models\Order::where('organization_id', $organizationId)
            ->whereNotNull('paid_at')
            ->where('status', 'pending')
            ->count();

        return view('dashboard.index', compact('vps', 'invoicesCount', 'openTicketsCount', 'pendingOrdersCount', 'unpaidOrders'));
    }

    /**
     * Endpoint JSON untuk polling progress provisioning dari dashboard customer.
     * Read-only, tidak mengubah state.
     */
    public function provisioningStatus($id)
    {
        $vps = VpsInstance::where('organization_id', $this->organizationId())
            ->with('latestProvisioningTask')
            ->findOrFail($id);

        $task = $vps->latestProvisioningTask;

        return response()->json([
            'vps_id' => $vps->id,
            'vps_status' => $vps->status,
            'task' => $task ? [
                'id' => $task->id,
                'uuid' => $task->uuid,
                'kind' => $task->kind,
                'status' => $task->status,
                'progress' => (int) $task->progress,
                'current_step' => $task->current_step,
                'attempts' => (int) $task->attempts,
                'error' => $task->error,
                'started_at' => $task->started_at?->toIso8601String(),
                'finished_at' => $task->finished_at?->toIso8601String(),
            ] : null,
        ]);
    }

    public function show($id)
    {
        $vps = VpsInstance::where('organization_id', $this->organizationId())
            ->with(['activityLogs' => function ($query) {
                $query->latest()->take(25);
            }])
            ->findOrFail($id);

        $cpuCores = $vps->cpu ?? 1;
        $ramGb = $vps->ram ?? 1;
        $diskGb = $vps->disk ?? 20;

        $isRunning = ($vps->status === 'running');

        // Resource metrics: Data ini akan tersedia ketika monitoring agent terhubung.
        // Untuk saat ini, tampilkan null jika belum ada data real dari monitoring.
        // Nanti bisa diintegrasikan dengan Prometheus/node_exporter/Uptime Kuma.
        $cpuUsage = null;
        $ramUsedGb = null;
        $ramPercent = null;
        $diskUsedGb = null;
        $diskPercent = null;
        $uptime = $vps->uptime_percent;

        return view('dashboard.show', compact(
            'vps',
            'cpuCores',
            'ramGb',
            'diskGb',
            'cpuUsage',
            'ramUsedGb',
            'ramPercent',
            'diskUsedGb',
            'diskPercent',
            'uptime',
            'isRunning'
        ));
    }

    public function start($id)
    {
        $vps = VpsInstance::where('organization_id', $this->organizationId())->findOrFail($id);

        if ($vps->isExpired()) {
            return back()->with('error', 'Layanan VPS telah kadaluarsa. Silakan perpanjang langganan atau hubungi support.');
        }

        if ($vps->status === 'running') {
            return back()->with('info', 'VPS ' . ($vps->hostname ?? $vps->id) . ' sudah dalam keadaan berjalan (running).');
        }

        try {
            $this->vpsStateMachine->transition($vps, 'running', [
                'reason' => 'Instance dinyalakan (Boot / Power On) oleh pelanggan.',
                'actor_type' => 'customer',
            ]);

            $vps->refresh()->logActivity(
                action: 'start',
                description: 'Instance dinyalakan (Boot / Power On) oleh pengguna',
                status: 'completed'
            );
        } catch (InvalidStateTransitionException $e) {
            return back()->with('error', 'Aksi tidak diizinkan: ' . $e->getMessage());
        }

        return back()->with('success', 'VPS ' . ($vps->hostname ?? $vps->id) . ' berhasil dinyalakan.');
    }

    public function stop($id)
    {
        $vps = VpsInstance::where('organization_id', $this->organizationId())->findOrFail($id);

        if ($vps->status === 'stopped') {
            return back()->with('info', 'VPS ' . ($vps->hostname ?? $vps->id) . ' sudah dalam keadaan mati (stopped).');
        }

        try {
            $this->vpsStateMachine->transition($vps, 'stopped', [
                'reason' => 'Graceful shutdown (ACPI Power Off) oleh pelanggan.',
                'actor_type' => 'customer',
            ]);

            $vps->refresh()->logActivity(
                action: 'stop',
                description: 'Perintah graceful shutdown (ACPI Power Off) dieksekusi oleh pengguna',
                status: 'completed'
            );
        } catch (InvalidStateTransitionException $e) {
            return back()->with('error', 'Aksi tidak diizinkan: ' . $e->getMessage());
        }

        return back()->with('success', 'VPS ' . ($vps->hostname ?? $vps->id) . ' berhasil dimatikan (graceful shutdown).');
    }

    public function reboot($id)
    {
        $vps = VpsInstance::where('organization_id', $this->organizationId())->findOrFail($id);

        if ($vps->isExpired()) {
            return back()->with('error', 'Layanan VPS telah kadaluarsa. Silakan perpanjang langganan.');
        }

        try {
            // rebooting → running (2 langkah dalam 1 transaction agar terlihat di history)
            $this->vpsStateMachine->transition($vps, 'rebooting', [
                'reason' => 'Soft reboot (ACPI) diminta pelanggan.',
                'actor_type' => 'customer',
                'allowSame' => true, // idempotent kalau spam klik
            ]);
            $this->vpsStateMachine->transition($vps->refresh(), 'running', [
                'reason' => 'Reboot selesai.',
                'actor_type' => 'system',
            ]);

            $vps->refresh()->logActivity(
                action: 'reboot',
                description: 'Perintah soft reboot (ACPI reboot) dieksekusi oleh pengguna',
                status: 'completed'
            );
        } catch (InvalidStateTransitionException $e) {
            return back()->with('error', 'Aksi tidak diizinkan: ' . $e->getMessage());
        }

        return back()->with('success', 'Permintaan reboot untuk VPS ' . ($vps->hostname ?? $vps->id) . ' berhasil dieksekusi. Layanan normal kembali dalam 1-2 menit.');
    }

    public function forceReboot($id)
    {
        $vps = VpsInstance::where('organization_id', $this->organizationId())->findOrFail($id);

        if ($vps->isExpired()) {
            return back()->with('error', 'Layanan VPS telah kadaluarsa.');
        }

        try {
            $this->vpsStateMachine->transition($vps, 'rebooting', [
                'reason' => 'Hard reset / Force reboot (Power Cycle).',
                'actor_type' => 'customer',
                'allowSame' => true,
            ]);
            $this->vpsStateMachine->transition($vps->refresh(), 'running', [
                'reason' => 'Force reboot selesai.',
                'actor_type' => 'system',
            ]);

            $vps->refresh()->logActivity(
                action: 'force_reboot',
                description: 'Hard reset / Force reboot (Power Cycle) dipicu oleh pengguna',
                status: 'completed'
            );
        } catch (InvalidStateTransitionException $e) {
            return back()->with('error', 'Aksi tidak diizinkan: ' . $e->getMessage());
        }

        return back()->with('warning', 'Hard reset (force reboot) untuk VPS ' . ($vps->hostname ?? $vps->id) . ' telah dieksekusi.');
    }

    public function reinstall(Request $request, $id)
    {
        $vps = VpsInstance::where('organization_id', $this->organizationId())->findOrFail($id);

        if ($vps->isExpired()) {
            return back()->with('error', 'Layanan VPS telah kadaluarsa.');
        }

        $validated = $request->validate([
            'os' => 'required|string|in:Ubuntu 24.04 LTS,Ubuntu 22.04 LTS,Debian 12',
            'control_panel' => 'required|string|in:none,coolify,dokploy,aapanel,cloudpanel,docker,cyberpanel,hestiacp,hermes_agent,openclaw,omniroute,9router,agent_zero,n8n,ollama,anythingllm,librechat,vscode_server,gitea_forgejo,uptime_kuma,netdata_beszel,wordpress,ghost,strapi_directus,prestashop_bagisto',
        ]);

        $newRootPassword = Str::random(10) . '@' . Str::upper(Str::random(4)) . rand(10, 99);

        try {
            // Transisi: → reinstalling → running (dalam 1 transaction via nested)
            $this->vpsStateMachine->transition($vps, 'reinstalling', [
                'reason' => 'Reinstall OS dan control panel.',
                'actor_type' => 'customer',
                'metadata' => [
                    'new_os' => $validated['os'],
                    'new_control_panel' => $validated['control_panel'],
                ],
                'onLocked' => function (VpsInstance $locked) use ($validated, $newRootPassword) {
                    $locked->os = $validated['os'];
                    $locked->control_panel = $validated['control_panel'];
                    $locked->initial_root_password = $newRootPassword;
                    $locked->root_password_revealed_at = null;
                    $locked->save();
                },
                'allowSame' => true,
            ]);

            $this->vpsStateMachine->transition($vps->refresh(), 'running', [
                'reason' => 'Reinstall selesai.',
                'actor_type' => 'system',
            ]);

            $vps->refresh()->logActivity(
                action: 'reinstall',
                description: "Install ulang OS: {$validated['os']} dengan panel {$validated['control_panel']}. Kredensial root baru telah di-generate.",
                status: 'completed'
            );
        } catch (InvalidStateTransitionException $e) {
            return back()->with('error', 'Aksi tidak diizinkan: ' . $e->getMessage());
        }

        return back()->with('success', 'Sistem operasi dan control panel VPS berhasil dijadwalkan untuk install ulang. Password root baru telah di-generate.');
    }

    public function revealPassword(Request $request, $id)
    {
        $throttleKey = 'reveal_password|' . Auth::id() . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return response()->json([
                'success' => false,
                'message' => "Terlalu banyak percobaan verifikasi. Silakan coba lagi dalam {$seconds} detik.",
            ], 429);
        }

        $vps = VpsInstance::where('organization_id', $this->organizationId())->findOrFail($id);

        $request->validate([
            'password' => 'required|string',
        ]);

        if (!Hash::check($request->password, Auth::user()->password)) {
            RateLimiter::hit($throttleKey, 300); // 5 minutes decay
            return response()->json([
                'success' => false,
                'message' => 'Password akun yang Anda masukkan salah. Verifikasi keamanan gagal.',
            ], 403);
        }

        RateLimiter::clear($throttleKey);

        $vps->update([
            'root_password_revealed_at' => now(),
        ]);

        $vps->logActivity(
            action: 'password_reveal',
            description: 'Kredensial root password diakses melalui verifikasi password akun pengguna',
            status: 'completed'
        );

        // Password visible untuk 60 detik saja. FE wajib menyembunyikan setelah expires_at.
        $expiresAt = now()->addSeconds(60);

        return response()->json([
            'success' => true,
            'password' => $vps->initial_root_password,
            'revealed_at' => $vps->root_password_revealed_at->format('d M Y H:i:s'),
            'expires_at' => $expiresAt->toIso8601String(),
            'visible_seconds' => 60,
        ]);
    }

    public function billing()
    {
        $invoices = Invoice::where('organization_id', $this->organizationId())
            ->with('order.vpsSpec')->orderBy('created_at', 'desc')->paginate(10);
        $subscriptions = Subscription::where('organization_id', $this->organizationId())
            ->with(['vpsSpec', 'vpsInstance'])->latest()->get();

        return view('dashboard.billing', compact('invoices', 'subscriptions'));
    }

    public function toggleAutoRenew(Request $request, $id)
    {
        $subscription = Subscription::where('organization_id', $this->organizationId())->findOrFail($id);
        $subscription->update(['auto_renew' => $request->boolean('auto_renew')]);

        return back()->with('success', $subscription->auto_renew
            ? 'Perpanjangan otomatis berhasil diaktifkan.'
            : 'Perpanjangan otomatis berhasil dimatikan.');
    }

    public function cancelSubscription($id)
    {
        $subscription = Subscription::where('organization_id', $this->organizationId())->findOrFail($id);
        abort_unless($subscription->isCancellable(), 422, 'Langganan ini tidak dapat dibatalkan.');

        $subscription->update([
            'auto_renew' => false,
            'cancelled_at' => now(),
        ]);

        return back()->with('success', 'Langganan dibatalkan setelah periode berjalan berakhir. Layanan tetap aktif sampai tanggal jatuh tempo.');
    }

    public function resumeSubscription($id)
    {
        $subscription = Subscription::where('organization_id', $this->organizationId())->findOrFail($id);
        abort_unless($subscription->status === 'active' && $subscription->cancelled_at && (!$subscription->current_period_end || $subscription->current_period_end->isFuture()), 422, 'Langganan ini tidak dapat dilanjutkan.');

        $subscription->update(['auto_renew' => true, 'cancelled_at' => null]);

        return back()->with('success', 'Langganan dilanjutkan dan akan diperpanjang otomatis.');
    }

    public function printInvoice($id)
    {
        $user = Auth::user();
        $invoice = Invoice::with(['order.customer', 'order.vpsSpec'])->findOrFail($id);

        if ($invoice->organization_id !== $this->organizationId() && !$user->is_admin) {
            abort(403);
        }

        return view('dashboard.invoice-print', compact('invoice'));
    }

    public function support()
    {
        $user = Auth::user();
        $tickets = SupportTicket::where('organization_id', $this->organizationId())
            ->with(['vpsInstance', 'latestCustomerMessage'])
            ->orderBy('updated_at', 'desc')
            ->paginate(10);

        $vpsList = VpsInstance::where('organization_id', $this->organizationId())->get();

        return view('dashboard.support', compact('tickets', 'vpsList'));
    }

    public function storeTicket(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'vps_instance_id' => [
                'nullable',
                Rule::exists('vps_instances', 'id')->where('organization_id', $this->organizationId()),
            ],
            'priority' => 'required|in:low,medium,high',
            'message' => 'required|string|min:10',
        ]);

        $ticket = SupportTicket::create([
            'customer_id' => Auth::id(),
            'organization_id' => $this->organizationId(),
            'vps_instance_id' => $validated['vps_instance_id'] ?? null,
            'subject' => $validated['subject'],
            'priority' => $validated['priority'],
            'status' => 'open',
            'sla_due_at' => now()->addHours(4),
            'last_customer_reply_at' => now(),
        ]);

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'message' => $validated['message'],
            'is_admin_reply' => false,
        ]);

        // Notifikasi ke email admin bahwa ada tiket support baru
        try {
            $ticket->loadMissing('customer');
            $adminEmail = env('VEXAHOST_ADMIN_EMAIL', config('mail.from.address'));
            if ($adminEmail) {
                \Illuminate\Support\Facades\Notification::route('mail', $adminEmail)
                    ->notify(new \App\Notifications\AdminTicketNotification($ticket, $validated['message'], 'created'));
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('support.ticket_create.notify_failed', ['ticket_id' => $ticket->id, 'error' => $e->getMessage()]);
        }

        return redirect()->route('dashboard.support.show', $ticket->id)->with('success', 'Tiket bantuan #' . $ticket->id . ' berhasil dikirim. Tim VexaHost akan merespon dalam waktu maksimal 4 jam.');
    }

    public function showTicket($id)
    {
        $user = Auth::user();
        $ticket = SupportTicket::with(['customer', 'vpsInstance', 'customerMessages.user'])->findOrFail($id);

        if ($ticket->organization_id !== $this->organizationId() && !$user->is_admin) {
            abort(403);
        }

        return view('dashboard.ticket-show', compact('ticket'));
    }

    public function replyTicket(Request $request, $id)
    {
        $user = Auth::user();
        $ticket = SupportTicket::where('organization_id', $this->organizationId())->findOrFail($id);

        $validated = $request->validate([
            'message' => 'required|string|min:2',
        ]);

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'message' => $validated['message'],
            'is_admin_reply' => !empty($user->is_admin),
            'is_internal' => false,
        ]);

        $ticket->update(['last_customer_reply_at' => now(), 'status' => $ticket->status === 'resolved' ? 'open' : $ticket->status]);

        if ($user->is_admin) {
            $ticket->update(['status' => 'in_progress']);
        } else {
            if ($ticket->status === 'resolved' || $ticket->status === 'closed') {
                $ticket->update(['status' => 'open']);
            }

            // Notifikasi ke email admin bahwa customer membalas tiket
            try {
                $ticket->loadMissing('customer');
                $adminEmail = env('VEXAHOST_ADMIN_EMAIL', config('mail.from.address'));
                if ($adminEmail) {
                    \Illuminate\Support\Facades\Notification::route('mail', $adminEmail)
                        ->notify(new \App\Notifications\AdminTicketNotification($ticket, $validated['message'], 'replied'));
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('support.ticket_reply.notify_failed', ['ticket_id' => $ticket->id, 'error' => $e->getMessage()]);
            }
        }
        $ticket->touch();

        return back()->with('success', 'Pesan balasan berhasil terkirim.');
    }

    public function settings()
    {
        $user = Auth::user();
        return view('dashboard.settings', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:25',
            'company' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
        ]);

        $user->update($validated);

        return back()->with('success', 'Profil akun Anda berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini yang Anda masukkan salah.']);
        }

        $user->update([
            'password' => Hash::make($validated['new_password']),
        ]);

        return back()->with('success', 'Password akun Anda berhasil diperbarui.');
    }
}
