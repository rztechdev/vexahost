<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\SecurityController;
use App\Http\Controllers\TwoFactorController;
use App\Models\VpsSpec;
use Illuminate\Support\Facades\Route;

// Public Landing Page
Route::get('/', function () {
    $vpsSpecs = VpsSpec::where('is_active', true)->where('category', 'vps')->orderBy('sell_price', 'asc')->get();
    $aiSpecs = VpsSpec::where('is_active', true)->where('category', 'ai_combo')->orderBy('sell_price', 'asc')->get();
    $dbSpecs = VpsSpec::where('is_active', true)->where('category', 'managed_db')->orderBy('sell_price', 'asc')->get();
    $specs = $vpsSpecs;
    return view('landing', compact('specs', 'vpsSpecs', 'aiSpecs', 'dbSpecs'));
})->name('home');

// Public Content & Info Pages
Route::view('/docs', 'pages.docs')->name('docs');
Route::view('/status', 'pages.status')->name('status');
Route::redirect('/kontak', '/#kontak')->name('contact');

// Public Direct Package Section Routes
Route::redirect('/vps', '/#pricing')->name('packages.vps');
Route::redirect('/tambah-vps', '/#pricing');
Route::redirect('/ai', '/#ai-packages')->name('packages.ai');
Route::redirect('/ai-agent', '/#ai-packages');
Route::redirect('/tambah-ai-agent', '/#ai-packages');
Route::redirect('/database', '/#database-packages')->name('packages.db');
Route::redirect('/db', '/#database-packages');
Route::redirect('/tambah-database', '/#database-packages');

// Public Legal Pages
Route::view('/terms', 'pages.terms')->name('terms');
Route::view('/privacy', 'pages.privacy')->name('privacy');
Route::view('/sla', 'pages.sla')->name('sla');
Route::view('/refund', 'pages.refund')->name('refund');

// Public XML Sitemap (Google Search Console)
Route::get('/sitemap.xml', function () {
    $baseUrl = rtrim(config('app.url', url('/')), '/');
    $urls = [
        ['loc' => $baseUrl . '/', 'changefreq' => 'daily', 'priority' => '1.0'],
        ['loc' => $baseUrl . '/docs', 'changefreq' => 'weekly', 'priority' => '0.8'],
        ['loc' => $baseUrl . '/status', 'changefreq' => 'hourly', 'priority' => '0.7'],
        ['loc' => $baseUrl . '/terms', 'changefreq' => 'monthly', 'priority' => '0.5'],
        ['loc' => $baseUrl . '/privacy', 'changefreq' => 'monthly', 'priority' => '0.5'],
        ['loc' => $baseUrl . '/sla', 'changefreq' => 'monthly', 'priority' => '0.5'],
        ['loc' => $baseUrl . '/refund', 'changefreq' => 'monthly', 'priority' => '0.5'],
        ['loc' => $baseUrl . '/checkout', 'changefreq' => 'weekly', 'priority' => '0.9'],
    ];

    try {
        $specs = \App\Models\VpsSpec::where('is_active', true)->get();
        foreach ($specs as $spec) {
            $urls[] = [
                'loc' => $baseUrl . '/checkout/' . $spec->id,
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ];
        }
    } catch (\Throwable $e) {
        // Fallback gracefully jika database belum dimigrate
    }

    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

    foreach ($urls as $u) {
        $xml .= "    <url>\n";
        $xml .= "        <loc>" . htmlspecialchars($u['loc'], ENT_XML1) . "</loc>\n";
        $xml .= "        <lastmod>" . now()->toAtomString() . "</lastmod>\n";
        $xml .= "        <changefreq>" . $u['changefreq'] . "</changefreq>\n";
        $xml .= "        <priority>" . $u['priority'] . "</priority>\n";
        $xml .= "    </url>\n";
    }

    $xml .= '</urlset>';

    return response($xml, 200, [
        'Content-Type' => 'application/xml; charset=utf-8',
    ]);
})->name('sitemap');

