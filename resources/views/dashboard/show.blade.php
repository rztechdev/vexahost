@extends('layouts.dashboard', ['title' => 'Detail VPS ' . ($vps->hostname ?? $vps->id), 'headerTitle' => 'Kelola Instance VPS', 'backUrl' => route('dashboard.index'), 'backLabel' => 'Kembali ke Daftar VPS'])

@section('content')
<div class="space-y-6" x-data="{
    activeTab: '{{ $vps->isAiPackage() ? 'webapp' : ($vps->isDatabasePackage() ? 'database' : 'overview') }}',
    reinstallModal: false,
    passwordModal: false,
    challengePassword: '',
    passwordError: '',
    passwordLoading: false,
    revealedPassword: '',
    revealedAt: '{{ $vps->root_password_revealed_at ? $vps->root_password_revealed_at->format('d M Y H:i:s') : '' }}',
    showDbPassword: false,
    copyText(text) {
        if (!text) return;
        navigator.clipboard.writeText(text);
        showAlert('Teks disalin ke clipboard: ' + text, { icon: 'success', title: 'Berhasil disalin' });
    },
    openPasswordModal() {
        this.challengePassword = '';
        this.passwordError = '';
        this.passwordModal = true;
    },
    async submitPasswordChallenge() {
        if (!this.challengePassword) {
            this.passwordError = 'Masukkan password akun Anda.';
            return;
        }
        this.passwordLoading = true;
        this.passwordError = '';
        try {
            const token = document.querySelector('meta[name=csrf-token]')?.getAttribute('content');
            const res = await fetch('{{ route('dashboard.vps.reveal-password', $vps->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ password: this.challengePassword })
            });
            const data = await res.json();
            if (res.ok && data.success) {
                this.revealedPassword = data.password;
                this.revealedAt = data.revealed_at;
                this.passwordModal = false;
                this.activeTab = 'access';
            } else {
                this.passwordError = data.message || 'Verifikasi password akun gagal.';
            }
        } catch (err) {
            this.passwordError = 'Terjadi kesalahan sistem. Silakan coba lagi.';
        } finally {
            this.passwordLoading = false;
        }
    }
}">
    <!-- Lifecycle & Status Warning Alerts -->
    @if($vps->status === 'suspended')
        <div class="p-4 rounded-lg bg-amber-50 border border-amber-300 text-amber-900 flex items-start gap-3">
            <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <div class="text-xs">
                <strong class="font-bold text-sm block">Instance VPS Ditangguhkan (Suspended)</strong>
                Layanan VPS ini sedang ditangguhkan. Operasi daya dan akses sistem dibatasi hingga tagihan diperbarui atau status diselesaikan.
            </div>
        </div>
    @elseif($vps->isInGracePeriod())
        <div class="p-4 rounded-lg bg-amber-50 border border-amber-300 text-amber-900 flex items-start gap-3">
            <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div class="text-xs">
                <strong class="font-bold text-sm block">Masa Tenggang Berlangganan (Grace Period)</strong>
                Masa aktif VPS telah jatuh tempo. Anda memiliki masa tenggang hingga <strong>{{ $vps->grace_period_ends_at?->format('d M Y, H:i') }}</strong> sebelum layanan dihentikan otomatis.
            </div>
        </div>
    @elseif($vps->isExpired())
        <div class="p-4 rounded-lg bg-red-50 border border-red-300 text-red-900 flex items-start gap-3">
            <svg class="w-5 h-5 text-red-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div class="text-xs">
                <strong class="font-bold text-sm block">Masa Aktif Berakhir (Expired)</strong>
                Masa aktif server telah kadaluarsa pada {{ $vps->expires_at?->format('d M Y, H:i') }}. Harap segera hubungi billing untuk aktivasi kembali.
            </div>
        </div>
    @endif

    <!-- Server Top Card -->
    <div class="bg-white rounded-lg border border-slate-200 p-6 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-lg bg-slate-100 text-slate-900 flex items-center justify-center font-bold text-lg shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2"/></svg>
            </div>
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-xl font-bold text-slate-900">{{ $vps->hostname ?? 'VPS-' . $vps->id }}</h1>
                    @if($vps->isDatabasePackage())
                        <span class="px-2.5 py-0.5 rounded text-[11px] font-bold uppercase bg-emerald-100 text-emerald-800 border border-emerald-200">
                            Managed DB
                        </span>
                    @elseif($vps->isAiPackage())
                        <span class="px-2.5 py-0.5 rounded text-[11px] font-bold uppercase bg-blue-100 text-blue-800 border border-blue-200">
                            AI Combo
                        </span>
                    @endif
                    @if($vps->status === 'running')
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold uppercase border border-emerald-300 text-emerald-800 bg-emerald-50">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            Running
                        </span>
                    @elseif($vps->status === 'stopped')
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold uppercase border border-slate-300 text-slate-700 bg-slate-100">
                            <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                            Stopped
                        </span>
                    @elseif($vps->status === 'suspended')
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold uppercase border border-amber-300 text-amber-800 bg-amber-50">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                            Suspended
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold uppercase border border-red-300 text-red-800 bg-red-50">
                            <span class="w-2 h-2 rounded-full bg-red-500"></span>
                            {{ ucfirst($vps->status) }}
                        </span>
                    @endif
                </div>
                <div class="flex flex-wrap items-center gap-y-1 gap-x-3 text-xs text-slate-600 mt-1.5">
                    <span>IP: <strong class="text-slate-900 font-mono-code">{{ $vps->public_ip ?? 'Belum dialokasikan' }}</strong></span>
                    <span>&bull;</span>
                    <span>OS: <strong class="text-slate-900">{{ $vps->os_label }}</strong></span>
                    <span>&bull;</span>
                    <span>Provider: <strong class="text-slate-900">{{ $vps->provider_label }}</strong></span>
                    <span>&bull;</span>
                    <span>Stack: <strong class="text-slate-900">{{ $vps->control_panel_label }}</strong></span>
                    <span>&bull;</span>
                    <span>Siklus: <strong class="text-slate-900 uppercase">{{ str_replace('_', ' ', $vps->billing_cycle ?? 'monthly') }}</strong></span>
                </div>
            </div>
        </div>

        <!-- Action Toolbar -->
        <div class="flex flex-wrap items-center gap-2 shrink-0">
            @if($vps->public_ip && $vps->status === 'running')
                @if($vps->isAiPackage() && $vps->app_url)
                    <a href="{{ $vps->app_url }}" target="_blank" rel="noopener" class="px-4 py-2 rounded-lg bg-[#4A6FA5] hover:bg-[#3D5E8C] text-white text-xs font-semibold transition-colors flex items-center gap-1.5 shadow-xs">
                        <span>Buka Web App</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                @elseif($vps->isDatabasePackage() && $vps->web_manager_url_resolved)
                    <a href="{{ $vps->web_manager_url_resolved }}" target="_blank" rel="noopener" class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold transition-colors flex items-center gap-1.5 shadow-xs">
                        <span>Buka Web Manager (Port 8080)</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                @elseif(!$vps->isDatabasePackage())
                    <a href="http://{{ $vps->public_ip }}:8000" target="_blank" rel="noopener" class="px-4 py-2 rounded-lg bg-black hover:bg-neutral-800 text-white text-xs font-semibold transition-colors flex items-center gap-1.5">
                        <span>Panel Web</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                @endif
            @endif

            @if($vps->status === 'stopped')
                <form action="{{ route('dashboard.vps.start', $vps->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold transition-colors flex items-center gap-1.5 shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Nyalakan Server
                    </button>
                </form>
            @elseif($vps->status === 'running')
                <form action="{{ route('dashboard.vps.stop', $vps->id) }}" method="POST" data-confirm="Lakukan graceful shutdown pada server {{ $vps->hostname }}?">
                    @csrf
                    <button type="submit" class="px-4 py-2 rounded-lg border border-slate-300 bg-white hover:bg-slate-100 text-xs font-semibold text-slate-900 transition-colors flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Matikan (Stop)
                    </button>
                </form>

                <form action="{{ route('dashboard.vps.reboot', $vps->id) }}" method="POST" data-confirm="Reboot server {{ $vps->hostname }}?">
                    @csrf
                    <button type="submit" class="px-4 py-2 rounded-lg border border-slate-300 bg-white hover:bg-slate-100 text-xs font-semibold text-slate-900 transition-colors flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        Reboot
                    </button>
                </form>
            @endif

            @if(!in_array($vps->status, ['suspended', 'terminated']))
                <form action="{{ route('dashboard.vps.force-reboot', $vps->id) }}" method="POST" data-confirm="Peringatan: Force reboot mematikan daya secara paksa (hard reset). Lanjutkan?">
                    @csrf
                    <button type="submit" class="px-3 py-2 rounded-lg border border-red-200 bg-red-50 hover:bg-red-100 text-xs font-semibold text-red-700 transition-colors" title="Force Reboot / Hard Reset">
                        Force Reset
                    </button>
                </form>

                <button @click="reinstallModal = true" class="px-4 py-2 rounded-lg border border-slate-300 bg-white hover:bg-slate-100 text-xs font-semibold text-slate-900 transition-colors">
                    Reinstall OS
                </button>
            @endif
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="flex flex-wrap items-center gap-2 text-xs font-semibold pt-1">
        @if($vps->isDatabasePackage())
            <button @click="activeTab = 'database'"
                    :class="activeTab === 'database' ? 'bg-black text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                    class="py-2 px-4 rounded-lg transition-colors flex items-center gap-1.5">
                <span>Koneksi Database (.env)</span>
                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
            </button>
            <button @click="activeTab = 'overview'"
                    :class="activeTab === 'overview' ? 'bg-black text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                    class="py-2 px-4 rounded-lg transition-colors">
                Ringkasan & Metrik
            </button>
            <button @click="activeTab = 'access'"
                    :class="activeTab === 'access' ? 'bg-black text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                    class="py-2 px-4 rounded-lg transition-colors">
                Akses SSH Server
            </button>
        @elseif($vps->isAiPackage())
            <button @click="activeTab = 'webapp'"
                    :class="activeTab === 'webapp' ? 'bg-black text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                    class="py-2 px-4 rounded-lg transition-colors flex items-center gap-1.5">
                <span>Akses Web App</span>
                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
            </button>
            <button @click="activeTab = 'overview'"
                    :class="activeTab === 'overview' ? 'bg-black text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                    class="py-2 px-4 rounded-lg transition-colors">
                Ringkasan & Metrik
            </button>
            <button @click="activeTab = 'access'"
                    :class="activeTab === 'access' ? 'bg-black text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                    class="py-2 px-4 rounded-lg transition-colors">
                Akses SSH (Lanjutan)
            </button>
        @else
            <button @click="activeTab = 'overview'"
                    :class="activeTab === 'overview' ? 'bg-black text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                    class="py-2 px-4 rounded-lg transition-colors">
                Ringkasan & Metrik
            </button>
            <button @click="activeTab = 'access'"
                    :class="activeTab === 'access' ? 'bg-black text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                    class="py-2 px-4 rounded-lg transition-colors">
                Detail Akses & SSH
            </button>
        @endif

        <button @click="activeTab = 'subscription'"
                :class="activeTab === 'subscription' ? 'bg-black text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                class="py-2 px-4 rounded-lg transition-colors">
            Berlangganan & Siklus
        </button>

        @if(!$vps->isAiPackage() && !$vps->isDatabasePackage())
            <button @click="activeTab = 'panel'"
                    :class="activeTab === 'panel' ? 'bg-black text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                    class="py-2 px-4 rounded-lg transition-colors">
                Control Panel
            </button>
        @endif

        <button @click="activeTab = 'logs'"
                :class="activeTab === 'logs' ? 'bg-black text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                class="py-2 px-4 rounded-lg transition-colors flex items-center gap-1.5">
            <span>Audit Trail & Riwayat</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-slate-200 text-slate-800" :class="activeTab === 'logs' ? 'bg-neutral-800 text-white' : ''">
                {{ $vps->activityLogs->count() }}
            </span>
        </button>
    @if($vps->isDatabasePackage())
    @php
        $dbHost = $vps->public_ip ?? '103.xxx.xxx.xxx';
        $dbPort = $vps->db_port_resolved;
        $dbConn = $vps->db_connection_driver;
        $dbName = $vps->db_name ?? 'vexadb_production';
        $dbUser = $vps->db_user ?? 'admin_vexa';
        $dbPass = $vps->db_password ?: ($vps->initial_root_password ?: 'vexapass123');
        $dbUrl = $vps->database_url;
        $webMgrUrl = $vps->web_manager_url_resolved;
        $dbEngineLabel = match($vps->db_engine) {
            'mysql' => 'MySQL 8.0 / MariaDB 11',
            'redis' => 'Redis 7 In-Memory Cache',
            'mongodb' => 'MongoDB 7 Community',
            'vector' => 'Qdrant / pgvector (AI & RAG)',
            default => 'PostgreSQL 16',
        };
        $dbManagerLabel = $vps->db_manager === 'cli_only' ? 'CLI Only (Headless)' : 'CloudBeaver / Adminer Web GUI';

        $fullEnvConfig = "DB_CONNECTION={$dbConn}\n"
                       . "DB_HOST={$dbHost}\n"
                       . "DB_PORT={$dbPort}\n"
                       . "DB_DATABASE={$dbName}\n"
                       . "DB_USERNAME={$dbUser}\n"
                       . "DB_PASSWORD={$dbPass}\n"
                       . "DATABASE_URL={$dbUrl}";
        if ($webMgrUrl) {
            $fullEnvConfig .= "\nWEB_MANAGER_URL={$webMgrUrl} (CloudBeaver)";
        }
    @endphp
    <!-- Tab Database: Kredensial & Connection String (.env) -->
    <div x-show="activeTab === 'database'" class="space-y-6">
        <div class="bg-white rounded-lg border border-slate-200 p-6 space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-base font-bold text-slate-900">Format Connection String Database</h3>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                            {{ $dbEngineLabel }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">Format connection string siap pakai untuk disalin langsung ke file <code class="bg-slate-100 px-1 py-0.5 rounded text-slate-800 font-mono-code">.env</code> aplikasi backend Anda.</p>
                </div>
                <div class="flex items-center gap-2">
                    @if($webMgrUrl && $vps->status === 'running')
                        <a href="{{ $webMgrUrl }}" target="_blank" rel="noopener" class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition-colors flex items-center gap-1.5 shadow-xs">
                            <span>Buka Web GUI</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        </a>
                    @endif
                    <button type="button" @click="copyText(@js($fullEnvConfig))" class="px-4 py-2 rounded-lg bg-black hover:bg-neutral-800 text-white text-xs font-bold transition-colors flex items-center gap-1.5 shadow-xs">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        <span>Salin Format .env</span>
                    </button>
                </div>
            </div>

            <!-- Connection String .env Code Block -->
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-700 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        Konfigurasi .env Aplikasi (Laravel, Node.js, Next.js, Django, FastAPI, Go)
                    </span>
                    <button type="button" @click="showDbPassword = !showDbPassword" class="text-xs font-medium text-slate-600 hover:text-black flex items-center gap-1">
                        <svg x-show="!showDbPassword" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <svg x-show="showDbPassword" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                        <span x-text="showDbPassword ? 'Sembunyikan Password' : 'Lihat Password'"></span>
                    </button>
                </div>

                <div class="relative group">
                    <pre class="bg-slate-950 text-slate-100 p-4 sm:p-5 rounded-xl font-mono-code text-xs sm:text-sm overflow-x-auto leading-relaxed border border-slate-800 shadow-inner select-all"><code>DB_CONNECTION={{ $dbConn }}
DB_HOST={{ $dbHost }}
DB_PORT={{ $dbPort }}
DB_DATABASE={{ $dbName }}
DB_USERNAME={{ $dbUser }}
DB_PASSWORD=<span x-text="showDbPassword ? '{{ $dbPass }}' : '****************'">****************</span>
DATABASE_URL={{ $dbUrl }}
@if($webMgrUrl)
WEB_MANAGER_URL={{ $webMgrUrl }} (CloudBeaver)
@endif</code></pre>
                    <button type="button" @click="copyText(@js($fullEnvConfig))" class="absolute top-3 right-3 px-3 py-1.5 rounded-md bg-slate-800 hover:bg-slate-700 text-white text-xs font-medium transition-colors shadow-sm flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        <span>Salin .env</span>
                    </button>
                </div>
            </div>

            <!-- Detail Parameter Database (Grid 3 Kolom) -->
            <div class="space-y-3 pt-2">
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500">Rincian Parameter Koneksi</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    {{-- DB_CONNECTION --}}
                    <div class="p-3.5 rounded-lg border border-slate-200 bg-slate-50/70 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] text-slate-400 font-bold uppercase block">DB_CONNECTION</span>
                            <span class="font-mono-code font-bold text-slate-900 text-xs">{{ $dbConn }}</span>
                        </div>
                        <button @click="copyText('{{ $dbConn }}')" class="p-1 rounded text-slate-400 hover:text-black hover:bg-white" title="Salin">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        </button>
                    </div>

                    {{-- DB_HOST --}}
                    <div class="p-3.5 rounded-lg border border-slate-200 bg-slate-50/70 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] text-slate-400 font-bold uppercase block">DB_HOST</span>
                            <span class="font-mono-code font-bold text-slate-900 text-xs">{{ $dbHost }}</span>
                        </div>
                        <button @click="copyText('{{ $dbHost }}')" class="p-1 rounded text-slate-400 hover:text-black hover:bg-white" title="Salin">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        </button>
                    </div>

                    {{-- DB_PORT --}}
                    <div class="p-3.5 rounded-lg border border-slate-200 bg-slate-50/70 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] text-slate-400 font-bold uppercase block">DB_PORT</span>
                            <span class="font-mono-code font-bold text-slate-900 text-xs">{{ $dbPort }}</span>
                        </div>
                        <button @click="copyText('{{ $dbPort }}')" class="p-1 rounded text-slate-400 hover:text-black hover:bg-white" title="Salin">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        </button>
                    </div>

                    {{-- DB_DATABASE --}}
                    <div class="p-3.5 rounded-lg border border-slate-200 bg-slate-50/70 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] text-slate-400 font-bold uppercase block">DB_DATABASE</span>
                            <span class="font-mono-code font-bold text-slate-900 text-xs">{{ $dbName }}</span>
                        </div>
                        <button @click="copyText('{{ $dbName }}')" class="p-1 rounded text-slate-400 hover:text-black hover:bg-white" title="Salin">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        </button>
                    </div>

                    {{-- DB_USERNAME --}}
                    <div class="p-3.5 rounded-lg border border-slate-200 bg-slate-50/70 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] text-slate-400 font-bold uppercase block">DB_USERNAME</span>
                            <span class="font-mono-code font-bold text-slate-900 text-xs">{{ $dbUser }}</span>
                        </div>
                        <button @click="copyText('{{ $dbUser }}')" class="p-1 rounded text-slate-400 hover:text-black hover:bg-white" title="Salin">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        </button>
                    </div>

                    {{-- DB_PASSWORD --}}
                    <div class="p-3.5 rounded-lg border border-slate-200 bg-slate-50/70 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] text-slate-400 font-bold uppercase block">DB_PASSWORD</span>
                            <span class="font-mono-code font-bold text-slate-900 text-xs" x-text="showDbPassword ? '{{ $dbPass }}' : '••••••••••••••••'"></span>
                        </div>
                        <div class="flex items-center gap-1">
                            <button @click="showDbPassword = !showDbPassword" class="p-1 rounded text-slate-400 hover:text-black hover:bg-white" title="Lihat/Sembunyikan">
                                <svg x-show="!showDbPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <svg x-show="showDbPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                            </button>
                            <button @click="copyText('{{ $dbPass }}')" class="p-1 rounded text-slate-400 hover:text-black hover:bg-white" title="Salin Password">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            </button>
                        </div>
                    </div>

                    {{-- DATABASE_URL --}}
                    <div class="sm:col-span-2 lg:col-span-2 p-3.5 rounded-lg border border-slate-200 bg-slate-50/70 flex items-center justify-between gap-2">
                        <div class="min-w-0 flex-1">
                            <span class="text-[10px] text-slate-400 font-bold uppercase block">DATABASE_URL</span>
                            <span class="font-mono-code font-bold text-slate-900 text-xs truncate block">{{ $dbUrl }}</span>
                        </div>
                        <button @click="copyText('{{ $dbUrl }}')" class="p-1 rounded text-slate-400 hover:text-black hover:bg-white shrink-0" title="Salin URL">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        </button>
                    </div>

                    {{-- WEB_MANAGER_URL --}}
                    <div class="p-3.5 rounded-lg border border-slate-200 bg-slate-50/70 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] text-slate-400 font-bold uppercase block">WEB_MANAGER_URL</span>
                            @if($webMgrUrl)
                                <span class="font-mono-code font-bold text-emerald-700 text-xs">{{ $webMgrUrl }}</span>
                            @else
                                <span class="text-slate-400 text-xs font-mono-code">CLI Only (Disabled)</span>
                            @endif
                        </div>
                        @if($webMgrUrl)
                            <button @click="copyText('{{ $webMgrUrl }}')" class="p-1 rounded text-slate-400 hover:text-black hover:bg-white" title="Salin">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Panduan Remote Database Client -->
            <div class="mt-4 p-4 rounded-lg bg-emerald-50/60 border border-emerald-200 space-y-3">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <h4 class="font-bold text-slate-900 text-xs uppercase tracking-wider">Panduan Remote Database Client</h4>
                </div>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Server ini terisolasi khusus database di Datacenter Jakarta untuk menjaga latensi minimal (~3–10ms), anti-crash memory swap otomatis, dan proteksi penuh. Anda dapat menghubungkan aplikasi backend (Vercel, Railway, VPS luar) atau remote client (DBeaver, TablePlus, DataGrip, Navicat, psql, mysql client) via Dedicated IP Publik Static server:
                </p>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 text-xs">
                    <div class="bg-white p-2.5 rounded border border-emerald-100">
                        <span class="text-slate-400 block text-[10px]">PostgreSQL</span>
                        <span class="font-mono-code font-bold text-slate-800">Port 5432</span>
                    </div>
                    <div class="bg-white p-2.5 rounded border border-emerald-100">
                        <span class="text-slate-400 block text-[10px]">MySQL / MariaDB</span>
                        <span class="font-mono-code font-bold text-slate-800">Port 3306</span>
                    </div>
                    <div class="bg-white p-2.5 rounded border border-emerald-100">
                        <span class="text-slate-400 block text-[10px]">Redis Cache</span>
                        <span class="font-mono-code font-bold text-slate-800">Port 6379</span>
                    </div>
                    <div class="bg-white p-2.5 rounded border border-emerald-100">
                        <span class="text-slate-400 block text-[10px]">Vector Qdrant</span>
                        <span class="font-mono-code font-bold text-slate-800">Port 6333</span>
                    </div>
                </div>
                <p class="text-[11px] text-slate-500">
                    Disarankan menggunakan SSH Tunneling (<code class="bg-white px-1 py-0.5 rounded border border-slate-200 text-slate-800 font-mono-code">ssh -L {{ $dbPort }}:localhost:{{ $dbPort }} root&#64;{{ $dbHost }}</code>) untuk keamanan maksimal di lingkungan production.
                </p>
            </div>
        </div>
    </div>
    @endif

    @if($vps->isAiPackage())
    <!-- Tab 0: Akses Web App (Khusus Paket AI Combo) -->
    <div x-show="activeTab === 'webapp'" class="space-y-6">
        <div class="bg-white rounded-lg border border-slate-200 p-6 space-y-5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Akses Web App {{ $vps->app_name ?? $vps->control_panel_label }}</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Tautan langsung menuju antarmuka runtime AI yang telah aktif di server Anda.</p>
                </div>
                @if($vps->app_url && $vps->status === 'running')
                    <a href="{{ $vps->app_url }}" target="_blank" rel="noopener" class="px-4 py-2 rounded-lg bg-black hover:bg-neutral-800 text-white text-xs font-bold transition-colors flex items-center gap-2 self-start sm:self-auto">
                        <span>Buka Web App di Tab Baru</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                @endif
            </div>

            <!-- Web App URL Box -->
            <div class="p-4 rounded-lg bg-slate-50 border border-slate-200 space-y-3">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <span class="text-xs font-semibold text-slate-600">URL Akses Web:</span>
                    <div class="flex items-center gap-2">
                        <span class="font-mono-code font-bold text-slate-900 text-xs sm:text-sm bg-white px-3 py-1 rounded border border-slate-200 select-all">
                            {{ $vps->app_url ?? 'Sedang mengalokasikan...' }}
                        </span>
                        @if($vps->app_url)
                            <button @click="copyText('{{ $vps->app_url }}')" class="px-3 py-1 rounded border border-slate-300 bg-white hover:bg-slate-100 text-xs font-semibold text-slate-900" title="Salin URL">
                                Salin
                            </button>
                        @endif
                    </div>
                </div>
                <p class="text-[11px] text-slate-500">
                    Akses URL di atas melalui browser modern (Chrome, Safari, Firefox, Edge).
                </p>
            </div>

            <!-- Panduan Penggunaan Berdasarkan Paket/Stack -->
            <div class="space-y-3 pt-2">
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500">Panduan Penggunaan Cepat</h4>

                @if($vps->control_panel === 'claude_opencode')
                    <div class="p-4 rounded-lg border border-slate-200 bg-white space-y-2 text-xs text-slate-700">
                        <p class="font-semibold text-slate-900">Combo Terminal Coding Agent:</p>
                        <ol class="list-decimal list-inside space-y-1.5 text-slate-600">
                            <li>Buka Web Terminal melalui URL di atas atau sambungkan terminal lokal Anda melalui SSH.</li>
                            <li>Ketik <code class="bg-slate-100 px-1.5 py-0.5 rounded text-slate-900 font-mono-code">claude</code> untuk menjalankan Claude Code CLI resmi Anthropic.</li>
                            <li>Ketik <code class="bg-slate-100 px-1.5 py-0.5 rounded text-slate-900 font-mono-code">opencode</code> jika ingin menggunakan multi-provider AI model open-source.</li>
                            <li>Masukkan API Key Anda saat pertama kali diminta. Environment Docker, Git, Node.js LTS, dan Python 3.12 sudah terpasang.</li>
                        </ol>
                    </div>
                @elseif($vps->control_panel === 'vscode_server')
                    <div class="p-4 rounded-lg border border-slate-200 bg-white space-y-2 text-xs text-slate-700">
                        <p class="font-semibold text-slate-900">Combo Cloud AI Workstation:</p>
                        <ol class="list-decimal list-inside space-y-1.5 text-slate-600">
                            <li>Buka URL Web App di atas langsung dari browser perangkat Anda (laptop, tablet/iPad).</li>
                            <li>Masukkan password VPS Anda jika diminta untuk membuka sesi Web VS Code Server.</li>
                            <li>Buka integrated terminal di VS Code (<code class="bg-slate-100 px-1.5 py-0.5 rounded text-slate-900 font-mono-code">Ctrl + `</code>) dan jalankan <code class="bg-slate-100 px-1.5 py-0.5 rounded text-slate-900 font-mono-code">claude</code>.</li>
                        </ol>
                    </div>
                @elseif(in_array($vps->control_panel, ['hermes_omniroute', 'hermes_agent', 'omniroute', '9router']))
                    <div class="p-4 rounded-lg border border-slate-200 bg-white space-y-2 text-xs text-slate-700">
                        <p class="font-semibold text-slate-900">Combo Hermes Autonomous Hub:</p>
                        <ol class="list-decimal list-inside space-y-1.5 text-slate-600">
                            <li>Buka antarmuka OmniRoute di URL di atas untuk mengonfigurasi routing AI providers dan API keys.</li>
                            <li>Hermes Agent runtime telah berjalan di background sebagai service 24/7.</li>
                            <li>Semua task dan automasi akan diproses secara otomatis melalui proxy routing terpadu.</li>
                        </ol>
                    </div>
                @else
                    <div class="p-4 rounded-lg border border-slate-200 bg-white space-y-2 text-xs text-slate-700">
                        <p class="font-semibold text-slate-900">Combo Enterprise Private AI &amp; RAG:</p>
                        <ol class="list-decimal list-inside space-y-1.5 text-slate-600">
                            <li>Buka URL Web App di browser Anda untuk mengakses dashboard studio AI.</li>
                            <li>Buat Knowledge Base baru dan unggah dokumen PDF / data privat organisasi Anda.</li>
                            <li>Pilih local model via Ollama yang telah terintegrasi di server. Data tersimpan di server privat Anda.</li>
                        </ol>
                    </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- Tab 1: Overview & Metrics -->
    <div x-show="activeTab === 'overview'" class="space-y-6">
        <!-- Resource Metrics -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg border border-slate-200 p-5">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-xs text-slate-500 font-semibold">CPU</span>
                    <span class="text-xs font-bold text-slate-900 font-mono-code">
                        @if($cpuUsage !== null)
                            {{ $cpuUsage }}%
                        @else
                            <span class="text-slate-400">—</span>
                        @endif
                    </span>
                </div>
                <div class="w-full bg-slate-100 rounded h-2 mb-2">
                    <div class="bg-black h-2 rounded transition-all duration-500" style="width: {{ $cpuUsage ?? 0 }}%"></div>
                </div>
                <p class="text-xs text-slate-500">{{ $cpuCores }} vCPU Dedicated Core</p>
            </div>

            <div class="bg-white rounded-lg border border-slate-200 p-5">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-xs text-slate-500 font-semibold">RAM</span>
                    <span class="text-xs font-bold text-slate-900 font-mono-code">
                        @if($ramPercent !== null)
                            {{ $ramPercent }}%
                        @else
                            <span class="text-slate-400">—</span>
                        @endif
                    </span>
                </div>
                <div class="w-full bg-slate-100 rounded h-2 mb-2">
                    <div class="bg-black h-2 rounded transition-all duration-500" style="width: {{ $ramPercent ?? 0 }}%"></div>
                </div>
                <p class="text-xs text-slate-500">
                    @if($ramUsedGb !== null)
                        {{ $ramUsedGb }} GB dari {{ $ramGb }} GB terpakai
                    @else
                        {{ $ramGb }} GB (data monitoring belum tersedia)
                    @endif
                </p>
            </div>

            <div class="bg-white rounded-lg border border-slate-200 p-5">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-xs text-slate-500 font-semibold">NVMe Storage</span>
                    <span class="text-xs font-bold text-slate-900 font-mono-code">
                        @if($diskPercent !== null)
                            {{ $diskPercent }}%
                        @else
                            <span class="text-slate-400">—</span>
                        @endif
                    </span>
                </div>
                <div class="w-full bg-slate-100 rounded h-2 mb-2">
                    <div class="bg-black h-2 rounded transition-all duration-500" style="width: {{ $diskPercent ?? 0 }}%"></div>
                </div>
                <p class="text-xs text-slate-500">
                    @if($diskUsedGb !== null)
                        {{ $diskUsedGb }} GB dari {{ $diskGb }} GB SSD
                    @else
                        {{ $diskGb }} GB SSD (data monitoring belum tersedia)
                    @endif
                </p>
            </div>

            <div class="bg-white rounded-lg border border-slate-200 p-5">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-xs text-slate-500 font-semibold">Status Server</span>
                    @php $badge = $vps->statusBadge; @endphp
                    <span class="text-xs font-bold px-2 py-0.5 rounded border {{ $badge['bg'] }}">{{ $badge['label'] }}</span>
                </div>
                <div class="w-full bg-slate-100 rounded h-2 mb-2">
                    <div class="h-2 rounded transition-all duration-500 {{ $isRunning ? 'bg-emerald-500' : 'bg-slate-300' }}" style="width: {{ $isRunning ? '100' : '0' }}%"></div>
                </div>
                <p class="text-xs text-slate-500">
                    SLA Garansi: 99.9% Uptime
                </p>
            </div>
        </div>

        <!-- Specifications -->
        <div class="bg-white rounded-lg border border-slate-200 p-6">
            <h3 class="text-base font-bold text-slate-900 mb-4">Spesifikasi Virtual Machine</h3>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 text-xs">
                <div>
                    <span class="text-slate-400 block mb-1">Compute Processor:</span>
                    <p class="font-bold text-slate-900 text-sm">VexaCloud vCPU</p>
                    <p class="text-slate-500">{{ $vps->cpu ?? 1 }} Core Dedicated</p>
                </div>

                <div>
                    <span class="text-slate-400 block mb-1">Memori RAM:</span>
                    <p class="font-bold text-slate-900 text-sm">{{ $vps->ram ?? 1 }} GB DDR4/DDR5</p>
                    <p class="text-slate-500">Unbuffered ECC</p>
                </div>

                <div>
                    <span class="text-slate-400 block mb-1">Penyimpanan Utama:</span>
                    <p class="font-bold text-slate-900 text-sm">{{ $vps->disk ?? 20 }} GB NVMe SSD</p>
                    <p class="text-slate-500">High IOPS Enterprise Storage</p>
                </div>

                <div>
                    <span class="text-slate-400 block mb-1">Konektivitas Port:</span>
                    <p class="font-bold text-slate-900 text-sm">30–100 Mbps Port</p>
                    <p class="text-slate-500">Unmetered Fair-share Traffic</p>
                </div>

                <div>
                    <span class="text-slate-400 block mb-1">Lokasi Node Cluster:</span>
                    <p class="font-bold text-slate-900 text-sm">Singapore / Jakarta</p>
                    <p class="text-slate-500">Tier-3 Enterprise Datacenter</p>
                </div>

                <div>
                    <span class="text-slate-400 block mb-1">Hypervisor Virtualisasi:</span>
                    <p class="font-bold text-slate-900 text-sm">KVM</p>
                    <p class="text-slate-500">Hardware Isolated Kernel</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab 2: Access & SSH with Secure Password Challenge Reveal -->
    <div x-show="activeTab === 'access'" class="space-y-6" style="display: none;">
        <div class="bg-white rounded-lg border border-slate-200 p-6 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Kredensial Akses SSH</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Kredensial root dienkripsi dengan standar AES-256-CBC at rest.</p>
                </div>
                <span class="px-2.5 py-1 rounded text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-300">
                    Port 22 (SSH)
                </span>
            </div>

            <div class="space-y-3 text-xs divide-y divide-slate-100 pt-2">
                <div class="flex justify-between items-center py-2">
                    <span class="text-slate-500 font-medium">Dedicated Public IPv4:</span>
                    <div class="flex items-center gap-2">
                        <span class="font-mono-code font-bold text-slate-900 text-sm">{{ $vps->public_ip ?? '127.0.0.1' }}</span>
                        <button @click="copyText('{{ $vps->public_ip }}')" class="p-1 rounded text-slate-400 hover:text-black hover:bg-slate-100" title="Salin IP">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        </button>
                    </div>
                </div>

                <div class="flex justify-between items-center py-2">
                    <span class="text-slate-500 font-medium">Private Subnet IP:</span>
                    <span class="font-mono-code text-slate-900">{{ $vps->private_ip ?? '—' }}</span>
                </div>

                <div class="flex justify-between items-center py-2">
                    <span class="text-slate-500 font-medium">Username Root:</span>
                    <span class="font-mono-code text-slate-900 font-bold">root</span>
                </div>

                <!-- Secure Root Password Section -->
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center py-3 gap-2">
                    <div>
                        <span class="text-slate-500 font-medium block">Password Root Awal:</span>
                        <span class="text-[11px] text-slate-400">Memerlukan verifikasi password akun sebelum ditampilkan.</span>
                    </div>

                    <div class="flex items-center gap-2">
                        <template x-if="!revealedPassword">
                            <div class="flex items-center gap-2">
                                <span class="font-mono-code text-slate-400 font-bold tracking-widest text-sm bg-slate-50 px-3 py-1.5 rounded border border-slate-200">
                                    ••••••••••••••••
                                </span>
                                <button @click="openPasswordModal()" class="px-3.5 py-1.5 rounded-lg bg-black hover:bg-neutral-800 text-white text-xs font-semibold flex items-center gap-1.5 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    Tampilkan Password
                                </button>
                            </div>
                        </template>

                        <template x-if="revealedPassword">
                            <div class="flex items-center gap-2">
                                <span class="font-mono-code font-bold text-slate-900 text-sm bg-emerald-50 text-emerald-900 px-3 py-1.5 rounded border border-emerald-200 select-all" x-text="revealedPassword"></span>
                                <button @click="copyText(revealedPassword)" class="px-3 py-1.5 rounded-lg border border-slate-300 bg-white hover:bg-slate-100 text-xs font-semibold text-slate-900">
                                    Salin
                                </button>
                                <span class="text-[11px] text-slate-400" x-text="'Diakses: ' + revealedAt"></span>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="pt-4">
                    <span class="text-slate-600 block mb-2 font-medium">Perintah Terminal Cepat:</span>
                    <div class="flex items-center justify-between p-3.5 rounded-lg bg-black text-white font-mono-code text-xs">
                        <span>{{ $vps->ssh_command }}</span>
                        <button @click="copyText('{{ $vps->ssh_command }}')" class="px-3 py-1 rounded bg-neutral-800 hover:bg-neutral-700 text-white font-sans text-xs">
                            Salin Perintah
                        </button>
                    </div>
                </div>

                @if($vps->isDatabasePackage())
                    <div class="mt-4 p-4 rounded-lg bg-emerald-50 border border-emerald-200 space-y-3">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <h4 class="font-bold text-slate-900 text-xs uppercase tracking-wider">Panduan Remote Database Client</h4>
                        </div>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Server ini terisolasi khusus database di Datacenter Jakarta untuk menjaga latensi minimal (~3–10ms), anti-crash memory swap otomatis, dan proteksi penuh. Anda dapat menghubungkan aplikasi backend (Vercel, Railway, VPS luar) atau remote client (DBeaver, TablePlus, DataGrip, Navicat, psql, mysql client) via Dedicated IP Publik Static server:
                        </p>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 text-xs">
                            <div class="bg-white p-2.5 rounded border border-emerald-100">
                                <span class="text-slate-400 block text-[10px]">PostgreSQL</span>
                                <span class="font-mono-code font-bold text-slate-800">Port 5432</span>
                            </div>
                            <div class="bg-white p-2.5 rounded border border-emerald-100">
                                <span class="text-slate-400 block text-[10px]">MySQL / MariaDB</span>
                                <span class="font-mono-code font-bold text-slate-800">Port 3306</span>
                            </div>
                            <div class="bg-white p-2.5 rounded border border-emerald-100">
                                <span class="text-slate-400 block text-[10px]">Redis Cache</span>
                                <span class="font-mono-code font-bold text-slate-800">Port 6379</span>
                            </div>
                            <div class="bg-white p-2.5 rounded border border-emerald-100">
                                <span class="text-slate-400 block text-[10px]">Vector Qdrant</span>
                                <span class="font-mono-code font-bold text-slate-800">Port 6333</span>
                            </div>
                        </div>
                        <div class="bg-white p-3 rounded border border-emerald-100 space-y-1.5 text-xs">
                            <span class="text-slate-600 font-semibold block text-[11px]">Contoh Connection String (.env):</span>
                            <div class="p-2 rounded bg-slate-900 text-slate-100 font-mono-code text-[11px] overflow-x-auto">
                                DATABASE_URL="postgresql://username:password&#64;{{ $vps->public_ip }}:5432/database_name"
                            </div>
                            <p class="text-[11px] text-slate-500">
                                Kompatibel langsung dengan Vercel, Supabase client, Laravel, Next.js, FastAPI, Prisma, DBeaver, TablePlus, dan Navicat.
                            </p>
                        </div>
                        <p class="text-[11px] text-slate-500">
                            Disarankan menggunakan SSH Tunneling (<code class="bg-white px-1 py-0.5 rounded border border-slate-200 text-slate-800 font-mono-code">ssh -L 5432:localhost:5432 root&#64;{{ $vps->public_ip }}</code>) untuk keamanan maksimal.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Tab 3: Subscription Lifecycle & Billing Cycle Card -->
    <div x-show="activeTab === 'subscription'" class="space-y-6" style="display: none;">
        <div class="bg-white rounded-lg border border-slate-200 p-6 space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Siklus Tagihan & Masa Aktif</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Informasi masa berlangganan, grace period, dan perpanjangan otomatis.</p>
                </div>
                <a href="{{ route('dashboard.billing') }}" class="px-4 py-2 rounded-lg border border-slate-300 hover:bg-slate-100 text-slate-900 text-xs font-semibold transition-colors">
                    Lihat Riwayat Invoice &rarr;
                </a>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="p-4 rounded-lg bg-slate-50 border border-slate-200">
                    <span class="text-xs text-slate-500 block mb-1">Siklus Berlangganan</span>
                    <p class="font-bold text-slate-900 text-sm uppercase">{{ str_replace('_', ' ', $vps->billing_cycle ?? 'monthly') }}</p>
                    <p class="text-[11px] text-slate-500 mt-1">
                        @if(($vps->billing_cycle ?? 'monthly') === 'annual')
                            Diskon 15% diterapkan
                        @elseif(($vps->billing_cycle ?? 'monthly') === 'semi_annual')
                            Diskon 10% diterapkan
                        @elseif(($vps->billing_cycle ?? 'monthly') === 'quarterly')
                            Diskon 5% diterapkan
                        @else
                            Tagihan bulanan standar
                        @endif
                    </p>
                </div>

                <div class="p-4 rounded-lg bg-slate-50 border border-slate-200">
                    <span class="text-xs text-slate-500 block mb-1">Tanggal Mulai Layanan</span>
                    <p class="font-bold text-slate-900 text-sm">{{ $vps->starts_at ? $vps->starts_at->format('d M Y') : '-' }}</p>
                    <p class="text-[11px] text-slate-500 mt-1">{{ $vps->starts_at ? $vps->starts_at->format('H:i T') : '' }}</p>
                </div>

                <div class="p-4 rounded-lg bg-slate-50 border border-slate-200">
                    <span class="text-xs text-slate-500 block mb-1">Tanggal Jatuh Tempo (Expiry)</span>
                    <p class="font-bold text-slate-900 text-sm {{ $vps->isExpired() ? 'text-red-600' : '' }}">
                        {{ $vps->expires_at ? $vps->expires_at->format('d M Y') : '30 Hari' }}
                    </p>
                    <p class="text-[11px] text-slate-500 mt-1">
                        @if($vps->isExpired())
                            <span class="text-red-600 font-semibold">Telah Kadaluarsa</span>
                        @elseif($vps->expires_at)
                            Sisa {{ now()->diffInDays($vps->expires_at, false) }} hari lagi
                        @endif
                    </p>
                </div>

                <div class="p-4 rounded-lg bg-slate-50 border border-slate-200">
                    <span class="text-xs text-slate-500 block mb-1">Batas Masa Tenggang</span>
                    <p class="font-bold text-slate-900 text-sm">
                        {{ $vps->grace_period_ends_at ? $vps->grace_period_ends_at->format('d M Y') : '+7 Hari' }}
                    </p>
                    <p class="text-[11px] text-slate-500 mt-1">
                        Grace Period: 7 hari pasca-expiry
                    </p>
                </div>
            </div>

            <div class="p-4 rounded-lg border border-slate-200 bg-white flex flex-col sm:flex-row sm:items-center justify-between gap-4 text-xs">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center font-bold text-slate-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    </div>
                    <div>
                        <strong class="text-slate-900 block font-semibold">Perpanjangan Otomatis (Auto-Renewal)</strong>
                        <span class="text-slate-500">Status saat ini: {{ $vps->auto_renew ? 'Aktif (Otomatis ditagih saat jatuh tempo)' : 'Non-aktif (Pemberitahuan manual)' }}</span>
                    </div>
                </div>
                <span class="px-3 py-1 rounded-full font-semibold {{ $vps->auto_renew ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-300' }}">
                    {{ $vps->auto_renew ? 'Enabled' : 'Disabled' }}
                </span>
            </div>
        </div>
    </div>

    <!-- Tab 4: Control Panel -->
    <div x-show="activeTab === 'panel'" class="space-y-6" style="display: none;">
        <div class="bg-white rounded-lg border border-slate-200 p-6 space-y-4">
            <h3 class="text-base font-bold text-slate-900 mb-1">Akses Stack Server</h3>
            <p class="text-xs text-slate-600 leading-relaxed">
                Stack server (<strong class="text-slate-900">{{ $vps->control_panel_label }}</strong>) terpasang pada port standar:
            </p>

            <div class="p-4 rounded-lg bg-slate-50 border border-slate-200 text-xs space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-slate-700 font-medium">URL Dashboard Panel:</span>
                    <a href="http://{{ $vps->public_ip }}:8000" target="_blank" rel="noopener" class="font-mono-code font-bold text-slate-900 underline">
                        http://{{ $vps->public_ip }}:8000 &rarr;
                    </a>
                </div>
                <p class="text-xs text-slate-500">Buka URL di atas untuk membuat akun administrator master.</p>
            </div>

            <div class="flex gap-3 pt-2">
                <a href="http://{{ $vps->public_ip }}:8000" target="_blank" rel="noopener" class="px-5 py-2.5 rounded-lg bg-black hover:bg-neutral-800 text-white font-semibold text-xs transition-colors">
                    Buka Control Panel
                </a>
                <a href="{{ route('dashboard.support') }}" class="px-5 py-2.5 rounded-lg border border-slate-300 hover:bg-slate-100 text-slate-900 font-semibold text-xs transition-colors">
                    Bantuan Setup
                </a>
            </div>
        </div>
    </div>

    <!-- Tab 5: Audit Trail & Activity Log -->
    <div x-show="activeTab === 'logs'" class="space-y-6" style="display: none;">
        <div class="bg-white rounded-lg border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-200 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Audit Trail & Riwayat Aktivitas Server</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Mencatat 25 riwayat lifecycle event, eksekusi daya, dan audit keamanan instance.</p>
                </div>
                <span class="text-xs font-semibold px-2.5 py-1 rounded bg-slate-100 text-slate-700 border border-slate-300">
                    Immutable Audit Log
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold uppercase tracking-wider">
                        <tr>
                            <th class="py-3 px-4">Waktu</th>
                            <th class="py-3 px-4">Aksi</th>
                            <th class="py-3 px-4">Deskripsi</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4">IP Address</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($vps->activityLogs as $log)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-3 px-4 whitespace-nowrap text-slate-600 font-mono-code">
                                    {{ $log->created_at->format('d M Y, H:i:s') }}
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap">
                                    @if(in_array($log->action, ['start', 'provision']))
                                        <span class="px-2 py-0.5 rounded font-mono-code text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 uppercase">
                                            {{ $log->action }}
                                        </span>
                                    @elseif(in_array($log->action, ['stop', 'terminate']))
                                        <span class="px-2 py-0.5 rounded font-mono-code text-[11px] font-semibold bg-red-50 text-red-700 border border-red-200 uppercase">
                                            {{ $log->action }}
                                        </span>
                                    @elseif(in_array($log->action, ['reboot', 'force_reboot']))
                                        <span class="px-2 py-0.5 rounded font-mono-code text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-200 uppercase">
                                            {{ $log->action }}
                                        </span>
                                    @elseif($log->action === 'password_reveal')
                                        <span class="px-2 py-0.5 rounded font-mono-code text-[11px] font-semibold bg-purple-50 text-purple-700 border border-purple-200 uppercase">
                                            {{ $log->action }}
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded font-mono-code text-[11px] font-semibold bg-slate-100 text-slate-700 border border-slate-300 uppercase">
                                            {{ $log->action }}
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-slate-800">
                                    {{ $log->description }}
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold uppercase {{ $log->status === 'completed' ? 'bg-slate-100 text-slate-800' : 'bg-amber-100 text-amber-800' }}">
                                        {{ $log->status }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap text-slate-500 font-mono-code">
                                    {{ $log->ip_address ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-500">
                                    Belum ada catatan aktivitas untuk instance ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Security Challenge Modal: Password Reveal -->
    <div x-show="passwordModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" style="display: none;">
        <div class="bg-white rounded-lg border border-slate-200 p-6 max-w-md w-full shadow-xl" @click.away="passwordModal = false">
            <div class="flex items-start gap-3 mb-3">
                <div class="w-10 h-10 rounded-lg bg-slate-100 text-slate-900 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">Verifikasi Keamanan Akun</h3>
                    <p class="text-xs text-slate-500">Masukkan password akun Anda untuk mendekripsi dan menampilkan kredensial root instance.</p>
                </div>
            </div>

            <template x-if="passwordError">
                <div class="mb-4 p-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-xs font-medium" x-text="passwordError"></div>
            </template>

            <form @submit.prevent="submitPasswordChallenge()" class="space-y-4 text-xs">
                <div>
                    <label class="block font-medium text-slate-700 mb-1">Password Akun VexaHost Anda</label>
                    <input type="password" x-model="challengePassword" required placeholder="Masukkan password login Anda..." class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:outline-none focus:ring-1 focus:ring-black bg-white">
                </div>

                <p class="text-[11px] text-slate-500">
                    Akses ini akan dicatat ke dalam audit trail keamanan server bersama IP address Anda.
                </p>

                <div class="pt-2 flex items-center justify-end gap-2">
                    <button type="button" @click="passwordModal = false" class="px-4 py-2 rounded-lg text-slate-700 hover:bg-slate-100 font-medium">
                        Batal
                    </button>
                    <button type="submit" :disabled="passwordLoading" class="px-4 py-2 rounded-lg bg-black hover:bg-neutral-800 text-white font-bold flex items-center gap-1.5 transition-colors">
                        <span x-show="!passwordLoading">Konfirmasi & Buka</span>
                        <span x-show="passwordLoading">Memverifikasi...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Reinstall Modal -->
    <div x-show="reinstallModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40" style="display: none;">
        <div class="bg-white rounded-lg border border-slate-200 p-6 max-w-md w-full shadow-xl" @click.away="reinstallModal = false">
            <h3 class="text-base font-bold text-slate-900 mb-2">Reinstall Sistem Operasi VPS</h3>
            <p class="text-xs text-slate-700 bg-slate-100 p-3 rounded-lg border border-slate-300 mb-4 font-medium">
                Peringatan: Seluruh data pada disk akan diformat ulang secara permanen. Pastikan Anda sudah membackup data sebelum melanjutkan.
            </p>

            <form action="{{ route('dashboard.vps.reinstall', $vps->id) }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-medium text-slate-700 mb-1">Pilih Distribusi OS Baru</label>
                    <select name="os" class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:outline-none bg-white">
                        <option value="Ubuntu 24.04 LTS">Ubuntu 24.04 LTS</option>
                        <option value="Ubuntu 22.04 LTS">Ubuntu 22.04 LTS</option>
                        <option value="Debian 12">Debian 12 Bookworm</option>
                    </select>
                </div>

                <div>
                    <label class="block font-medium text-slate-700 mb-1">Pilih Control Panel</label>
                    <select name="control_panel" class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:outline-none bg-white">
                        <option value="coolify">Coolify PaaS</option>
                        <option value="dokploy">Dokploy Docker Manager</option>
                        <option value="cpanel">CloudPanel / cPanel</option>
                        <option value="hermes_omniroute">Hermes AI Stack</option>
                    </select>
                </div>

                <div class="pt-4 flex items-center justify-end gap-2">
                    <button type="button" @click="reinstallModal = false" class="px-4 py-2 rounded-lg text-slate-700 hover:bg-slate-100 font-medium">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-black hover:bg-neutral-800 text-white font-bold">
                        Konfirmasi Reinstall
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
