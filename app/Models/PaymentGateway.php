<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * PHASE 4 - Registry payment gateway.
 *
 * Kredensial disimpan terenkripsi dan dibaca lewat credential(), yang jatuh
 * kembali ke config/services.php bila registry belum diisi. Dengan begitu,
 * kunci dapat dirotasi dari panel tanpa deploy, sementara server lama yang
 * hanya punya .env tetap berjalan.
 */
class PaymentGateway extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'is_active',
        'mode',
        'credentials',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'credentials' => 'encrypted:array',
    ];

    protected $hidden = ['credentials'];

    public const MODES = ['sandbox', 'production'];

    /**
     * Definisi field kredensial per gateway. Hanya field ini yang boleh disimpan.
     */
    public static function credentialFields(): array
    {
        return [
            'lynk' => ['merchant_key' => 'Merchant Key'],
            'midtrans' => ['server_key' => 'Server Key', 'client_key' => 'Client Key'],
            'xendit' => ['secret_key' => 'Secret Key', 'callback_token' => 'Callback Token'],
            'tripay' => ['api_key' => 'API Key', 'private_key' => 'Private Key', 'merchant_code' => 'Kode Merchant'],
            'qris' => [],
            'manual_transfer' => [
                'bank_name' => 'Nama Bank',
                'account_number' => 'Nomor Rekening',
                'account_name' => 'Atas Nama',
            ],
        ];
    }

    public function fields(): array
    {
        return self::credentialFields()[$this->code] ?? [];
    }

    /**
     * Kredensial hasil dekripsi. Bila APP_KEY berganti dan dekripsi gagal,
     * dikembalikan array kosong agar halaman admin tetap bisa dibuka.
     */
    public function decryptedCredentials(): array
    {
        try {
            return $this->credentials ?? [];
        } catch (\Throwable $e) {
            Log::error('payment_gateway.decrypt_failed', ['code' => $this->code, 'error' => $e->getMessage()]);

            return [];
        }
    }

    /**
     * Nilai kredensial yang disamarkan, mis. "vx_s••••••3f".
     */
    public function maskedCredential(string $key): ?string
    {
        $value = (string) ($this->decryptedCredentials()[$key] ?? '');

        if ($value === '') {
            return null;
        }

        if (strlen($value) <= 8) {
            return str_repeat('•', strlen($value));
        }

        return substr($value, 0, 4) . str_repeat('•', 6) . substr($value, -2);
    }

    public function hasCredential(string $key): bool
    {
        return (string) ($this->decryptedCredentials()[$key] ?? '') !== '';
    }

    /**
     * Baca satu kredensial dari registry, jatuh kembali ke nilai cadangan.
     */
    public static function credential(string $code, string $key, mixed $fallback = null): mixed
    {
        try {
            if (!Schema::hasTable('payment_gateways')) {
                return $fallback;
            }

            $gateway = self::where('code', $code)->first();
            $value = $gateway?->decryptedCredentials()[$key] ?? null;

            return ($value === null || $value === '') ? $fallback : $value;
        } catch (\Throwable $e) {
            return $fallback;
        }
    }

    /**
     * Metode di checkout dan gateway yang menanganinya.
     * VA dan e-wallet berjalan lewat Midtrans.
     */
    public const METHOD_GATEWAY = [
        'lynk' => 'lynk',
        'qris' => 'qris',
        'midtrans_snap' => 'midtrans',
        'bca_va' => 'midtrans',
        'mandiri_va' => 'midtrans',
        'bni_va' => 'midtrans',
        'bri_va' => 'midtrans',
        'cimb_va' => 'midtrans',
        'permata_va' => 'midtrans',
        'other_va' => 'midtrans',
        'gopay' => 'midtrans',
        'bsi_va' => 'midtrans',
        'danamon_va' => 'midtrans',
        'seabank_va' => 'midtrans',
        'credit_card' => 'midtrans',
        'ovo' => 'midtrans',
        'dana' => 'midtrans',
        'shopeepay' => 'midtrans',
    ];

    /**
     * Cek apakah suatu metode pembayaran ditangani oleh Midtrans.
     */
    public static function isMidtransMethod(string $method): bool
    {
        return (self::METHOD_GATEWAY[$method] ?? null) === 'midtrans';
    }

    /**
     * Metode checkout yang boleh dipilih saat ini.
     *
     * Bila registry belum ada (migrasi belum jalan), dipakai nilai lama
     * yang dulu tertulis langsung di blade: hanya lynk dan qris.
     */
    public static function activeMethods(): array
    {
        try {
            if (!Schema::hasTable('payment_gateways') || !self::query()->exists()) {
                return ['lynk', 'qris'];
            }

            $activeCodes = self::active()->pluck('code')->all();
        } catch (\Throwable $e) {
            return ['lynk', 'qris'];
        }

        return array_keys(array_filter(
            self::METHOD_GATEWAY,
            fn (string $gateway) => in_array($gateway, $activeCodes, true)
        ));
    }

    /**
     * Dipanggil per metode di dalam loop checkout. once() menyimpan hasilnya
     * selama satu request agar tidak menjalankan kueri untuk setiap metode.
     */
    public static function isMethodActive(string $method): bool
    {
        return in_array($method, once(fn () => self::activeMethods()), true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getModeLabelAttribute(): string
    {
        return $this->mode === 'sandbox' ? 'Sandbox' : 'Produksi';
    }
}