// Website Self-Serve Order Flow (PRD 7.1.1)
Route::get('/checkout/{spec_id?}', [OrderController::class, 'checkout'])->name('checkout');
Route::post('/checkout', [OrderController::class, 'store'])->name('order.store');
Route::post('/checkout/quick-login', [OrderController::class, 'quickLogin'])->name('checkout.quick-login');
Route::get('/order/payment/{id}', [OrderController::class, 'payment'])->name('order.payment')->middleware('auth');
// Halaman polling status pembayaran (read-only, diotentikasi via controller & session order).
Route::get('/order/payment/{id}/status', [OrderController::class, 'paymentStatus'])->name('order.payment.status');
// JSON endpoint untuk polling status by frontend.
Route::get('/order/payment/{id}/status.json', [OrderController::class, 'paymentStatusJson'])->name('order.payment.status.json');
// DEV ONLY: simulate payment - dipagari APP_ENV=local dan APP_DEV_SIMULATE_PAYMENT=true.
Route::post('/order/payment/{id}/dev-simulate', [OrderController::class, 'devSimulatePayment'])
    ->name('order.payment.dev-simulate')
    ->middleware(['auth', 'throttle:5,1']);
Route::get('/order/success/{id}', [OrderController::class, 'success'])->name('order.success');
Route::get('/order/callback', [OrderController::class, 'paymentCallback'])->name('order.callback');
Route::get('/payment/callback', [OrderController::class, 'paymentCallback'])->name('payment.callback');
Route::get('/qris/render', function (\Illuminate\Http\Request $request, \App\Services\QrisService $qrisService) {
    $amount = max(1000, (float) $request->input('amount', 80000));
    return response()->json([
        'amount' => $amount,
        'payload' => $qrisService->generatePayload($amount),
        'svg_data_uri' => $qrisService->generateDataUri($amount),
    ]);
})->name('qris.render');

// Authentication Routes (PRD 12.1)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,1');

    // Google OAuth (PRD 12.2)
    Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('auth.google');
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('auth.google.callback');

    // Password reset (Poin 5)
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email')->middleware('throttle:5,1');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update')->middleware('throttle:5,1');
});

// Two-factor challenge — dapat diakses tanpa full auth (session masih pending).
Route::get('/two-factor/challenge', [TwoFactorController::class, 'showChallenge'])->name('two-factor.challenge');
Route::post('/two-factor/challenge', [TwoFactorController::class, 'verifyChallenge'])->name('two-factor.verify')->middleware('throttle:10,1');

// Public invitation view (butuh login untuk accept)
Route::get('/invitations/{token}', [InvitationController::class, 'show'])->name('invitations.show');
Route::post('/invitations/{token}/accept', [InvitationController::class, 'accept'])->name('invitations.accept')->middleware('auth');

