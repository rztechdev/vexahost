<?php

namespace App\Providers;

use App\Models\AbuseCase;
use App\Models\MaintenanceWindow;
use App\Models\Order;
use App\Models\SupportTicket;
use App\Models\WebhookEvent;
use App\Services\ImpersonationService;
use App\Services\MaintenanceService;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;
use App\Services\SettingsService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Singleton agar cache tingkat request di dalamnya benar-benar dipakai ulang.
        $this->app->singleton(SettingsService::class, fn () => new SettingsService());
        $this->app->singleton(
            MaintenanceService::class,
            fn ($app) => new MaintenanceService($app->make(SettingsService::class))
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (str_contains(request()->header('x-forwarded-proto', ''), 'https') || request()->isSecure()) {
            URL::forceScheme('https');
        }

        // PHASE 7 - logout saat sedang masuk sebagai pelanggan tetap menutup
        // catatan sesi impersonation. Event Logout dipicu sebelum sesi dihapus,
        // sehingga data impersonation masih dapat dibaca di sini.
        Event::listen(Logout::class, function () {
            $request = request();

            if (!$request->hasSession()) {
                return;
            }

            $data = $request->session()->get(ImpersonationService::SESSION_KEY);

            if (is_array($data) && !empty($data['log_id'])) {
                app(ImpersonationService::class)->closeLog((int) $data['log_id'], 'logout');
            }
        });

        // Share open ticket count with dashboard layout to avoid N+1 query in blade
        View::composer('layouts.dashboard', function ($view) {
            $userOpenTickets = 0;
            if (Auth::check()) {
                $userOpenTickets = SupportTicket::where('organization_id', Auth::user()->current_organization_id)
                    ->whereIn('status', ['open', 'in_progress'])
                    ->count();
            }
            $view->with('userOpenTickets', $userOpenTickets);
        });

        // Share admin sidebar counts with admin layout.
        //
        // Penghitung untuk tabel baru dibungkus pemeriksaan keberadaan tabel.
        // Tanpa ini, panel admin ikut mati (HTTP 500) pada jeda antara deploy
        // kode baru dan dijalankannya migrasi di server.
        View::composer('layouts.admin', function ($view) {
            $view->with('adminPendingOrders', Order::where('status', 'pending')->count());
            $view->with('adminOpenTickets', SupportTicket::whereIn('status', ['open', 'in_progress'])->count());

            $view->with(
                'adminOpenAbuse',
                Schema::hasTable('abuse_cases') ? AbuseCase::unresolved()->count() : 0
            );
            $view->with(
                'adminRunningMaintenance',
                Schema::hasTable('maintenance_windows') ? MaintenanceWindow::active()->count() : 0
            );
            $view->with(
                'adminFulfillmentWorking',
                Order::whereIn('status', ['paid', 'failed', 'provisioning'])->count()
            );
            $view->with('adminPendingRefunds', \App\Models\Refund::where('status', 'pending')->count());
            $view->with(
                'adminSupplierUrgent',
                Schema::hasTable('supplier_purchases')
                    ? app(\App\Services\SupplierCoverageService::class)->urgentCount()
                    : 0
            );
            $view->with(
                'adminFailedWebhooks',
                Schema::hasColumn('webhook_events', 'http_status')
                    ? WebhookEvent::where('processing_status', 'failed')->count()
                    : 0
            );
        });

        // Branding dibagikan ke SELURUH view agar nama merek, logo, dan warna
        // tidak lagi ditulis langsung di blade. Diambil dari tabel settings (Phase 1).
        View::composer('*', function ($view) {
            $settings = app(SettingsService::class);

            $view->with('brand', [
                'name' => $settings->get('brand_name', config('app.name', 'VexaHost')),
                'tagline' => $settings->get('brand_tagline', ''),
                'logo' => $settings->get('brand_logo_path')
                    ? asset('storage/' . $settings->get('brand_logo_path'))
                    : asset('images/logo.png'),
                'favicon' => $settings->get('brand_favicon_path')
                    ? asset('storage/' . $settings->get('brand_favicon_path'))
                    : asset('images/logo.png'),
                'accent' => $settings->get('brand_accent_color', '#4A6FA5'),
                'support_email' => $settings->get('support_email', 'support@vexahostcloud.my.id'),
                'support_whatsapp' => $settings->get('support_whatsapp', ''),
            ]);
        });

        // Spanduk maintenance terjadwal di dasbor klien.
        View::composer('layouts.dashboard', function ($view) {
            $view->with('upcomingMaintenance', app(MaintenanceService::class)->upcomingWindows());
        });
    }
}
