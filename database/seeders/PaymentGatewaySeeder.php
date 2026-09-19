<?php

namespace Database\Seeders;

use App\Models\PaymentGateway;
use Illuminate\Database\Seeder;

/**
 * PHASE 4 - Registry payment gateway bawaan.
 *
 * Status aktif awal SENGAJA sama dengan perilaku checkout sebelumnya:
 * hanya Lynk.id dan QRIS yang bisa dipilih. Gateway lain didaftarkan
 * nonaktif sebagai kerangka, menunggu pendaftaran merchant.
 *
 * Aman dijalankan berulang: baris yang sudah ada tidak ditimpa, sehingga
 * status dan kredensial yang diubah admin tidak kembali ke bawaan.
 */
class PaymentGatewaySeeder extends Seeder
{
    public function run(): void
    {
        $gateways = [
            [
                'code' => 'lynk',
                'name' => 'Lynk.id',
                'description' => 'Jalur pembayaran utama lewat webhook. QRIS, VA, e-wallet, dan kartu via Lynk.id.',
                'is_active' => true,
                'mode' => 'production',
            ],
            [
                'code' => 'qris',
                'name' => 'QRIS VexaHost',
                'description' => 'QRIS statis dengan verifikasi manual di menu Verifikasi Pembayaran.',
                'is_active' => true,
                'mode' => 'production',
            ],
            [
                'code' => 'midtrans',
                'name' => 'Midtrans',
                'description' => 'Virtual Account dan e-wallet (BCA, Mandiri, BNI, BRI, GoPay, OVO, DANA, ShopeePay). Menunggu pendaftaran merchant.',
                'is_active' => false,
                'mode' => 'sandbox',
            ],
            [
                'code' => 'xendit',
                'name' => 'Xendit',
                'description' => 'Kerangka. Menunggu pendaftaran merchant.',
                'is_active' => false,
                'mode' => 'sandbox',
            ],
            [
                'code' => 'tripay',
                'name' => 'Tripay',
                'description' => 'Kerangka. Menunggu pendaftaran merchant.',
                'is_active' => false,
                'mode' => 'sandbox',
            ],
            [
                'code' => 'manual_transfer',
                'name' => 'Transfer Bank Manual',
                'description' => 'Kerangka rekening tujuan transfer manual. Belum ditawarkan di checkout.',
                'is_active' => false,
                'mode' => 'production',
            ],
        ];

        foreach ($gateways as $index => $gateway) {
            PaymentGateway::firstOrCreate(
                ['code' => $gateway['code']],
                $gateway + ['sort_order' => $index]
            );
        }
    }
}
