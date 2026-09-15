<?php

namespace App\Providers;

use App\Models\Order;
use App\Models\SupportTicket;
use Illuminate\Support\Facades\Auth;
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
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (str_contains(request()->header('x-forwarded-proto', ''), 'https') || request()->isSecure()) {
            URL::forceScheme('https');
        }

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

        // Share admin sidebar counts with admin layout
        View::composer('layouts.admin', function ($view) {
            $view->with('adminPendingOrders', Order::where('status', 'pending')->count());
            $view->with('adminOpenTickets', SupportTicket::whereIn('status', ['open', 'in_progress'])->count());
        });
    }
}
