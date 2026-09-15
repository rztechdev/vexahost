@extends('layouts.app', ['title' => 'Status Sistem & Uptime — VexaHost'])

@section('content')
<!-- Status Banner Hero -->
<section class="bg-[#0B0F19] text-white pt-16 pb-12 border-b border-slate-800">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumbs -->
        <nav class="flex items-center space-x-2 text-xs text-slate-400 mb-6 font-mono-code">
            <a href="{{ route('home') }}" class="hover:text-white transition-colors">Beranda</a>
            <span>/</span>
            <span class="text-[#6588BC]">Status Sistem</span>
        </nav>

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h1 class="text-3xl sm:text-4xl font-bold tracking-tight text-white mb-2">
                    Status Sistem &amp; Infrastruktur
                </h1>
                <p class="text-slate-400 text-sm">
                    Pemantauan real-time ketersediaan node server, konektivitas datacenter, dan layanan VexaHost.
                </p>
            </div>

            <!-- Overall Badge -->
            <div class="inline-flex items-center gap-3 px-4 py-2.5 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400">
                <span class="relative flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                </span>
                <span class="text-sm font-bold">Semua Sistem Beroperasi Normal</span>
            </div>
        </div>
    </div>
</section>

<!-- Detailed Component Health -->
<section class="py-14 bg-slate-50 border-b border-slate-200">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">

        <!-- 90 Days SLA Card -->
        <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-xs">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6 pb-4 border-b border-slate-100">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Uptime Rata-rata 90 Hari Terakhir</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Metrik ketersediaan hardware &amp; jaringan independen</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-2xl font-black text-emerald-600 font-mono-code">99.9%</span>
                    <span class="text-xs font-semibold px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200">SLA Memenuhi</span>
                </div>
            </div>

            <!-- Mini bars visualization -->
            <div class="flex items-center gap-[3px] overflow-hidden py-1">
                @for ($i = 0; $i < 60; $i++)
                    <div class="h-8 flex-1 rounded-[2px] bg-emerald-500 hover:bg-emerald-400 transition-colors" title="Hari {{ 60 - $i }} lalu: 100% operational"></div>
                @endfor
            </div>
            <div class="flex justify-between text-[11px] text-slate-400 mt-2 font-mono-code">
                <span>60 hari lalu</span>
                <span>Hari ini (100% uptime)</span>
            </div>
        </div>

        <!-- Infrastructure Nodes Group -->
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-xs">
            <div class="px-6 py-4 bg-slate-100/70 border-b border-slate-200 flex justify-between items-center">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-700">Cluster Datacenter &amp; Node Hardware</span>
                <span class="text-xs text-slate-500 font-mono-code">Auto-check per 60 detik</span>
            </div>
            <div class="divide-y divide-slate-100">
                <!-- SG Node -->
                <div class="p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-slate-50/50 transition-colors">
                    <div>
                        <div class="flex items-center gap-2">
                            <h4 class="text-sm font-bold text-slate-900">Datacenter Singapore (Cluster SG-01)</h4>
                            <span class="text-[10px] bg-blue-50 text-[#4A6FA5] font-semibold px-1.5 py-0.5 rounded border border-blue-200">KVM Host</span>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">Tier-3 International Backbone &bull; Rata-rata Latensi: 18ms &bull; Paket Loss: 0.0%</p>
                    </div>
                    <div class="flex items-center gap-2 self-start sm:self-center">
                        <span class="inline-block w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span class="text-xs font-semibold text-emerald-700">Operational</span>
                    </div>
                </div>

                <!-- JKT Node -->
                <div class="p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-slate-50/50 transition-colors">
                    <div>
                        <div class="flex items-center gap-2">
                            <h4 class="text-sm font-bold text-slate-900">Datacenter Jakarta (Cyber 1 Building)</h4>
                            <span class="text-[10px] bg-blue-50 text-[#4A6FA5] font-semibold px-1.5 py-0.5 rounded border border-blue-200">KVM Host</span>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">Direct IIX / OpenIXP Peering &bull; Rata-rata Latensi: 3ms &bull; Paket Loss: 0.0%</p>
                    </div>
                    <div class="flex items-center gap-2 self-start sm:self-center">
                        <span class="inline-block w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span class="text-xs font-semibold text-emerald-700">Operational</span>
                    </div>
                </div>

                <!-- Storage Array -->
                <div class="p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-slate-50/50 transition-colors">
                    <div>
                        <h4 class="text-sm font-bold text-slate-900">NVMe RAID-10 Storage Clusters</h4>
                        <p class="text-xs text-slate-500 mt-1">IOPS Health Normal &bull; Read/Write Throughput stabil</p>
                    </div>
                    <div class="flex items-center gap-2 self-start sm:self-center">
                        <span class="inline-block w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span class="text-xs font-semibold text-emerald-700">Operational</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Platform & Core Services -->
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-xs">
            <div class="px-6 py-4 bg-slate-100/70 border-b border-slate-200 flex justify-between items-center">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-700">Layanan Platform &amp; Transaksi</span>
                <span class="text-xs text-slate-500 font-mono-code">Status API</span>
            </div>
            <div class="divide-y divide-slate-100">
                <div class="p-5 flex items-center justify-between">
                    <div>
                        <h4 class="text-sm font-bold text-slate-900">Customer Dashboard &amp; Web Portal</h4>
                        <p class="text-xs text-slate-500 mt-0.5">Akses panel kontrol, reboot VPS &amp; manajemen tagihan</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-block w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span class="text-xs font-semibold text-emerald-700">Operational</span>
                    </div>
                </div>

                <div class="p-5 flex items-center justify-between">
                    <div>
                        <h4 class="text-sm font-bold text-slate-900">Payment Gateway &amp; Webhook (Midtrans / QRIS)</h4>
                        <p class="text-xs text-slate-500 mt-0.5">Verifikasi pembayaran instan 24 jam</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-block w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span class="text-xs font-semibold text-emerald-700">Operational</span>
                    </div>
                </div>

                <div class="p-5 flex items-center justify-between">
                    <div>
                        <h4 class="text-sm font-bold text-slate-900">Automated Provisioning Engine</h4>
                        <p class="text-xs text-slate-500 mt-0.5">Sistem pembuatan &amp; instalasi otomatis server VPS baru</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-block w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span class="text-xs font-semibold text-emerald-700">Operational</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Incident History -->
        <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-xs">
            <h3 class="text-base font-bold text-slate-900 mb-4">Riwayat Pemeliharaan &amp; Insiden</h3>
            <div class="space-y-4 text-xs text-slate-600">
                <div class="border-l-2 border-emerald-500 pl-4 py-1">
                    <p class="font-bold text-slate-800 text-sm">Pemeliharaan Terjadwal Selesai: Optimasi Jaringan Core Jakarta</p>
                    <p class="text-slate-400 font-mono-code mt-0.5">10 September 2026 &bull; 02:00 - 02:30 WIB</p>
                    <p class="mt-1 text-slate-600">Pembaruan firmware switch core Gedung Cyber Jakarta selesai tanpa gangguan downtime pada instance pengguna.</p>
                </div>
                <div class="border-l-2 border-slate-300 pl-4 py-1">
                    <p class="font-bold text-slate-800 text-sm">Tidak Ada Insiden Besar Dilaporkan</p>
                    <p class="text-slate-400 font-mono-code mt-0.5">Dalam 90 hari terakhir</p>
                    <p class="mt-1 text-slate-500">Seluruh cluster KVM berjalan normal dengan SLA ketersediaan 99.9%.</p>
                </div>
            </div>
        </div>

    </div>
</section>
@endsection
