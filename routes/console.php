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
// PHASE 1 - Maintenance terjadwal
// ============================================================
// Berjalan tiap 5 menit agar jendela maintenance mulai dan berakhir
// mendekati waktu yang dijadwalkan admin.
Schedule::command('maintenance:sync')
    ->everyFiveMinutes()
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->onOneServer()
    ->description('Mulai/akhiri jendela maintenance terjadwal dan kirim pemberitahuannya.');

// ============================================================
// PHASE 3 - Pengingat perpanjangan instance
// ============================================================
// Dijalankan sekali sehari. Idempotensi dijamin indeks unik di
// tabel renewal_reminders, sehingga aman bila terjadi pengulangan.
Schedule::command('renewal:remind')
    ->dailyAt('09:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->onOneServer()
    ->description('Kirim pengingat H-3/H-1/H-0 instance dan pindahkan status yang jatuh tempo.');

// ============================================================
// PHASE 5 - Peringatan waktu tanggap fulfillment
// ============================================================
// Tiap jam agar keterlambatan cepat diketahui. Tiap order hanya
// diperingatkan sekali (orders.sla_alerted_at).
Schedule::command('fulfillment:check-sla')
    ->hourly()
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->onOneServer()
    ->description('Peringatkan admin tentang order dibayar yang belum diserahkan melewati ambang.');

// ============================================================
// Provisioning reconciliation scheduler (Poin 4)
// ============================================================
Schedule::command('provider:reconcile')
    ->everyFifteenMinutes()
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->onOneServer()
    ->description('Rekonsiliasi status VPS antara database dan provider.');
