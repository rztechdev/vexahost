<?php

use App\Http\Controllers\Api\AdminApiController;
use App\Http\Controllers\Api\LynkWebhookController;
use App\Http\Controllers\Api\PaymentWebhookController;
use Illuminate\Support\Facades\Route;

// Payment Webhook (public - tapi di-verify dengan signature)
Route::post('/webhooks/payment', [PaymentWebhookController::class, 'handle'])->name('api.webhooks.payment');
Route::post('/webhooks/lynk', [LynkWebhookController::class, 'handle'])->name('api.webhooks.lynk');

// Admin API Endpoints - PROTECTED with admin.api middleware (ApiKey or Admin Session)
Route::prefix('admin')->middleware('admin.api')->group(function () {
    Route::post('/vps/provision', [AdminApiController::class, 'provision'])->name('api.admin.vps.provision');
    Route::post('/shopee/process-order', [AdminApiController::class, 'processShopeeOrder'])->name('api.admin.shopee.process');
    Route::get('/vps/{id}/status', [AdminApiController::class, 'vpsStatus'])->name('api.admin.vps.status');
});
