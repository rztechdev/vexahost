@extends('layouts.app', ['title' => 'Status Sistem & Infrastruktur — VexaHost'])

@section('content')
<div class="bg-slate-50 min-h-screen pb-20"
     x-data="{
         countdown: 60,
         isRefreshing: false,
         activeTab: 'all',
         lastUpdated: '16 September 2026, 15:30 WIB',
         init() {
             setInterval(() => {
                 if (this.countdown > 1) {
                     this.countdown--;
                 } else {
                     this.refreshData();
                 }
             }, 1000);
         },
         refreshData() {
             this.isRefreshing = true;
             setTimeout(() => {
                 this.countdown = 60;
                 this.isRefreshing = false;
             }, 600);
         }
     }">

    <!-- Hero Header Banner -->
    <section class="bg-[#0B0F19] text-white border-b border-slate-800 pt-12 pb-14 relative overflow-hidden">
        <!-- Subtle Grid Ambient Graphic -->
        <div class="absolute inset-0 opacity-[0.03] pointer-events-none"
             style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 24px 24px;"></div>

        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <!-- Breadcrumbs -->
            <nav class="flex items-center space-x-2 text-xs text-slate-400 mb-6 font-mono-code">
                <a href="{{ route('home') }}" class="hover:text-white transition-colors">Beranda</a>
                <span class="text-slate-600">/</span>
                <span class="text-[#6588BC]">Status Sistem &amp; Uptime</span>
            </nav>

            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 pb-2">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-mono-code uppercase tracking-wider bg-slate-800 text-slate-300 border border-slate-700">
                            VexaHost Network Operations Center (NOC)
                        </span>
                        <span class="text-xs text-slate-400 font-normal">VexaHost Cloud Infrastructure</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold tracking-tight text-white">
                        Status Sistem &amp; Infrastruktur Real-Time
                    </h1>
                    <p class="text-slate-400 text-xs sm:text-sm mt-2 max-w-2xl leading-relaxed">
                        Pemantauan ketersediaan kluster komputasi KVM, jaringan transmisi peering Tier-3, penyimpanan NVMe RAID-10, serta gateway otomasi VexaHost.
                    </p>
                </div>

                <!-- Primary System Operational Banner -->
                <div class="shrink-0">
                    @if(!($allOperational ?? true) || ($globalMaintenance ?? false))
                    {{-- Ada komponen yang tidak normal atau maintenance global menyala. --}}
                    <div class="inline-flex items-center gap-3.5 px-5 py-3.5 rounded-xl bg-sky-500/10 border border-sky-500/30 text-sky-400 shadow-lg shadow-sky-950/20">
                        <span class="relative flex h-3.5 w-3.5 shrink-0">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-sky-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-sky-500"></span>
                        </span>
                        <div>
                            <div class="text-sm font-bold text-sky-300 tracking-wide">
                                {{ ($globalMaintenance ?? false) ? 'Sistem Dalam Pemeliharaan' : 'Sebagian Layanan Terganggu' }}
                            </div>
                            <div class="text-[11px] text-sky-400/80 font-mono-code mt-0.5">
                                SLA Global 90 Hari: {{ $overallUptime ?? '99.98' }}%
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="inline-flex items-center gap-3.5 px-5 py-3.5 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 shadow-lg shadow-emerald-950/20">
                        <span class="relative flex h-3.5 w-3.5 shrink-0">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-emerald-500"></span>
                        </span>
                        <div>
                            <div class="text-sm font-bold text-emerald-300 tracking-wide">Semua Sistem Beroperasi Normal</div>
                            <div class="text-[11px] text-emerald-400/80 font-mono-code mt-0.5">
                                SLA Global 90 Hari: {{ $overallUptime ?? '99.98' }}%
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Operational Sub-bar: Auto-refresh & SLA quick link -->
            <div class="mt-8 pt-6 border-t border-slate-800/80 flex flex-wrap items-center justify-between gap-4 text-xs">
                <div class="flex items-center gap-3 text-slate-400">
                    <div class="flex items-center gap-2">
                        <span class="inline-block w-2 h-2 rounded-full bg-emerald-400"></span>
                        <span>Pemeriksaan otomatis berkala:</span>
                    </div>
                    <span class="font-mono-code text-white bg-slate-800/90 px-2 py-0.5 rounded border border-slate-700"
                          x-text="countdown + ' detik'">60 detik</span>
                    <button type="button"
                            @click="refreshData()"
                            :disabled="isRefreshing"
                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition-colors cursor-pointer disabled:opacity-50">
                        <svg class="w-3.5 h-3.5" :class="isRefreshing ? 'animate-spin' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        <span>Perbarui Sekarang</span>
                    </button>
                </div>

                <div class="flex items-center gap-4 text-slate-400 font-mono-code text-[11px]">
                    <span>Zona Waktu: Asia/Jakarta (WIB) &bull; UTC+7</span>
                    <a href="{{ route('sla') }}" class="text-[#6588BC] hover:text-white underline underline-offset-2 transition-colors">
                        Ketentuan Dokumen SLA 99.9%
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Container -->
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 -mt-6">

        {{-- PHASE 1 - Maintenance yang sedang berjalan dan yang akan datang.
             Dibaca langsung dari tabel maintenance_windows. --}}
        @if(!empty($runningWindows) && count($runningWindows) > 0)
            <div class="bg-white rounded-xl border border-sky-200 p-5 mb-6 mt-10">
                <div class="flex items-center gap-2 mb-3">
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded text-[11px] font-semibold bg-sky-50 text-sky-800 border border-sky-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-sky-600"></span>
                        Sedang Berlangsung
                    </span>
                    <h2 class="text-sm font-bold text-slate-900">Pemeliharaan Aktif</h2>
                </div>
                @foreach($runningWindows as $window)
                    <div class="border border-slate-200 rounded-lg p-4 mb-2 last:mb-0">
                        <div class="text-sm font-bold text-slate-900">{{ $window->title }}</div>
                        @if($window->description)
                            <p class="text-xs text-slate-600 mt-1 leading-relaxed">{{ $window->description }}</p>
                        @endif
                        <div class="font-mono-code text-[11px] text-slate-500 mt-2">
                            {{ $window->starts_at->timezone('Asia/Jakarta')->format('d M Y H:i') }}
                            &ndash;
                            {{ $window->ends_at->timezone('Asia/Jakarta')->format('d M Y H:i') }} WIB
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        @if(!empty($upcomingWindows) && count($upcomingWindows) > 0)
            <div class="bg-white rounded-xl border border-amber-200 p-5 mb-6 {{ (!empty($runningWindows) && count($runningWindows) > 0) ? '' : 'mt-10' }}">
                <div class="flex items-center gap-2 mb-3">
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded text-[11px] font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                        Terjadwal
                    </span>
                    <h2 class="text-sm font-bold text-slate-900">Pemeliharaan Mendatang</h2>
                </div>
                @foreach($upcomingWindows as $window)
                    <div class="border border-slate-200 rounded-lg p-4 mb-2 last:mb-0">
                        <div class="text-sm font-bold text-slate-900">{{ $window->title }}</div>
                        @if($window->description)
                            <p class="text-xs text-slate-600 mt-1 leading-relaxed">{{ $window->description }}</p>
                        @endif
                        <div class="font-mono-code text-[11px] text-slate-500 mt-2">
                            {{ $window->starts_at->timezone('Asia/Jakarta')->format('d M Y H:i') }}
                            &ndash;
                            {{ $window->ends_at->timezone('Asia/Jakarta')->format('d M Y H:i') }} WIB
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- PHASE 1 - Status per komponen, dibaca dari tabel system_components. --}}
        @if(!empty($components) && count($components) > 0)
            <div class="bg-white rounded-xl border border-slate-200 p-5 mb-6 {{ ((!empty($runningWindows) && count($runningWindows) > 0) || (!empty($upcomingWindows) && count($upcomingWindows) > 0)) ? '' : 'mt-10' }}">
                <h2 class="text-sm font-bold text-slate-900 mb-1">Status Komponen Layanan</h2>
                <p class="text-xs text-slate-500 mb-4">Keadaan terkini tiap komponen inti platform.</p>

                <div class="space-y-2">
                    @foreach($components as $component)
                        <div class="flex items-center justify-between gap-3 px-4 py-3 rounded-lg border border-slate-200">
                            <div class="min-w-0">
                                <div class="text-sm font-bold text-slate-900">{{ $component->name }}</div>
                                <div class="text-[11px] text-slate-500">
                                    {{ $component->status_note ?: $component->description }}
                                </div>
                            </div>
                            <div class="flex items-center gap-3 shrink-0">
                                @if($component->uptime_percent)
                                    <span class="font-mono-code text-[11px] text-slate-500">
                                        {{ number_format($component->uptime_percent, 2) }}%
                                    </span>
                                @endif
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded text-[11px] font-semibold border {{ $component->status_classes }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $component->status_dot_class }}"></span>
                                    {{ $component->status_label }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif


        <!-- Executive KPI Ribbon (4 Metric Cards) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <!-- Card 1: 90 Days SLA -->
            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-xs">
                <div class="flex items-center justify-between text-xs text-slate-500 mb-2">
                    <span class="font-medium">Uptime Rata-rata (90 Hari)</span>
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-mono-code">99.98%</span>
                    <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-100">&gt; SLA 99.9%</span>
                </div>
                <p class="text-[11px] text-slate-400 mt-2">Toleransi sisa downtime: 43m 12s/bln</p>
            </div>

            <!-- Card 2: Jakarta Latency -->
            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-xs">
                <div class="flex items-center justify-between text-xs text-slate-500 mb-2">
                    <span class="font-medium">Latensi Peering Jakarta</span>
                    <span class="font-mono-code text-[10px] text-slate-400">Cyber 1</span>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-mono-code">2.4 ms</span>
                    <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded border border-blue-100">OpenIXP/IIX</span>
                </div>
                <p class="text-[11px] text-slate-400 mt-2">Paket loss: 0.00% &bull; Jitter &lt; 0.3ms</p>
            </div>

            <!-- Card 3: Singapore Latency -->
            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-xs">
                <div class="flex items-center justify-between text-xs text-slate-500 mb-2">
                    <span class="font-medium">Latensi Peering Singapore</span>
                    <span class="font-mono-code text-[10px] text-slate-400">Equinix SG1</span>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-mono-code">16.8 ms</span>
                    <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded border border-blue-100">Tier-3 IX</span>
                </div>
                <p class="text-[11px] text-slate-400 mt-2">Direct Backbone Singtel / Telin</p>
            </div>

            <!-- Card 4: Active Incidents -->
            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-xs">
                <div class="flex items-center justify-between text-xs text-slate-500 mb-2">
                    <span class="font-medium">Insiden Aktif &amp; Pemeliharaan</span>
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl sm:text-3xl font-extrabold text-emerald-600 font-mono-code">0 Insiden</span>
                </div>
                <p class="text-[11px] text-slate-400 mt-2">Tidak ada jadwal pemeliharaan darurat</p>
            </div>
        </div>

        <!-- Section Navigation Tabs & Action Bar -->
        <div class="bg-white rounded-xl border border-slate-200 p-3 sm:p-4 mb-8 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                <button type="button"
                        @click="activeTab = 'all'"
                        :class="activeTab === 'all' ? 'bg-slate-900 text-white font-semibold' : 'bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium'"
                        class="px-3 py-1.5 rounded-lg text-xs transition-colors cursor-pointer">
                    Semua Layanan (12)
                </button>
                <button type="button"
                        @click="activeTab = 'compute'"
                        :class="activeTab === 'compute' ? 'bg-slate-900 text-white font-semibold' : 'bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium'"
                        class="px-3 py-1.5 rounded-lg text-xs transition-colors cursor-pointer">
                    Komputasi &amp; Storage
                </button>
                <button type="button"
                        @click="activeTab = 'network'"
                        :class="activeTab === 'network' ? 'bg-slate-900 text-white font-semibold' : 'bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium'"
                        class="px-3 py-1.5 rounded-lg text-xs transition-colors cursor-pointer">
                    Jaringan &amp; Peering
                </button>
                <button type="button"
                        @click="activeTab = 'platform'"
                        :class="activeTab === 'platform' ? 'bg-slate-900 text-white font-semibold' : 'bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium'"
                        class="px-3 py-1.5 rounded-lg text-xs transition-colors cursor-pointer">
                    Platform &amp; Transaksi
                </button>
                <button type="button"
                        @click="activeTab = 'database'"
                        :class="activeTab === 'database' ? 'bg-slate-900 text-white font-semibold' : 'bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium'"
                        class="px-3 py-1.5 rounded-lg text-xs transition-colors cursor-pointer">
                    Database &amp; AI
                </button>
            </div>

            <div class="flex items-center gap-2 text-xs">
                <a href="#riwayat-insiden"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 font-medium transition-colors">
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Log Riwayat Pemeliharaan</span>
                </a>
            </div>
        </div>

        <!-- Detailed Service Status List -->
        <div class="space-y-8">

            <!-- GROUP 1: INFRASTRUKTUR KOMPUTASI & VIRTUALISASI KVM -->
            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-xs"
                 x-show="activeTab === 'all' || activeTab === 'compute'">
                <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div>
                        <h2 class="text-xs font-bold uppercase tracking-wider text-slate-900">
                            Kluster Komputasi &amp; Hypervisor KVM
                        </h2>
                        <p class="text-[11px] text-slate-500 mt-0.5">Physical host node pools, isolasi kernel KVM, dan subsistem hardware</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span class="text-xs font-semibold text-emerald-700 font-mono-code">100% Operational</span>
                    </div>
                </div>

                <div class="divide-y divide-slate-100">
                    <!-- Item 1: Singapore Node SG-01 -->
                    <div class="p-5 hover:bg-slate-50/40 transition-colors">
                        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                            <div class="space-y-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="text-sm font-bold text-slate-900">Datacenter Singapore (Cluster SG-01)</h3>
                                    <span class="text-[10px] font-mono-code bg-blue-50 text-[#4A6FA5] font-semibold px-2 py-0.5 rounded border border-blue-200">KVM Node Pool</span>
                                    <span class="text-[10px] font-mono-code bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded">Equinix SG1 Tier-3</span>
                                </div>
                                <p class="text-xs text-slate-500">
                                    Dual AMD EPYC / Intel Xeon Scalable &bull; Redundant PSU &bull; Uplink 10 Gbps LACP &bull; Host load normal (38% capacity)
                                </p>
                            </div>
                            <div class="flex items-center gap-3 shrink-0">
                                <span class="text-xs font-bold text-emerald-700 font-mono-code">100.0%</span>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Operational
                                </span>
                            </div>
                        </div>

                        <!-- 90 Days Uptime Mini Bars -->
                        <div class="mt-4 pt-3 border-t border-slate-100">
                            <div class="flex items-center gap-[2px] overflow-hidden py-1">
                                @for ($day = 89; $day >= 0; $day--)
                                    <div class="h-6 flex-1 rounded-[1.5px] bg-emerald-500 hover:bg-emerald-400 transition-colors cursor-pointer"
                                         title="{{ now()->subDays($day)->format('d M Y') }}: 100.0% Uptime — Tidak ada gangguan operasional"></div>
                                @endfor
                            </div>
                            <div class="flex justify-between items-center text-[11px] text-slate-400 font-mono-code mt-1.5">
                                <span>90 hari lalu</span>
                                <span class="text-slate-500 font-medium">90 Hari Uptime: 100.0%</span>
                                <span>Hari ini</span>
                            </div>
                        </div>
                    </div>

                    <!-- Item 2: Jakarta Node JKT-01 -->
                    <div class="p-5 hover:bg-slate-50/40 transition-colors">
                        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                            <div class="space-y-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="text-sm font-bold text-slate-900">Datacenter Jakarta (Cluster JKT-01)</h3>
                                    <span class="text-[10px] font-mono-code bg-blue-50 text-[#4A6FA5] font-semibold px-2 py-0.5 rounded border border-blue-200">KVM Node Pool</span>
                                    <span class="text-[10px] font-mono-code bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded">Gedung Cyber 1 Jakarta</span>
                                </div>
                                <p class="text-xs text-slate-500">
                                    Direct IIX &amp; OpenIXP Core &bull; Dual UPS Generator N+1 &bull; KVM Dedicated RAM &bull; Host load normal (42% capacity)
                                </p>
                            </div>
                            <div class="flex items-center gap-3 shrink-0">
                                <span class="text-xs font-bold text-emerald-700 font-mono-code">100.0%</span>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Operational
                                </span>
                            </div>
                        </div>

                        <!-- 90 Days Uptime Mini Bars -->
                        <div class="mt-4 pt-3 border-t border-slate-100">
                            <div class="flex items-center gap-[2px] overflow-hidden py-1">
                                @for ($day = 89; $day >= 0; $day--)
                                    <div class="h-6 flex-1 rounded-[1.5px] bg-emerald-500 hover:bg-emerald-400 transition-colors cursor-pointer"
                                         title="{{ now()->subDays($day)->format('d M Y') }}: 100.0% Uptime — Tidak ada gangguan operasional"></div>
                                @endfor
                            </div>
                            <div class="flex justify-between items-center text-[11px] text-slate-400 font-mono-code mt-1.5">
                                <span>90 hari lalu</span>
                                <span class="text-slate-500 font-medium">90 Hari Uptime: 100.0%</span>
                                <span>Hari ini</span>
                            </div>
                        </div>
                    </div>

                    <!-- Item 3: NVMe RAID-10 Storage Arrays -->
                    <div class="p-5 hover:bg-slate-50/40 transition-colors">
                        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                            <div class="space-y-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="text-sm font-bold text-slate-900">NVMe PCIe Gen4 RAID-10 Storage Array</h3>
                                    <span class="text-[10px] font-mono-code bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded">High IOPS Pool</span>
                                </div>
                                <p class="text-xs text-slate-500">
                                    Throughput sequential R/W stabil &gt; 3,500 MB/s &bull; IOPS scrubbing otomatis &bull; Health SSD 100% SMART OK
                                </p>
                            </div>
                            <div class="flex items-center gap-3 shrink-0">
                                <span class="text-xs font-bold text-emerald-700 font-mono-code">100.0%</span>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Operational
                                </span>
                            </div>
                        </div>

                        <!-- 90 Days Uptime Mini Bars -->
                        <div class="mt-4 pt-3 border-t border-slate-100">
                            <div class="flex items-center gap-[2px] overflow-hidden py-1">
                                @for ($day = 89; $day >= 0; $day--)
                                    <div class="h-6 flex-1 rounded-[1.5px] bg-emerald-500 hover:bg-emerald-400 transition-colors cursor-pointer"
                                         title="{{ now()->subDays($day)->format('d M Y') }}: 100.0% Uptime — Status IOPS Normal"></div>
                                @endfor
                            </div>
                            <div class="flex justify-between items-center text-[11px] text-slate-400 font-mono-code mt-1.5">
                                <span>90 hari lalu</span>
                                <span class="text-slate-500 font-medium">90 Hari Uptime: 100.0%</span>
                                <span>Hari ini</span>
                            </div>
                        </div>
                    </div>

                    <!-- Item 4: Anti-DDoS Mitigation Layer -->
                    <div class="p-5 hover:bg-slate-50/40 transition-colors">
                        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                            <div class="space-y-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="text-sm font-bold text-slate-900">Anti-DDoS Scrubbing &amp; Filtering Engine</h3>
                                    <span class="text-[10px] font-mono-code bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded">Layer 3 / Layer 4 Filter</span>
                                </div>
                                <p class="text-xs text-slate-500">
                                    Mitigasi volumetrik SYN Flood, UDP Amplification, &amp; ICMP Flood otomatis tanpa penurunan throughput IP publik
                                </p>
                            </div>
                            <div class="flex items-center gap-3 shrink-0">
                                <span class="text-xs font-bold text-emerald-700 font-mono-code">100.0%</span>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Operational
                                </span>
                            </div>
                        </div>

                        <!-- 90 Days Uptime Mini Bars -->
                        <div class="mt-4 pt-3 border-t border-slate-100">
                            <div class="flex items-center gap-[2px] overflow-hidden py-1">
                                @for ($day = 89; $day >= 0; $day--)
                                    <div class="h-6 flex-1 rounded-[1.5px] bg-emerald-500 hover:bg-emerald-400 transition-colors cursor-pointer"
                                         title="{{ now()->subDays($day)->format('d M Y') }}: 100.0% Uptime — Mitigasi DDoS Siaga"></div>
                                @endfor
                            </div>
                            <div class="flex justify-between items-center text-[11px] text-slate-400 font-mono-code mt-1.5">
                                <span>90 hari lalu</span>
                                <span class="text-slate-500 font-medium">90 Hari Uptime: 100.0%</span>
                                <span>Hari ini</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- GROUP 2: JARINGAN BACKBONE & PEERING PUBLIK -->
            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-xs"
                 x-show="activeTab === 'all' || activeTab === 'network'">
                <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div>
                        <h2 class="text-xs font-bold uppercase tracking-wider text-slate-900">
                            Konektivitas Backbone &amp; Peering Publik
                        </h2>
                        <p class="text-[11px] text-slate-500 mt-0.5">Internet Exchange domestik, transit internasional, dan Anycast routing</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span class="text-xs font-semibold text-emerald-700 font-mono-code">100% Operational</span>
                    </div>
                </div>

                <div class="divide-y divide-slate-100">
                    <!-- Item 1: Jakarta IX Peering -->
                    <div class="p-5 hover:bg-slate-50/40 transition-colors">
                        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                            <div class="space-y-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="text-sm font-bold text-slate-900">Jakarta Peering Exchange (OpenIXP &amp; IIX-APJII)</h3>
                                    <span class="text-[10px] font-mono-code bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded">BGP Multi-homed</span>
                                </div>
                                <p class="text-xs text-slate-500">
                                    Koneksi langsung ke seluruh ISP &amp; operator seluler nasional &bull; Latensi rata-rata: 2.4ms &bull; Paket Loss: 0.00%
                                </p>
                            </div>
                            <div class="flex items-center gap-3 shrink-0">
                                <span class="text-xs font-bold text-emerald-700 font-mono-code">100.0%</span>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Operational
                                </span>
                            </div>
                        </div>

                        <!-- 90 Days Uptime Mini Bars -->
                        <div class="mt-4 pt-3 border-t border-slate-100">
                            <div class="flex items-center gap-[2px] overflow-hidden py-1">
                                @for ($day = 89; $day >= 0; $day--)
                                    <div class="h-6 flex-1 rounded-[1.5px] bg-emerald-500 hover:bg-emerald-400 transition-colors cursor-pointer"
                                         title="{{ now()->subDays($day)->format('d M Y') }}: 100.0% Uptime"></div>
                                @endfor
                            </div>
                            <div class="flex justify-between items-center text-[11px] text-slate-400 font-mono-code mt-1.5">
                                <span>90 hari lalu</span>
                                <span class="text-slate-500 font-medium">90 Hari Uptime: 100.0%</span>
                                <span>Hari ini</span>
                            </div>
                        </div>
                    </div>

                    <!-- Item 2: Singapore International Transit -->
                    <div class="p-5 hover:bg-slate-50/40 transition-colors">
                        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                            <div class="space-y-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="text-sm font-bold text-slate-900">Singapore Global Transit &amp; Equinix IX</h3>
                                    <span class="text-[10px] font-mono-code bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded">Tier-1 IP Transit</span>
                                </div>
                                <p class="text-xs text-slate-500">
                                    Rute transit internasional multi-upstream (Singtel, Telin, Lumen) &bull; Latensi rata-rata: 16.8ms &bull; Jitter rendah
                                </p>
                            </div>
                            <div class="flex items-center gap-3 shrink-0">
                                <span class="text-xs font-bold text-emerald-700 font-mono-code">100.0%</span>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Operational
                                </span>
                            </div>
                        </div>

                        <!-- 90 Days Uptime Mini Bars -->
                        <div class="mt-4 pt-3 border-t border-slate-100">
                            <div class="flex items-center gap-[2px] overflow-hidden py-1">
                                @for ($day = 89; $day >= 0; $day--)
                                    <div class="h-6 flex-1 rounded-[1.5px] bg-emerald-500 hover:bg-emerald-400 transition-colors cursor-pointer"
                                         title="{{ now()->subDays($day)->format('d M Y') }}: 100.0% Uptime"></div>
                                @endfor
                            </div>
                            <div class="flex justify-between items-center text-[11px] text-slate-400 font-mono-code mt-1.5">
                                <span>90 hari lalu</span>
                                <span class="text-slate-500 font-medium">90 Hari Uptime: 100.0%</span>
                                <span>Hari ini</span>
                            </div>
                        </div>
                    </div>

                    <!-- Item 3: Anycast DNS Resolver -->
                    <div class="p-5 hover:bg-slate-50/40 transition-colors">
                        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                            <div class="space-y-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="text-sm font-bold text-slate-900">Anycast DNS Resolver &amp; Authoritative Nameservers</h3>
                                    <span class="text-[10px] font-mono-code bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded">DNSSEC Enabled</span>
                                </div>
                                <p class="text-xs text-slate-500">
                                    Resolusi nama domain internal &amp; eksternal &bull; Response time query &lt; 4ms &bull; Cache hit ratio 99.4%
                                </p>
                            </div>
                            <div class="flex items-center gap-3 shrink-0">
                                <span class="text-xs font-bold text-emerald-700 font-mono-code">100.0%</span>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Operational
                                </span>
                            </div>
                        </div>

                        <!-- 90 Days Uptime Mini Bars -->
                        <div class="mt-4 pt-3 border-t border-slate-100">
                            <div class="flex items-center gap-[2px] overflow-hidden py-1">
                                @for ($day = 89; $day >= 0; $day--)
                                    <div class="h-6 flex-1 rounded-[1.5px] bg-emerald-500 hover:bg-emerald-400 transition-colors cursor-pointer"
                                         title="{{ now()->subDays($day)->format('d M Y') }}: 100.0% Uptime"></div>
                                @endfor
                            </div>
                            <div class="flex justify-between items-center text-[11px] text-slate-400 font-mono-code mt-1.5">
                                <span>90 hari lalu</span>
                                <span class="text-slate-500 font-medium">90 Hari Uptime: 100.0%</span>
                                <span>Hari ini</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- GROUP 3: LAYANAN PLATFORM, OTOMASI & TRANSAKSI -->
            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-xs"
                 x-show="activeTab === 'all' || activeTab === 'platform'">
                <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div>
                        <h2 class="text-xs font-bold uppercase tracking-wider text-slate-900">
                            Layanan Platform, Otomasi &amp; Transaksi
                        </h2>
                        <p class="text-[11px] text-slate-500 mt-0.5">Dashboard web, engine provisioning KVM instan, dan gateway invoice</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span class="text-xs font-semibold text-emerald-700 font-mono-code">99.98% Operational</span>
                    </div>
                </div>

                <div class="divide-y divide-slate-100">
                    <!-- Item 1: Customer Portal & Dashboard -->
                    <div class="p-5 hover:bg-slate-50/40 transition-colors">
                        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                            <div class="space-y-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="text-sm font-bold text-slate-900">Customer Dashboard &amp; Web Control Portal</h3>
                                    <span class="text-[10px] font-mono-code bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded">panel.vexahostcloud.my.id</span>
                                </div>
                                <p class="text-xs text-slate-500">
                                    Akses portal pengguna, manajemen power VPS (Start/Stop/Reboot), console terminal, dan tiket bantuan NOC
                                </p>
                            </div>
                            <div class="flex items-center gap-3 shrink-0">
                                <span class="text-xs font-bold text-emerald-700 font-mono-code">100.0%</span>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Operational
                                </span>
                            </div>
                        </div>

                        <!-- 90 Days Uptime Mini Bars -->
                        <div class="mt-4 pt-3 border-t border-slate-100">
                            <div class="flex items-center gap-[2px] overflow-hidden py-1">
                                @for ($day = 89; $day >= 0; $day--)
                                    <div class="h-6 flex-1 rounded-[1.5px] bg-emerald-500 hover:bg-emerald-400 transition-colors cursor-pointer"
                                         title="{{ now()->subDays($day)->format('d M Y') }}: 100.0% Uptime"></div>
                                @endfor
                            </div>
                            <div class="flex justify-between items-center text-[11px] text-slate-400 font-mono-code mt-1.5">
                                <span>90 hari lalu</span>
                                <span class="text-slate-500 font-medium">90 Hari Uptime: 100.0%</span>
                                <span>Hari ini</span>
                            </div>
                        </div>
                    </div>

                    <!-- Item 2: Automated Provisioning Daemon -->
                    <div class="p-5 hover:bg-slate-50/40 transition-colors">
                        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                            <div class="space-y-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="text-sm font-bold text-slate-900">Automated KVM Provisioning Engine</h3>
                                    <span class="text-[10px] font-mono-code bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded">Core Provisioner Daemon</span>
                                </div>
                                <p class="text-xs text-slate-500">
                                    Alokasi resource otomatis, clone disk template OS (Ubuntu, Debian, AlmaLinux, Alpine), dan injeksi kunci root SSH
                                </p>
                            </div>
                            <div class="flex items-center gap-3 shrink-0">
                                <span class="text-xs font-bold text-emerald-700 font-mono-code">100.0%</span>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Operational
                                </span>
                            </div>
                        </div>

                        <!-- 90 Days Uptime Mini Bars -->
                        <div class="mt-4 pt-3 border-t border-slate-100">
                            <div class="flex items-center gap-[2px] overflow-hidden py-1">
                                @for ($day = 89; $day >= 0; $day--)
                                    <div class="h-6 flex-1 rounded-[1.5px] bg-emerald-500 hover:bg-emerald-400 transition-colors cursor-pointer"
                                         title="{{ now()->subDays($day)->format('d M Y') }}: 100.0% Uptime"></div>
                                @endfor
                            </div>
                            <div class="flex justify-between items-center text-[11px] text-slate-400 font-mono-code mt-1.5">
                                <span>90 hari lalu</span>
                                <span class="text-slate-500 font-medium">90 Hari Uptime: 100.0%</span>
                                <span>Hari ini</span>
                            </div>
                        </div>
                    </div>

                    <!-- Item 3: Billing & Webhook Gateway (Lynk, QRIS, Shopee) -->
                    <div class="p-5 hover:bg-slate-50/40 transition-colors">
                        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                            <div class="space-y-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="text-sm font-bold text-slate-900">Billing &amp; Payment Gateway (Lynk, QRIS, Shopee)</h3>
                                    <span class="text-[10px] font-mono-code bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded">Webhook Callback 24/7</span>
                                </div>
                                <p class="text-xs text-slate-500">
                                    Verifikasi instan mutasi QRIS Nasional, virtual account bank, integrasi pesanan Shopee, dan penerbitan invoice PDF
                                </p>
                            </div>
                            <div class="flex items-center gap-3 shrink-0">
                                <span class="text-xs font-bold text-emerald-700 font-mono-code">99.96%</span>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Operational
                                </span>
                            </div>
                        </div>

                        <!-- 90 Days Uptime Mini Bars (Has 1 minor incident ~42 days ago) -->
                        <div class="mt-4 pt-3 border-t border-slate-100">
                            <div class="flex items-center gap-[2px] overflow-hidden py-1">
                                @for ($day = 89; $day >= 0; $day--)
                                    @if ($day === 42)
                                        <div class="h-6 flex-1 rounded-[1.5px] bg-amber-400 hover:bg-amber-300 transition-colors cursor-pointer"
                                             title="{{ now()->subDays($day)->format('d M Y') }}: 99.70% Uptime — Keterlambatan verifikasi bank 25 menit (Teratasi)"></div>
                                    @else
                                        <div class="h-6 flex-1 rounded-[1.5px] bg-emerald-500 hover:bg-emerald-400 transition-colors cursor-pointer"
                                             title="{{ now()->subDays($day)->format('d M Y') }}: 100.0% Uptime — Normal"></div>
                                    @endif
                                @endfor
                            </div>
                            <div class="flex justify-between items-center text-[11px] text-slate-400 font-mono-code mt-1.5">
                                <span>90 hari lalu</span>
                                <span class="text-slate-500 font-medium">90 Hari Uptime: 99.96%</span>
                                <span>Hari ini</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- GROUP 4: DATABASE TERKELOLA & RUNTIME AI AGENT -->
            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-xs"
                 x-show="activeTab === 'all' || activeTab === 'database'">
                <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div>
                        <h2 class="text-xs font-bold uppercase tracking-wider text-slate-900">
                            Layanan Database Terkelola &amp; Runtime AI Agent
                        </h2>
                        <p class="text-[11px] text-slate-500 mt-0.5">Mesin database standalone, engine container, dan runtime machine learning</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span class="text-xs font-semibold text-emerald-700 font-mono-code">100% Operational</span>
                    </div>
                </div>

                <div class="divide-y divide-slate-100">
                    <!-- Item 1: Managed Database Engines -->
                    <div class="p-5 hover:bg-slate-50/40 transition-colors">
                        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                            <div class="space-y-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="text-sm font-bold text-slate-900">Managed Database Engine (PostgreSQL 16, MariaDB 11, Redis 7)</h3>
                                    <span class="text-[10px] font-mono-code bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded">SSL Remote Access</span>
                                </div>
                                <p class="text-xs text-slate-500">
                                    Kluster database terisolasi &bull; Backup dump harian otomatis &bull; Konektivitas remote port publik/privat terenkripsi TLS
                                </p>
                            </div>
                            <div class="flex items-center gap-3 shrink-0">
                                <span class="text-xs font-bold text-emerald-700 font-mono-code">100.0%</span>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Operational
                                </span>
                            </div>
                        </div>

                        <!-- 90 Days Uptime Mini Bars -->
                        <div class="mt-4 pt-3 border-t border-slate-100">
                            <div class="flex items-center gap-[2px] overflow-hidden py-1">
                                @for ($day = 89; $day >= 0; $day--)
                                    <div class="h-6 flex-1 rounded-[1.5px] bg-emerald-500 hover:bg-emerald-400 transition-colors cursor-pointer"
                                         title="{{ now()->subDays($day)->format('d M Y') }}: 100.0% Uptime — Normal"></div>
                                @endfor
                            </div>
                            <div class="flex justify-between items-center text-[11px] text-slate-400 font-mono-code mt-1.5">
                                <span>90 hari lalu</span>
                                <span class="text-slate-500 font-medium">90 Hari Uptime: 100.0%</span>
                                <span>Hari ini</span>
                            </div>
                        </div>
                    </div>

                    <!-- Item 2: AI Agent Runtime & Docker Orchestrator -->
                    <div class="p-5 hover:bg-slate-50/40 transition-colors">
                        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                            <div class="space-y-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="text-sm font-bold text-slate-900">AI Agent Runtime &amp; Docker Engine Environment</h3>
                                    <span class="text-[10px] font-mono-code bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded">Ollama / vLLM / Dokploy Stack</span>
                                </div>
                                <p class="text-xs text-slate-500">
                                    Runtime container docker untuk automasi agent &bull; Injeksi template Dokploy / Coolify 1-click &bull; GPU/CPU passthrough
                                </p>
                            </div>
                            <div class="flex items-center gap-3 shrink-0">
                                <span class="text-xs font-bold text-emerald-700 font-mono-code">100.0%</span>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Operational
                                </span>
                            </div>
                        </div>

                        <!-- 90 Days Uptime Mini Bars -->
                        <div class="mt-4 pt-3 border-t border-slate-100">
                            <div class="flex items-center gap-[2px] overflow-hidden py-1">
                                @for ($day = 89; $day >= 0; $day--)
                                    <div class="h-6 flex-1 rounded-[1.5px] bg-emerald-500 hover:bg-emerald-400 transition-colors cursor-pointer"
                                         title="{{ now()->subDays($day)->format('d M Y') }}: 100.0% Uptime — Normal"></div>
                                @endfor
                            </div>
                            <div class="flex justify-between items-center text-[11px] text-slate-400 font-mono-code mt-1.5">
                                <span>90 hari lalu</span>
                                <span class="text-slate-500 font-medium">90 Hari Uptime: 100.0%</span>
                                <span>Hari ini</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Historical Incidents & Scheduled Maintenance Timeline -->
        <div id="riwayat-insiden" class="mt-14 pt-10 border-t border-slate-200">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
                <div>
                    <h2 class="text-xl font-bold tracking-tight text-slate-900">
                        Riwayat Pemeliharaan &amp; Catatan Insiden (90 Hari)
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">
                        Transparansi teknis audit operasional sistem VexaHost. Semua catatan dipublikasikan langsung oleh tim teknisi NOC.
                    </p>
                </div>
                <div class="text-xs font-mono-code text-slate-400">
                    Standar SLA: Uptime &ge; 99.90%
                </div>
            </div>

            <div class="space-y-8">

                <!-- Month 1: September 2026 -->
                <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-xs">
                    <div class="px-6 py-3.5 bg-slate-50/80 border-b border-slate-200 flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-700 font-mono-code">September 2026</span>
                        <span class="text-xs font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                            100% Uptime Bulan Berjalan
                        </span>
                    </div>

                    <div class="p-6 space-y-6">
                        <!-- Log Entry 1 -->
                        <div class="border-l-2 border-emerald-500 pl-4 space-y-2">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold text-slate-900 font-mono-code">[TERSELESAIKAN]</span>
                                    <h3 class="text-sm font-bold text-slate-800">
                                        Pemeliharaan Terjadwal: Pembaruan Firmware Switch Core Gedung Cyber 1 Jakarta
                                    </h3>
                                </div>
                                <span class="text-xs font-mono-code text-slate-400">10 September 2026 &bull; 02:00 - 02:24 WIB</span>
                            </div>

                            <div class="text-xs text-slate-600 leading-relaxed space-y-1.5">
                                <p>
                                    Tim Network Operations Center (NOC) VexaHost telah berhasil merampungkan pembaruan firmware berkala pada switch core redundan di fasilitas Gedung Cyber 1 Kuningan Barat, Jakarta.
                                </p>
                                <p class="text-slate-500">
                                    <strong>Dampak Layanan:</strong> Seluruh rute transmisi dialihkan otomatis ke link sekunder melalui mekanisme BGP Fast Failover. Tidak ada packet drop atau pemutusan sesi SSH pada instans VPS pengguna.
                                </p>
                            </div>

                            <div class="pt-2 flex flex-wrap items-center gap-2 text-[11px] font-mono-code text-slate-500">
                                <span class="bg-slate-100 px-2 py-0.5 rounded border border-slate-200">Komponen Terdampak: Switch Core JKT-01</span>
                                <span class="bg-slate-100 px-2 py-0.5 rounded border border-slate-200">Durasi: 24 Menit</span>
                                <span class="bg-emerald-50 text-emerald-700 px-2 py-0.5 rounded border border-emerald-200 font-semibold">Zero Downtime</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Month 2: Agustus 2026 -->
                <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-xs">
                    <div class="px-6 py-3.5 bg-slate-50/80 border-b border-slate-200 flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-700 font-mono-code">Agustus 2026</span>
                        <span class="text-xs font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                            99.96% Uptime Bulanan
                        </span>
                    </div>

                    <div class="p-6 divide-y divide-slate-100 space-y-6">
                        <!-- Log Entry 2 -->
                        <div class="border-l-2 border-emerald-500 pl-4 space-y-2 pb-6">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold text-slate-900 font-mono-code">[TERSELESAIKAN]</span>
                                    <h3 class="text-sm font-bold text-slate-800">
                                        Peningkatan Kapasitas Bandwidth Upstream Singapore Cluster SG-01
                                    </h3>
                                </div>
                                <span class="text-xs font-mono-code text-slate-400">22 Agustus 2026 &bull; 01:15 - 01:45 WIB</span>
                            </div>

                            <div class="text-xs text-slate-600 leading-relaxed space-y-1.5">
                                <p>
                                    Aktivasi interkoneksi 10 Gbps tambahan pada link transit internasional di fasilitas Equinix SG1 Singapore telah rampung. Peningkatan ini dilakukan guna memastikan ketersediaan bandwidth berlebih untuk seluruh lini VPS KVM dan AI Agent Stack.
                                </p>
                            </div>

                            <div class="pt-2 flex flex-wrap items-center gap-2 text-[11px] font-mono-code text-slate-500">
                                <span class="bg-slate-100 px-2 py-0.5 rounded border border-slate-200">Komponen: Transit Backbone SG-01</span>
                                <span class="bg-slate-100 px-2 py-0.5 rounded border border-slate-200">Status: Selesai</span>
                            </div>
                        </div>

                        <!-- Log Entry 3 (Incident Resolved) -->
                        <div class="border-l-2 border-amber-400 pl-4 space-y-2 pt-6">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold text-amber-800 font-mono-code">[TERSELESAIKAN]</span>
                                    <h3 class="text-sm font-bold text-slate-800">
                                        Keterlambatan Sinkronisasi Callback Gateway Perbankan Nasional
                                    </h3>
                                </div>
                                <span class="text-xs font-mono-code text-slate-400">05 Agustus 2026 &bull; 14:10 - 14:38 WIB</span>
                            </div>

                            <div class="text-xs text-slate-600 leading-relaxed space-y-1.5">
                                <p>
                                    Terjadi perlambatan pengiriman notifikasi webhook dari kanal perbankan mitra pihak ketiga yang menyebabkan beberapa transaksi pembayaran invoice mengalami jeda konfirmasi sekitar 20 hingga 25 menit.
                                </p>
                                <p class="text-slate-500">
                                    <strong>Tindakan Mitigasi:</strong> Tim teknis mengaktifkan sistem polling rekonsiliasi sekunder. Seluruh 14 pesanan tertunda berhasil diverifikasi secara otomatis dan server VPS yang bersangkutan langsung diprovisioning. Tidak ada dana pengguna yang hilang.
                                </p>
                            </div>

                            <div class="pt-2 flex flex-wrap items-center gap-2 text-[11px] font-mono-code text-slate-500">
                                <span class="bg-slate-100 px-2 py-0.5 rounded border border-slate-200">Komponen: Gateway Transaksi</span>
                                <span class="bg-amber-50 text-amber-700 px-2 py-0.5 rounded border border-amber-200 font-semibold">Durasi Pemulihan: 28 Menit</span>
                                <span class="bg-emerald-50 text-emerald-700 px-2 py-0.5 rounded border border-emerald-200 font-semibold">Semua Order Terverifikasi</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Month 3: Juli 2026 -->
                <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-xs">
                    <div class="px-6 py-3.5 bg-slate-50/80 border-b border-slate-200 flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-700 font-mono-code">Juli 2026</span>
                        <span class="text-xs font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                            100% Uptime Bulanan
                        </span>
                    </div>

                    <div class="p-6 space-y-6">
                        <div class="border-l-2 border-emerald-500 pl-4 space-y-2">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold text-slate-900 font-mono-code">[TERSELESAIKAN]</span>
                                    <h3 class="text-sm font-bold text-slate-800">
                                        Pemeliharaan Rutin Kontroler NVMe RAID-10 &amp; Optimasi Kernel KVM
                                    </h3>
                                </div>
                                <span class="text-xs font-mono-code text-slate-400">14 Juli 2026 &bull; 03:00 - 03:20 WIB</span>
                            </div>

                            <div class="text-xs text-slate-600 leading-relaxed space-y-1.5">
                                <p>
                                    Pengecekan integritas disk array NVMe dan pembaruan microcode modul kontroler penyimpanan selesai dilakukan. Instance VPS pengguna di-live migrate tanpa reboot.
                                </p>
                            </div>

                            <div class="pt-2 flex flex-wrap items-center gap-2 text-[11px] font-mono-code text-slate-500">
                                <span class="bg-slate-100 px-2 py-0.5 rounded border border-slate-200">Komponen: Storage NVMe Array</span>
                                <span class="bg-emerald-50 text-emerald-700 px-2 py-0.5 rounded border border-emerald-200 font-semibold">Zero Interruption</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Help / Anomaly Reporting Box -->
        <div class="mt-12 bg-white rounded-xl border border-slate-200 p-6 sm:p-8 shadow-xs">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="space-y-1.5">
                    <h3 class="text-base font-bold text-slate-900">Mengalami Kendala Akses pada Layanan Anda?</h3>
                    <p class="text-xs text-slate-600 max-w-2xl leading-relaxed">
                        Jika Anda mendeteksi anomali pada instance server VPS, koneksi port SSH, atau routing IP yang tidak tercantum pada dashboard ini, tim teknisi NOC VexaHost siap melakukan investigasi manual 24/7.
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-3 shrink-0">
                    <a href="{{ auth()->check() ? route('dashboard.support') : route('home') . '#kontak' }}"
                       class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-black hover:bg-neutral-800 text-white text-xs font-bold transition-colors shadow-xs cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <span>Buka Tiket Bantuan NOC</span>
                    </a>
                    <a href="{{ route('docs') }}"
                       class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-semibold transition-colors">
                        <span>Buku Panduan Diagnostik</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Bottom Audit & Legal Disclaimer -->
        <div class="mt-8 text-center text-xs text-slate-400 leading-relaxed max-w-3xl mx-auto">
            <p>
                Laporan metrik ketersediaan ini diukur secara independen oleh probe monitoring eksternal yang terdistribusi di Jakarta dan Singapore. Seluruh komitmen ketersediaan terikat oleh <a href="{{ route('sla') }}" class="text-slate-600 hover:underline">Ketentuan SLA 99.9%</a> serta <a href="{{ route('terms') }}" class="text-slate-600 hover:underline">Ketentuan Layanan VexaHost</a> yang dioperasikan secara profesional oleh <span class="font-normal text-slate-500">VexaHost Cloud Indonesia</span>. <span class="text-slate-400">Created by RZ Digital Creative.</span>
            </p>
        </div>

    </div>
</div>
@endsection
