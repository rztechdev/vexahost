<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VpsSpec extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'cpu',
        'ram',
        'disk',
        'bandwidth',
        'cost_price',
        'sell_price',
        'is_active',
        'tagline',
        'target_audience',
        'features',
        'solution',
        'badge',
        'default_stack',
    ];

    protected $casts = [
        'features' => 'array',
        'is_active' => 'boolean',
        'cpu' => 'integer',
        'ram' => 'integer',
        'disk' => 'integer',
        'bandwidth' => 'integer',
        'cost_price' => 'decimal:2',
        'sell_price' => 'decimal:2',
    ];

    protected $appends = [
        'allowed_providers',
        'default_provider',
        'is_ai_package',
        'is_database_package',
        'is_direct_checkout',
    ];

    public function isAiPackage(): bool
    {
        return ($this->category === 'ai_combo')
            || in_array((int)$this->id, [7, 8, 9, 10], true)
            || str_contains(strtolower($this->name), 'combo')
            || str_contains(strtolower($this->name), 'terminal coding agent')
            || str_contains(strtolower($this->name), 'cloud ai workstation')
            || str_contains(strtolower($this->name), 'hermes')
            || str_contains(strtolower($this->name), 'rag');
    }

    public function getIsAiPackageAttribute(): bool
    {
        return $this->isAiPackage();
    }

    public function isDatabasePackage(): bool
    {
        return ($this->category === 'managed_db')
            || in_array((int)$this->id, [11, 12, 13], true)
            || str_starts_with(strtolower($this->name), 'db ')
            || str_contains(strtolower($this->name), 'db enterprise');
    }

    public function getIsDatabasePackageAttribute(): bool
    {
        return $this->isDatabasePackage();
    }

    public function isDirectCheckout(): bool
    {
        return $this->isAiPackage() || $this->isDatabasePackage();
    }

    public function getIsDirectCheckoutAttribute(): bool
    {
        return $this->isDirectCheckout();
    }

    public function allowedProviders(): array
    {
        $name = strtolower($this->name);

        if ($this->isDatabasePackage()) {
            return ['tencent', 'cloudeka'];
        }

        if ($this->isAiPackage()) {
            return ['cloudeka', 'tencent'];
        }

        // Student Basic, Startup, and Business can ONLY use Cloudeka
        if (in_array($name, ['student basic', 'startup', 'business', 'ai production', 'ai production pro'], true) || str_contains($name, 'ai')) {
            return ['cloudeka'];
        }

        // Mahasiswa Basic, Standard, and Premium can ONLY use Tencent
        return ['tencent'];
    }

    public function isProviderAllowed(string $provider): bool
    {
        return in_array($provider, $this->allowedProviders(), true);
    }

    public function defaultProvider(): string
    {
        return $this->allowedProviders()[0];
    }

    public function getAllowedProvidersAttribute(): array
    {
        return $this->allowedProviders();
    }

    public function getDefaultProviderAttribute(): string
    {
        return $this->defaultProvider();
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
