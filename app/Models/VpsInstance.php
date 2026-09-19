<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class VpsInstance extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(function (self $instance) {
            if (!$instance->organization_id && $instance->customer_id) {
                $instance->organization_id = User::find($instance->customer_id)?->current_organization_id;
            }
        });
    }

    public function getControlPanelLabelAttribute(): string
    {
        return Order::stackLabels()[$this->control_panel] ?? str_replace('_', ' ', ucfirst((string) $this->control_panel));
    }

    public function getProviderLabelAttribute(): string
    {
        // Nilai tak dikenal (mis. nama driver supplier) diganti label netral.
        return Order::customerProviderLabel($this->provider);
    }

    public function getOsLabelAttribute(): string
    {
        return Order::osLabels()[$this->os] ?? (string) $this->os;
    }

    protected $fillable = [
        'customer_id',
        'organization_id',
        'order_id',
        'hostname',
        'public_ip',
        'private_ip',
        'ssh_port',
        'initial_root_password',
        'root_password_revealed_at',
        'os',
        'datacenter_location',
        'datacenter_region_id',
        'status',
        'cpu',
        'ram',
        'disk',
        'control_panel',
        'db_engine',
        'db_manager',
        'db_name',
        'db_user',
        'db_password',
        'db_port',
        'provider',
        'provider_resource_id',
        'provider_meta',
        'last_reconciled_at',
        'uptime_percent',
        'app_url',
        'app_name',
        'app_guide',
        'billing_cycle',
        'starts_at',
        'expires_at',
        'grace_period_ends_at',
        'auto_renew',
    ];

    public function isAiPackage(): bool
    {
        if ($this->order && $this->order->vpsSpec) {
            return $this->order->vpsSpec->isAiPackage();
        }
        return in_array($this->control_panel, ['vscode_server', 'hermes_agent', 'hermes_omniroute', 'claude_opencode', 'dify_ollama', 'anythingllm', 'ollama'], true)
            || !empty($this->attributes['app_url']);
    }

    public function isDatabasePackage(): bool
    {
        if ($this->order && $this->order->vpsSpec) {
            return $this->order->vpsSpec->isDatabasePackage();
        }
        return in_array($this->control_panel, ['managed_database'], true)
            || !empty($this->attributes['db_engine'])
            || str_starts_with(strtolower((string)$this->hostname), 'vx-db-');
    }

    public function getDbConnectionDriverAttribute(): string
    {
        return match ($this->db_engine) {
            'mysql' => 'mysql',
            'redis' => 'redis',
            'mongodb' => 'mongodb',
            'vector' => 'qdrant',
            default => 'pgsql',
        };
    }

    public function getDbPortResolvedAttribute(): int
    {
        if ($this->db_port) {
            return (int) $this->db_port;
        }
        return match ($this->db_engine) {
            'mysql' => 3306,
            'redis' => 6379,
            'mongodb' => 27017,
            'vector' => 6333,
            default => 5432,
        };
    }

    public function getDatabaseUrlAttribute(): string
    {
        $driver = match ($this->db_engine) {
            'mysql' => 'mysql',
            'redis' => 'redis',
            'mongodb' => 'mongodb',
            'vector' => 'http',
            default => 'postgresql',
        };
        $user = $this->db_user ?? 'admin_vexa';
        $pass = $this->db_password ?: ($this->initial_root_password ?: 'vexapass123');
        $host = $this->public_ip ?: '127.0.0.1';
        $port = $this->db_port_resolved;
        $name = $this->db_name ?? 'vexadb_production';

        if ($this->db_engine === 'redis') {
            return "redis://:{$pass}@{$host}:{$port}/0";
        }
        if ($this->db_engine === 'vector') {
            return "http://{$host}:{$port}";
        }

        return "{$driver}://{$user}:{$pass}@{$host}:{$port}/{$name}";
    }

    public function getWebManagerUrlResolvedAttribute(): ?string
    {
        if ($this->db_manager === 'cli_only') {
            return null;
        }
        $host = $this->public_ip ?: '127.0.0.1';
        return "https://{$host}:8080";
    }

    public function getAppUrlAttribute(): ?string
    {
        if (!empty($this->attributes['app_url'])) {
            return $this->attributes['app_url'];
        }

        if (!$this->public_ip) {
            return null;
        }

        return match ($this->control_panel) {
            'vscode_server' => "http://{$this->public_ip}:8443",
            'hermes_agent', 'hermes_omniroute', 'omniroute', '9router' => "http://{$this->public_ip}:8080",
            'anythingllm', 'ollama', 'dify_ollama' => "http://{$this->public_ip}:3000",
            'claude_opencode' => "http://{$this->public_ip}:7681",
            default => ($this->isAiPackage() ? "http://{$this->public_ip}:8000" : null),
        };
    }

    protected $casts = [
        'initial_root_password' => 'encrypted',
        'root_password_revealed_at' => 'datetime',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'grace_period_ends_at' => 'datetime',
        'last_reconciled_at' => 'datetime',
        'auto_renew' => 'boolean',
        'ssh_port' => 'integer',
        'uptime_percent' => 'float',
        'provider_meta' => 'array',
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * Catatan pembelian di Supplier, satu baris per periode prepaid.
     */
    public function supplierPurchases()
    {
        return $this->hasMany(SupplierPurchase::class)->orderByDesc('purchased_at');
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function activityLogs()
    {
        return $this->hasMany(VpsActivityLog::class, 'vps_instance_id')->latest();
    }

    public function statusHistories()
    {
        return $this->hasMany(VpsStatusHistory::class)->orderBy('created_at', 'desc');
    }

    public function subscription()
    {
        return $this->hasOne(Subscription::class);
    }

    public function datacenterRegion()
    {
        return $this->belongsTo(DatacenterRegion::class);
    }

    public function provisioningTasks()
    {
        return $this->hasMany(ProvisioningTask::class)->orderBy('created_at', 'desc');
    }

    public function latestProvisioningTask()
    {
        return $this->hasOne(ProvisioningTask::class)->latestOfMany();
    }

    public function logActivity(string $action, ?string $description = null, string $status = 'completed', ?int $userId = null): VpsActivityLog
    {
        $resolvedUserId = $userId ?? Auth::id() ?? $this->customer_id;
        $ip = request()?->ip() ?? '127.0.0.1';
        $userAgent = request()?->userAgent();

        return $this->activityLogs()->create([
            'user_id' => $resolvedUserId,
            'action' => $action,
            'description' => $description,
            'status' => $status,
            'ip_address' => $ip,
            'user_agent' => $userAgent,
        ]);
    }

    public function getSshCommandAttribute(): string
    {
        $port = $this->ssh_port ?: 22;
        $ip = $this->public_ip ?: '0.0.0.0';
        return "ssh root@{$ip} -p {$port}";
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'running' => [
                'label' => 'Running',
                'color' => 'emerald',
                'bg' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                'dot' => 'bg-emerald-500',
            ],
            'stopped' => [
                'label' => 'Stopped',
                'color' => 'rose',
                'bg' => 'bg-rose-500/10 text-rose-400 border-rose-500/20',
                'dot' => 'bg-rose-500',
            ],
            'rebooting' => [
                'label' => 'Rebooting',
                'color' => 'amber',
                'bg' => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                'dot' => 'bg-amber-500 animate-pulse',
            ],
            'reinstalling' => [
                'label' => 'Reinstalling OS',
                'color' => 'blue',
                'bg' => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
                'dot' => 'bg-blue-500 animate-pulse',
            ],
            'provisioning' => [
                'label' => 'Provisioning',
                'color' => 'cyan',
                'bg' => 'bg-cyan-500/10 text-cyan-400 border-cyan-500/20',
                'dot' => 'bg-cyan-500 animate-pulse',
            ],
            'suspended' => [
                'label' => 'Suspended',
                'color' => 'red',
                'bg' => 'bg-red-500/10 text-red-400 border-red-500/20',
                'dot' => 'bg-red-500',
            ],
            'terminated' => [
                'label' => 'Terminated',
                'color' => 'zinc',
                'bg' => 'bg-zinc-500/10 text-zinc-400 border-zinc-500/20',
                'dot' => 'bg-zinc-500',
            ],
            default => [
                'label' => ucfirst($this->status),
                'color' => 'slate',
                'bg' => 'bg-slate-500/10 text-slate-400 border-slate-500/20',
                'dot' => 'bg-slate-500',
            ],
        };
    }

    public function isExpiringSoon(int $days = 7): bool
    {
        if (!$this->expires_at) {
            return false;
        }
        return $this->expires_at->isFuture() && $this->expires_at->diffInDays(now()) <= $days;
    }

    public function isExpired(): bool
    {
        if (!$this->expires_at) {
            return false;
        }
        return $this->expires_at->isPast();
    }

    public function isInGracePeriod(): bool
    {
        if (!$this->expires_at || !$this->grace_period_ends_at) {
            return false;
        }
        return $this->expires_at->isPast() && $this->grace_period_ends_at->isFuture();
    }
}
