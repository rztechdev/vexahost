@extends('layouts.dashboard', ['title' => 'Dashboard', 'headerTitle' => 'VPS Instances Saya'])

@section('content')
<div class="space-y-6" x-data="{
    copyIp(ip) {
        if (!ip) return;
        navigator.clipboard.writeText(ip);
        showAlert('IP ' + ip + ' disalin ke clipboard', { icon: 'success', title: 'Berhasil disalin' });
    }
}">
    <!-- Stat Cards -->
    <div class="grid grid-cols-1 {{ $pendingOrdersCount > 0 ? 'sm:grid-cols-2 lg:grid-cols-4' : 'sm:grid-cols-3' }} gap-4">
        <div class="bg-white rounded-lg border border-slate-200 p-5 flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-slate-500">VPS Instances</p>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono-code">{{ $vps->count() }} <span class="text-xs font-normal text-slate-500 font-sans">Unit</span></p>
                <p class="text-xs text-slate-400 mt-0.5">
                    {{ $vps->where('status', 'running')->count() }} Berjalan &bull; {{ $vps->where('status', 'stopped')->count() }} Berhenti
                </p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-slate-100 text-slate-900 flex items-center justify-center font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2"/></svg>
            </div>
        </div>

        @if($pendingOrdersCount > 0)
        <div class="bg-amber-50 rounded-lg border border-amber-200 p-5 flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-amber-700">Pesanan Diproses</p>
                <p class="text-2xl font-bold text-amber-900 mt-1 font-mono-code">{{ $pendingOrdersCount }} <span class="text-xs font-normal text-amber-600 font-sans">Order</span></p>
                <p class="text-xs text-amber-600 mt-0.5">Menunggu setup oleh tim teknis</p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
        @endif

        <div class="bg-white rounded-lg border border-slate-200 p-5 flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-slate-500">Faktur & Tagihan</p>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono-code">{{ $invoicesCount }} <span class="text-xs font-normal text-slate-500 font-sans">Faktur</span></p>
                <a href="{{ route('dashboard.billing') }}" class="text-xs text-slate-900 underline mt-0.5 inline-block font-medium">
                    Lihat faktur &rarr;
                </a>
            </div>
            <div class="w-10 h-10 rounded-lg bg-slate-100 text-slate-900 flex items-center justify-center font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-slate-200 p-5 flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-slate-500">Tiket Bantuan</p>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono-code">{{ $openTicketsCount }} <span class="text-xs font-normal text-slate-500 font-sans">Aktif</span></p>
                <a href="{{ route('dashboard.support') }}" class="text-xs text-slate-900 underline mt-0.5 inline-block font-medium">
                    Buka tiket &rarr;
                </a>
            </div>
            <div class="w-10 h-10 rounded-lg bg-slate-100 text-slate-900 flex items-center justify-center font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
        </div>
    </div>

    <!-- Unpaid Orders Section -->
    @if($unpaidOrders->count() > 0)
    <div class="bg-amber-50 border border-amber-200 rounded-lg p-6 mb-6">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
            </div>
            <div>
                <h3 class="font-bold text-amber-800">Layanan Terkunci - Menunggu Pembayaran</h3>
                <p class="text-sm text-amber-700">Anda memiliki {{ $unpaidOrders->count() }} pesanan yang belum dibayar</p>
            </div>
        </div>
        
        <div class="space-y-4">
            @foreach($unpaidOrders as $order)
            <div class="bg-white border border-amber-300 rounded-lg p-4">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <div class="flex items-center gap-2">
                            <h4 class="font-semibold text-slate-900">{{ $order->vpsSpec->name }}</h4>
                            @if($order->hostname)
                                <span class="px-2 py-0.5 rounded text-[11px] font-mono-code font-bold bg-slate-100 text-slate-800 border border-slate-200">
                                    {{ $order->hostname }}
                                </span>
                            @endif
                        </div>
                        <div class="flex items-center gap-3 text-xs text-slate-500 mt-1">
                            <span>{{ $order->vpsSpec->cpu }} vCPU &middot; {{ $order->vpsSpec->ram }} GB RAM &middot; {{ $order->vpsSpec->disk }} GB SSD</span>
                            <span class="px-2 py-0.5 bg-red-50 text-red-700 border border-red-200 rounded text-xs font-medium">
                                TERKUNCI
                            </span>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-xs text-slate-500">Total Tagihan</div>
                        <div class="font-bold text-lg font-mono-code">Rp {{ number_format($order->amount, 0, ',', '.') }}</div>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs mb-4">
                    <div>
                        <span class="text-slate-500">Provider & DC:</span>
                        <span class="font-medium text-slate-900 block capitalize">{{ $order->provider_label }} · {{ $order->datacenter_location }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500">OS & Stack:</span>
                        <span class="font-medium text-slate-900 block">{{ $order->os_label }} · {{ $order->control_panel_label }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500">Metode Bayar:</span>
                        <span class="font-medium text-slate-900 block">{{ strtoupper($order->payment_method) }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500">Invoice:</span>
                        <span class="font-mono-code font-medium block">{{ $order->invoice->invoice_number ?? 'INV-' . $order->id }}</span>
                    </div>
                </div>
                
                <div class="flex gap-3">
                    <a href="{{ route('order.payment', $order->id) }}" 
                       class="flex-1 py-2.5 px-4 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm text-center transition-colors">
                        Selesaikan Pembayaran
                    </a>
                    <a href="{{ route('order.success', $order->id) }}" 
                       class="py-2.5 px-4 rounded-lg border border-slate-300 hover:bg-slate-50 text-slate-700 font-medium text-sm transition-colors">
                        Lihat Detail
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        
        <div class="mt-4 p-4 bg-amber-100 border border-amber-300 rounded-lg">
            <p class="text-sm text-amber-800">
                <span class="font-semibold">Catatan:</span> Layanan VPS akan terkunci sampai pembayaran Anda dikonfirmasi. 
                Setelah pembayaran berhasil, tim teknis akan melakukan setup server dalam 1-2 jam kerja.
            </p>
        </div>
    </div>
    @endif

    <!-- VPS & AI Agent List Section -->
    <div class="space-y-4 pt-2">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Daftar Server, AI Agent &amp; Database</h2>
                <p class="text-xs text-slate-500">Kelola Cloud VPS, runtime AI Agent, dan Dedicated Database Anda</p>
            </div>
        </div>

        @if($vps->count() === 0)
            <div class="bg-white rounded-lg border border-slate-200 p-12 text-center max-w-md mx-auto">
                <div class="w-12 h-12 rounded-lg bg-slate-100 text-slate-900 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2"/></svg>
                </div>
                <h3 class="text-base font-bold text-slate-900 mb-1">Belum Ada Layanan Aktif</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Server cloud, AI Agent, atau Dedicated Database Anda belum terdaftar. Pilih paket komputasi atau database terisolasi siap pakai melalui menu navigasi.
                </p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($vps as $instance)
                    <div class="bg-white rounded-lg border {{ $instance->isDatabasePackage() ? 'border-emerald-300 ring-1 ring-emerald-300/40 shadow-xs' : ($instance->isAiPackage() ? 'border-blue-300 ring-1 ring-blue-300/40 shadow-xs' : 'border-slate-200') }} p-5 hover:border-slate-400 transition-colors flex flex-col justify-between">
                        <div>
                            <!-- Card Header -->
                            <div class="flex items-start justify-between mb-3">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h3 class="font-bold text-slate-900 text-base flex items-center gap-1.5">
                                            <span>{{ $instance->hostname ?? 'vps-' . $instance->id }}</span>
                                        </h3>
                                        @if($instance->isDatabasePackage())
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                Managed DB
                                            </span>
                                        @elseif($instance->isAiPackage())
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-blue-100 text-blue-800 border border-blue-200">
                                                AI Agent
                                            </span>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-1.5 mt-1">
                                        <span class="text-xs font-mono-code font-semibold text-slate-900">
                                            {{ $instance->public_ip ?? 'Mengalokasikan IP...' }}
                                        </span>
                                        @if($instance->public_ip)
                                            <button @click="copyIp('{{ $instance->public_ip }}')" title="Salin IP" class="text-slate-400 hover:text-black">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                            </button>
                                        @endif
                                    </div>
                                </div>

                                @if($instance->status === 'running')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold uppercase border border-emerald-300 text-emerald-800 bg-emerald-50">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        Running
                                    </span>
                                @elseif($instance->status === 'stopped')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold uppercase border border-slate-300 text-slate-700 bg-slate-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                        Stopped
                                    </span>
                                @elseif($instance->status === 'suspended')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold uppercase border border-amber-300 text-amber-800 bg-amber-50">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        Suspended
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold uppercase border border-red-300 text-red-800 bg-red-50">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                        {{ ucfirst($instance->status) }}
                                    </span>
                                @endif
                            </div>

                            <!-- Subscription & Expiry Tag -->
                            <div class="mb-3">
                                @if($instance->status === 'suspended')
                                    <span class="inline-block px-2 py-0.5 rounded text-[11px] font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                                        Layanan Ditangguhkan (Suspended)
                                    </span>
                                @elseif($instance->isInGracePeriod())
                                    <span class="inline-block px-2 py-0.5 rounded text-[11px] font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                                        Masa Tenggang s/d {{ $instance->grace_period_ends_at?->format('d M') }}
                                    </span>
                                @elseif($instance->isExpired())
                                    <span class="inline-block px-2 py-0.5 rounded text-[11px] font-semibold bg-red-50 text-red-800 border border-red-200">
                                        Expired {{ $instance->expires_at?->format('d M Y') }}
                                    </span>
                                @elseif($instance->expires_at)
                                    <span class="inline-block px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                        Tempo: {{ $instance->expires_at->format('d M Y') }} ({{ now()->diffInDays($instance->expires_at, false) }} hr)
                                    </span>
                                @endif
                            </div>

                            <!-- Hardware Specs -->
                            <div class="bg-slate-50 rounded p-3 my-2 text-xs space-y-1 border border-slate-100">
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Compute:</span>
                                    <span class="font-semibold text-slate-900">{{ $instance->cpu ?? 1 }} vCPU &bull; {{ $instance->ram ?? 1 }} GB RAM</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Storage:</span>
                                    <span class="font-semibold text-slate-900">{{ $instance->disk ?? 20 }} GB NVMe SSD</span>
                                </div>
                            </div>

                            <!-- Meta info -->
                            <div class="space-y-1.5 text-xs text-slate-600 mb-3 pt-1">
                                <div class="flex justify-between">
                                    <span>OS:</span>
                                    <span class="font-medium text-slate-900">{{ $instance->os ?? 'Ubuntu 24.04 LTS' }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span>Control Panel / Stack:</span>
                                    <span class="font-semibold text-slate-900 uppercase">
                                        {{ $instance->control_panel_label }}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Siklus:</span>
                                    <span class="font-semibold text-slate-900 uppercase">
                                        {{ str_replace('_', ' ', $instance->billing_cycle ?? 'monthly') }}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Uptime SLA:</span>
                                    <span class="font-semibold text-slate-900">99.9%</span>
                                </div>
                            </div>

                            @if($instance->isAiPackage())
                                <!-- Dedicated Web App Link Box for AI Agent Instance -->
                                <div class="my-3 p-3 rounded-xl bg-blue-50/80 border border-blue-200 text-xs space-y-1.5">
                                    <div class="flex items-center justify-between">
                                        <span class="font-bold text-blue-900 flex items-center gap-1.5">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                            <span>Link Web App:</span>
                                        </span>
                                        <span class="text-[10px] font-bold px-1.5 py-0.2 rounded bg-white text-[#4A6FA5] border border-blue-200">Web App</span>
                                    </div>
                                    @if($instance->app_url && $instance->status === 'running')
                                        <div class="flex items-center justify-between gap-1.5 p-2 rounded-lg bg-white border border-blue-100 font-mono-code text-[11px]">
                                            <a href="{{ $instance->app_url }}" target="_blank" rel="noopener" class="text-[#4A6FA5] font-bold hover:underline truncate">
                                                {{ $instance->app_url }}
                                            </a>
                                            <button @click="copyIp('{{ $instance->app_url }}')" title="Salin Link App" class="text-slate-400 hover:text-black shrink-0">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                            </button>
                                        </div>
                                    @else
                                        <p class="text-[11px] text-slate-500 italic">
                                            {{ $instance->status === 'running' ? 'Mengalokasikan tautan web...' : 'Server non-aktif. Nyalakan untuk membuka app.' }}
                                        </p>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center gap-2 pt-3 border-t border-slate-100">
                            @if($instance->isAiPackage() && $instance->app_url && $instance->status === 'running')
                                <a href="{{ $instance->app_url }}" target="_blank" rel="noopener"
                                   class="flex-1 py-2 px-3 rounded-lg bg-[#4A6FA5] hover:bg-[#3D5E8C] text-white font-bold text-xs text-center transition-colors flex items-center justify-center gap-1.5 shadow-xs">
                                    <span>Buka Web App</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                </a>
                                <a href="{{ route('dashboard.vps.show', $instance->id) }}"
                                   class="py-2 px-3 rounded-lg border border-slate-300 hover:bg-slate-100 text-slate-800 font-semibold text-xs text-center transition-colors">
                                    Detail
                                </a>
                            @else
                                <a href="{{ route('dashboard.vps.show', $instance->id) }}"
                                   class="flex-1 py-2 px-3 rounded-lg bg-black hover:bg-neutral-800 text-white font-semibold text-xs text-center transition-colors">
                                    Kelola Layanan
                                </a>
                            @endif

                            @if($instance->status === 'running')
                                <form action="{{ route('dashboard.vps.stop', $instance->id) }}" method="POST" data-confirm="Matikan instance {{ $instance->hostname }}?">
                                    @csrf
                                    <button type="submit" title="Matikan Server" class="p-2 rounded-lg border border-slate-300 hover:bg-slate-100 text-slate-700 transition-colors">
                                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </button>
                                </form>

                                <form action="{{ route('dashboard.vps.reboot', $instance->id) }}" method="POST" data-confirm="Reboot instance {{ $instance->hostname }}?">
                                    @csrf
                                    <button type="submit" title="Reboot Server" class="p-2 rounded-lg border border-slate-300 hover:bg-slate-100 text-slate-700 transition-colors">
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                    </button>
                                </form>
                            @elseif($instance->status === 'stopped')
                                <form action="{{ route('dashboard.vps.start', $instance->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" title="Nyalakan Server" class="p-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