// Authenticated User Routes
// org.context middleware memastikan setiap user login punya currentOrganization.
Route::middleware(['auth', '2fa', 'org.context'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Email verification (Laravel built-in)
    Route::get('/email/verify', [AuthController::class, 'showVerificationNotice'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
        ->middleware(['signed', 'throttle:6,1'])->name('verification.verify');
    Route::post('/email/verification-notification', [AuthController::class, 'resendVerification'])
        ->middleware('throttle:6,1')->name('verification.send');

    // Security settings (Poin 5)
    Route::prefix('security')->name('security.')->group(function () {
        Route::get('/', [SecurityController::class, 'index'])->name('settings');
        Route::get('/login-activity', [SecurityController::class, 'loginActivity'])->name('login-activity');
        Route::get('/sessions', [SecurityController::class, 'sessions'])->name('sessions');
        Route::post('/sessions/{sessionId}/revoke', [SecurityController::class, 'revokeSession'])->name('sessions.revoke');
        Route::post('/sessions/revoke-all', [SecurityController::class, 'revokeAllOtherSessions'])->name('sessions.revoke-all');
        Route::post('/api-keys', [SecurityController::class, 'createApiKey'])->name('api-keys.create');
        Route::post('/api-keys/{id}/revoke', [SecurityController::class, 'revokeApiKey'])->name('api-keys.revoke');
        Route::post('/api-keys/{id}/rotate', [SecurityController::class, 'rotateApiKey'])->name('api-keys.rotate');
        Route::post('/ssh-keys', [SecurityController::class, 'createSshKey'])->name('ssh-keys.create');
        Route::delete('/ssh-keys/{id}', [SecurityController::class, 'deleteSshKey'])->name('ssh-keys.delete');

        // Two-factor
        Route::get('/two-factor/enable', [TwoFactorController::class, 'showEnable'])->name('two-factor.enable');
        Route::post('/two-factor/enable', [TwoFactorController::class, 'enable'])->name('two-factor.confirm');
        Route::post('/two-factor/disable', [TwoFactorController::class, 'disable'])->name('two-factor.disable');
        Route::get('/two-factor/recovery-codes', [TwoFactorController::class, 'showRecoveryCodes'])->name('recovery-codes');
        Route::post('/two-factor/recovery-codes/regenerate', [TwoFactorController::class, 'regenerateRecoveryCodes'])->name('recovery-codes.regenerate');
    });

    // Organizations (Poin 6)
    Route::prefix('organizations')->name('organizations.')->group(function () {
        Route::get('/', [OrganizationController::class, 'index'])->name('index');
        Route::get('/create', [OrganizationController::class, 'create'])->name('create');
        Route::post('/', [OrganizationController::class, 'store'])->name('store');
        Route::get('/{id}', [OrganizationController::class, 'show'])->name('show');
        Route::post('/{id}/switch', [OrganizationController::class, 'switch'])->name('switch');
        Route::post('/{id}/billing', [OrganizationController::class, 'updateBilling'])->name('billing');
        Route::post('/{id}/invite', [OrganizationController::class, 'invite'])->name('invite');
        Route::post('/{id}/members/{memberId}/remove', [OrganizationController::class, 'removeMember'])->name('members.remove');
        Route::post('/{id}/members/{memberId}/role', [OrganizationController::class, 'updateMemberRole'])->name('members.role');
        Route::post('/invitations/{id}/revoke', [InvitationController::class, 'revoke'])->name('invitations.revoke');
    });

    // Customer Dashboard Routes (PRD 8.1)
    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('index');
        Route::get('/vps/{id}', [DashboardController::class, 'show'])->middleware('permission:vps.read')->name('vps.show');
        // Polling endpoint untuk progress provisioning (Poin 4).
        Route::get('/vps/{id}/provisioning-status.json', [DashboardController::class, 'provisioningStatus'])->middleware('permission:vps.read')->name('vps.provisioning-status');
        Route::post('/vps/{id}/start', [DashboardController::class, 'start'])->middleware('permission:vps.manage')->name('vps.start');
        Route::post('/vps/{id}/stop', [DashboardController::class, 'stop'])->middleware('permission:vps.manage')->name('vps.stop');
        Route::post('/vps/{id}/reboot', [DashboardController::class, 'reboot'])->middleware('permission:vps.manage')->name('vps.reboot');
        Route::post('/vps/{id}/force-reboot', [DashboardController::class, 'forceReboot'])->middleware('permission:vps.manage')->name('vps.force-reboot');
        Route::post('/vps/{id}/reinstall', [DashboardController::class, 'reinstall'])->middleware('permission:vps.reinstall')->name('vps.reinstall');
        Route::post('/vps/{id}/reveal-password', [DashboardController::class, 'revealPassword'])->middleware('permission:vps.credentials')->name('vps.reveal-password');
        Route::get('/billing', [DashboardController::class, 'billing'])->middleware('permission:billing.read')->name('billing');
        Route::post('/subscriptions/{id}/auto-renew', [DashboardController::class, 'toggleAutoRenew'])->middleware('permission:billing.read')->name('subscriptions.auto-renew');
        Route::post('/subscriptions/{id}/cancel', [DashboardController::class, 'cancelSubscription'])->middleware('permission:billing.read')->name('subscriptions.cancel');
        Route::post('/subscriptions/{id}/resume', [DashboardController::class, 'resumeSubscription'])->middleware('permission:billing.read')->name('subscriptions.resume');
        Route::get('/invoices/{id}/print', [DashboardController::class, 'printInvoice'])->middleware('permission:billing.read')->name('invoice.print');
        Route::get('/support', [DashboardController::class, 'support'])->name('support');
        Route::post('/support', [DashboardController::class, 'storeTicket'])->name('support.store');
        Route::get('/support/{id}', [DashboardController::class, 'showTicket'])->name('support.show');
        Route::post('/support/{id}/reply', [DashboardController::class, 'replyTicket'])->name('support.reply');
        Route::get('/settings', [DashboardController::class, 'settings'])->name('settings');
        Route::post('/settings/profile', [DashboardController::class, 'updateProfile'])->name('settings.profile');
        Route::post('/settings/password', [DashboardController::class, 'updatePassword'])->name('settings.password');
    });

    // Admin Panel Routes (Ryan Only - PRD 8.2)
    Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('index');
        Route::get('/payments', [AdminController::class, 'payments'])->name('payments');
        Route::post('/payments/{id}/approve', [AdminController::class, 'approvePayment'])->name('payments.approve');
        Route::post('/payments/{id}/hold', [AdminController::class, 'holdPayment'])->name('payments.hold');
        Route::post('/payments/{id}/reject', [AdminController::class, 'rejectPayment'])->name('payments.reject');
        Route::get('/orders', [AdminController::class, 'orders'])->name('orders');
        Route::post('/orders/{id}/provision', [AdminController::class, 'provision'])->name('orders.provision');
        Route::post('/orders/{id}/cancel', [AdminController::class, 'cancelOrder'])->name('orders.cancel');
        // Admin manual mark paid (untuk payment offline / rekonsiliasi manual).
        Route::post('/orders/{id}/mark-paid', [AdminController::class, 'markOrderPaid'])->name('orders.mark-paid');
        // Retry provisioning yang gagal.
        Route::post('/orders/{id}/retry-provision', [AdminController::class, 'retryProvision'])->name('orders.retry-provision');
        // Riwayat status order (audit trail).
        Route::get('/orders/{id}/history', [AdminController::class, 'orderHistory'])->name('orders.history');
        Route::get('/shopee', [AdminController::class, 'showShopeeForm'])->name('shopee');
        Route::post('/shopee/process', [AdminController::class, 'processShopeeOrder'])->name('shopee.process');
        Route::get('/instances', [AdminController::class, 'instances'])->name('instances');
        Route::get('/monitoring', [AdminController::class, 'monitoring'])->name('monitoring');
        Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
        Route::get('/reports/preview', [AdminController::class, 'previewReport'])->name('reports.preview');
        Route::get('/reports/export', [AdminController::class, 'exportReport'])->name('reports.export');
        Route::post('/instances/{id}/status', [AdminController::class, 'updateInstanceStatus'])->name('instances.status');
        Route::post('/instances/{id}/suspend', [AdminController::class, 'suspendInstance'])->name('instances.suspend');
        Route::post('/instances/{id}/unsuspend', [AdminController::class, 'unsuspendInstance'])->name('instances.unsuspend');
        Route::post('/instances/{id}/terminate', [AdminController::class, 'terminateInstance'])->name('instances.terminate');
        Route::get('/packages', [AdminController::class, 'packages'])->name('packages.index');
        Route::post('/packages', [AdminController::class, 'storePackage'])->name('packages.store');
        Route::put('/packages/{id}', [AdminController::class, 'updatePackage'])->name('packages.update');
        Route::delete('/packages/{id}', [AdminController::class, 'destroyPackage'])->name('packages.destroy');
        Route::get('/customers', [AdminController::class, 'customers'])->name('customers');
        Route::get('/tickets', [AdminController::class, 'tickets'])->name('tickets');
        Route::get('/tickets/{id}', [AdminController::class, 'showTicket'])->name('tickets.show');
        Route::post('/tickets/{id}/reply', [AdminController::class, 'replyTicket'])->name('tickets.reply');
        Route::post('/tickets/{id}/assign', [AdminController::class, 'assignTicket'])->name('tickets.assign');
    });
});
