@extends('layouts.app', ['title' => 'Status Layanan — VexaHost'])

@section('content')
{{--
    Halaman status publik.

    Seluruh isi berasal dari data nyata yang dikelola admin:
      - system_components   : status tiap komponen (Admin > Pengaturan)
      - maintenance_windows : jadwal pemeliharaan (Admin > Maintenance)

    Sengaja TIDAK menampilkan persentase uptime, grafik 90 hari, atau latensi:
    VexaHost tidak memiliki pemantauan otomatis atas server supplier, jadi
    angka seperti itu tidak bisa dipertanggungjawabkan.
--}}
<div class="bg-slate-50 min-h-screen pb-20">

    <!-- Hero Header Banner -->
    <section class="bg-[#0B0F19] text-white border-b border-slate-800 pt-12 pb-14 relative overflow-hidden">
        <div class="absolute inset-0 opacity-[0.03] pointer-events-none"
             style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 24px 24px;"></div>

        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <!-- Breadcrumbs -->
            <nav class="flex items-center space-x-2 text-xs text-slate-400 mb-6 font-mono-code">
                <a href="{{ route('home') }}" class="hover:text-white transition-colors">Beranda</a>
                <span class="text-slate-600">/</span>
                <span class="text-[#6588BC]">Status Layanan</span>
            </nav>

            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 pb-2">
                <div>
                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold tracking-tight text-white">
                        Status Layanan VexaHost
                    </h1>
                    <p class="text-slate-400 text-xs sm:text-sm mt-2 max-w-2xl leading-relaxed">
                        Keadaan terkini komponen layanan dan jadwal pemeliharaan. Status diperbarui oleh tim VexaHost.
                    </p>
                </div>

                <!-- Ringkasan status keseluruhan -->
                <div class="shrink-0">
                    @if(!($allOperational ?? true) || ($globalMaintenance ?? false))
                        <div class="inline-flex items-center gap-3.5 px-5 py-3.5 rounded-xl bg-sky-500/10 border border-sky-500/30 text-sky-400">
                            <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-sky-500 shrink-0"></span>
                            <div class="text-sm font-bold text-sky-300 tracking-wide">
                                {{ ($globalMaintenance ?? false) ? 'Sistem Dalam Pemeliharaan' : 'Sebagian Layanan Terganggu' }}
                            </div>
                        </div>
                    @else
                        <div class="inline-flex items-center gap-3.5 px-5 py-3.5 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400">
                            <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-emerald-500 shrink-0"></span>
                            <div class="text-sm font-bold text-emerald-300 tracking-wide">Semua Sistem Beroperasi Normal</div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-slate-800/80 text-[11px] text-slate-400 font-mono-code">
                Zona Waktu: Asia/Jakarta (WIB) &bull; UTC+7
            </div>
        </div>
    </section>

    <!-- Main Container -->
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 mt-10 space-y-6">

        {{-- Maintenance yang sedang berjalan (tabel maintenance_windows). --}}
        @if(!empty($runningWindows) && count($runningWindows) > 0)
            <div class="bg-white rounded-xl border border-sky-200 p-5">
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

        {{-- Maintenance yang akan datang. --}}
        @if(!empty($upcomingWindows) && count($upcomingWindows) > 0)
            <div class="bg-white rounded-xl border border-amber-200 p-5">
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

        {{-- Status per komponen (tabel system_components). --}}
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <h2 class="text-sm font-bold text-slate-900 mb-1">Status Komponen Layanan</h2>
            <p class="text-xs text-slate-500 mb-4">Keadaan terkini tiap komponen inti platform.</p>

            @if(!empty($components) && count($components) > 0)
                <div class="space-y-2">
                    @foreach($components as $component)
                        <div class="flex items-center justify-between gap-3 px-4 py-3 rounded-lg border border-slate-200">
                            <div class="min-w-0">
                                <div class="text-sm font-bold text-slate-900">{{ $component->name }}</div>
                                <div class="text-[11px] text-slate-500">
                                    {{ $component->status_note ?: $component->description }}
                                </div>
                            </div>
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded text-[11px] font-semibold border shrink-0 {{ $component->status_classes }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $component->status_dot_class }}"></span>
                                {{ $component->status_label }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-xs text-slate-500">Belum ada komponen yang ditampilkan.</p>
            @endif
        </div>

        <!-- Bantuan -->
        <div class="bg-white rounded-xl border border-slate-200 p-6 sm:p-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="space-y-1.5">
                    <h3 class="text-base font-bold text-slate-900">Mengalami Kendala pada Server Anda?</h3>
                    <p class="text-xs text-slate-600 max-w-2xl leading-relaxed">
                        Jika server Anda tidak bisa diakses, laporkan dari halaman detail VPS di dashboard atau buka tiket bantuan. Tim kami akan menanganinya pada jam kerja.
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-3 shrink-0">
                    <a href="{{ auth()->check() ? route('dashboard.support') : route('home') . '#kontak' }}"
                       class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-black hover:bg-neutral-800 text-white text-xs font-bold transition-colors">
                        Buka Tiket Bantuan
                    </a>
                    <a href="{{ route('docs') }}"
                       class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-semibold transition-colors">
                        Dokumentasi
                    </a>
                </div>
            </div>
        </div>

        <!-- Catatan -->
        <div class="text-center text-xs text-slate-400 leading-relaxed max-w-3xl mx-auto">
            <p>
                Status di halaman ini diperbarui secara manual oleh tim VexaHost. Penggunaan layanan tunduk pada
                <a href="{{ route('terms') }}" class="text-slate-600 hover:underline">Ketentuan Layanan VexaHost</a>.
                <span class="text-slate-400">Created by RZ Digital Creative.</span>
            </p>
        </div>

    </div>
</div>
@endsection
