<?php

namespace App\Services;

use App\Models\VpsInstance;

/**
 * PHASE 3 - Penentu masa tenggang per jenis produk.
 *
 * Angka tenggang tidak boleh tunggal. Supplier menghapus VPS pada hari ke-7,
 * sedangkan database dan aplikasi pada hari ke-30. Tenggang yang kita janjikan
 * ke pelanggan selalu lebih pendek, agar tersisa ruang untuk bertindak
 * sebelum penghapusan permanen di sisi supplier terjadi.
 */
class RenewalService
{
    /**
     * Batas penghapusan di sisi Supplier, dalam hari.
     * Sumber: Terms of Service Supplier pasal 11.
     */
    public const SUPPLIER_LIMITS = [
        'vps' => 7,
        'database' => 30,
        'app' => 30,
    ];

    public function __construct(
        protected SettingsService $settings
    ) {
    }

    /**
     * Tentukan jenis produk sebuah instance.
     */
    public function productType(VpsInstance $instance): string
    {
        if ($instance->isDatabasePackage()) {
            return 'database';
        }

        if ($instance->isAiPackage()) {
            return 'app';
        }

        return 'vps';
    }

    /**
     * Jenis produk dari sebuah order, dipakai sebelum instance dibuat.
     */
    public function productTypeForOrder(\App\Models\Order $order): string
    {
        if ($order->isDatabasePackage()) {
            return 'database';
        }

        if ($order->isAiPackage()) {
            return 'app';
        }

        return 'vps';
    }

    /**
     * Tenggang untuk order yang sedang diprovision. Menggantikan angka 7 hari
     * yang dulu tertulis langsung di alur provisioning: 7 hari justru batas
     * penghapusan VPS di Supplier, sehingga tidak menyisakan ruang aman.
     */
    public function graceDaysForOrder(\App\Models\Order $order): int
    {
        return $this->graceDaysForType($this->productTypeForOrder($order));
    }

    /**
     * Jumlah hari tenggang yang dijanjikan ke pelanggan untuk instance ini.
     */
    public function graceDaysFor(VpsInstance $instance): int
    {
        return $this->graceDaysForType($this->productType($instance));
    }

    public function graceDaysForType(string $type): int
    {
        $configured = match ($type) {
            'database' => $this->settings->int('renewal_grace_days_database', 25),
            'app' => $this->settings->int('renewal_grace_days_app', 25),
            default => $this->settings->int('renewal_grace_days_vps', 5),
        };

        $limit = self::SUPPLIER_LIMITS[$type] ?? 7;

        // Pengaman keras: tenggang kita tidak boleh menyentuh batas supplier.
        // Bila pengaturan terlanjur diisi terlalu besar, dipangkas di sini.
        return max(1, min($configured, $limit - 1));
    }

    /**
     * Ruang aman yang tersisa sebelum supplier menghapus data, dalam hari.
     */
    public function safetyMarginFor(VpsInstance $instance): int
    {
        $type = $this->productType($instance);

        return (self::SUPPLIER_LIMITS[$type] ?? 7) - $this->graceDaysForType($type);
    }

    /**
     * Hitung dan simpan tanggal akhir tenggang berdasarkan expires_at.
     */
    public function applyGracePeriod(VpsInstance $instance): VpsInstance
    {
        if (!$instance->expires_at) {
            return $instance;
        }

        $instance->grace_period_ends_at = $instance->expires_at
            ->copy()
            ->addDays($this->graceDaysFor($instance));

        $instance->save();

        return $instance;
    }

    /**
     * Label jenis produk untuk ditampilkan di antarmuka dan surel.
     */
    public function typeLabel(string $type): string
    {
        return match ($type) {
            'database' => 'Database Terkelola',
            'app' => 'Aplikasi & AI',
            default => 'VPS',
        };
    }
}
