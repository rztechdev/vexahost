<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ============================================================
// Billing lifecycle scheduler (Poin 3)
// ============================================================
// Semua command dijalankan harian. Waktu dipilih agar tidak tabrakan
// dan agar reminder terkirim di jam kerja Indonesia.

Schedule::command('billing:check-due')
    ->dailyAt('08:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->onOneServer()
    ->description('Kirim reminder H-7/H-3/H-1/H+0 ke subscription yang mendekati jatuh tempo.');

Schedule::command('billing:renew')
    ->dailyAt('02:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->onOneServer()
    ->description('Generate invoice renewal untuk subscription auto-renew.');

Schedule::command('billing:enter-grace')
    ->dailyAt('03:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->onOneServer()
    ->description('Masukkan subscription past due ke grace period.');

Schedule::command('billing:suspend-overdue')
    ->dailyAt('04:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->onOneServer()
    ->description('Suspend subscription yang grace period-nya habis.');

Schedule::command('billing:terminate-expired')
    ->dailyAt('05:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->onOneServer()
    ->description('Terminate subscription yang suspended > 30 hari.');

// ============================================================
// Provisioning reconciliation scheduler (Poin 4)
// ============================================================
Schedule::command('provider:reconcile')
    ->everyFifteenMinutes()
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->onOneServer()
    ->description('Rekonsiliasi status VPS antara database dan provider.');
