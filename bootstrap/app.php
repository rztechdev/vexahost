<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $trustedProxies = env('TRUSTED_PROXIES');
        if ($trustedProxies === '*') {
            $middleware->trustProxies(at: '*');
        } elseif (!empty($trustedProxies)) {
            $middleware->trustProxies(at: array_map('trim', explode(',', $trustedProxies)));
        } else {
            $middleware->trustProxies(at: ['127.0.0.1', '::1']);
        }

        // Security headers untuk semua response web.
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);

        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'admin.api' => \App\Http\Middleware\AdminApiKeyMiddleware::class,
            '2fa' => \App\Http\Middleware\EnsureTwoFactorConfirmed::class,
            'permission' => \App\Http\Middleware\RequirePermission::class,
            'role' => \App\Http\Middleware\RequireRole::class,
            'org.context' => \App\Http\Middleware\EnsureOrganizationContext::class,
            'apikey' => \App\Http\Middleware\ApiKeyAuth::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
