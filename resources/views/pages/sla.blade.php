@extends('layouts.app', ['title' => 'Service Level Agreement (SLA 99.9%) — VexaHost'])

@section('content')
<div x-data="{
    activeSection: 'pasal-1',
    scrollTo(id) {
        const el = document.getElementById(id);
        if (el) {
            el.scrollIntoView({ behavior: 'smooth' });
            this.activeSection = id;
        }
    }
}" class="min-h-screen bg-slate-50 text-slate-900 selection:bg-slate-900 selection:text-white">

    <!-- Top Sub-Header Bar (Legal Documentation Hub) -->
    <div class="bg-white border-b border-slate-200 sticky top-16 z-30 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-14 flex items-center justify-between">
            <div class="flex items-center space-x-3 overflow-x-auto no-scrollbar py-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 whitespace-nowrap">Portal Legal</span>
                <span class="text-slate-300">/</span>
                <nav class="flex items-center space-x-1 sm:space-x-2 text-xs">
                    <a href="{{ route('terms') }}" class="px-2.5 py-1 rounded-md font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors whitespace-nowrap">
                        Terms of Service
                    </a>
                    <a href="{{ route('privacy') }}" class="px-2.5 py-1 rounded-md font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors whitespace-nowrap">
                        Privacy Policy
                    </a>
                    <a href="{{ route('sla') }}" class="px-2.5 py-1 rounded-md font-bold bg-slate-900 text-white whitespace-nowrap">
                        SLA 99.9%
                    </a>
                    <a href="{{ route('refund') }}" class="px-2.5 py-1 rounded-md font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors whitespace-nowrap">
                        Refund Policy
                    </a>
                </nav>
            </div>

            <div class="hidden sm:flex items-center space-x-3 shrink-0">
                <button onclick="window.print()" type="button" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-600 hover:text-slate-900 text-xs font-semibold transition-colors shadow-2xs">
                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Dokumen</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Header Hero Banner -->
    <header class="bg-white border-b border-slate-200 py-10 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl">
                <div class="flex items-center gap-2 mb-3">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-slate-800 border border-slate-200">
                        Dokumen Komitmen Layanan
                    </span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        Target Uptime: 99.90% Bulanan
                    </span>
                </div>
                <h1 class="text-2xl sm:text-4xl font-bold tracking-tight text-slate-900">
                    Service Level Agreement (SLA 99.9%)
                </h1>
                <p class="mt-3 text-sm text-slate-600 leading-relaxed">
                    Standar jaminan ketersediaan infrastruktur komputasi, keandalan jaringan transmisi datacenter, serta mekanisme kompensasi kredit layanan bagi pelanggan VexaHost.
                </p>

                <!-- Metadata Grid -->
                <div class="mt-6 pt-6 border-t border-slate-100 grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
                    <div>
                        <span class="text-slate-400 block font-medium">Garansi Ketersediaan</span>
                        <span class="font-semibold text-slate-800 mt-0.5 block">99.90% Periode Kalender</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-medium">Batas Toleransi Gangguan</span>
                        <span class="font-semibold text-slate-800 mt-0.5 block">&le; 43 Menit 12 Detik / Bulan</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-medium">Bentuk Kompensasi</span>
                        <span class="font-semibold text-slate-800 mt-0.5 block">Kredit Layanan (Service Credit)</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-medium">Identifikasi Dokumen</span>
                        <span class="font-mono-code font-semibold text-slate-800 mt-0.5 block">SLA-2026-V2</span>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content Layout (Sidebar + Content Body) -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-12">
        <div class="flex flex-col lg:flex-row gap-10">

            <!-- Left Sticky Sidebar Table of Contents -->
            <aside class="w-full lg:w-72 shrink-0">
                <div class="sticky top-36 space-y-6">
                    <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-2xs">
                        <h2 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3 px-2">
                            Daftar Klausul SLA
                        </h2>
                        <nav class="space-y-1 text-xs">
                            <button @click="scrollTo('pasal-1')" class="w-full text-left px-2.5 py-2 rounded-lg font-medium transition-colors text-slate-600 hover:text-slate-900 hover:bg-slate-50 block">
                                1. Ikhtisar Jaminan Uptime
                            </button>
                            <button @click="scrollTo('pasal-2')" class="w-full text-left px-2.5 py-2 rounded-lg font-medium transition-colors text-slate-600 hover:text-slate-900 hover:bg-slate-50 block">
                                2. Metodologi Pengukuran Uptime
                            </button>
                            <button @click="scrollTo('pasal-3')" class="w-full text-left px-2.5 py-2 rounded-lg font-medium transition-colors text-slate-600 hover:text-slate-900 hover:bg-slate-50 block">
                                3. Ruang Lingkup Komponen
                            </button>
                            <button @click="scrollTo('pasal-4')" class="w-full text-left px-2.5 py-2 rounded-lg font-medium transition-colors text-slate-600 hover:text-slate-900 hover:bg-slate-50 block">
                                4. Pengecualian Jaminan SLA
                            </button>
                            <button @click="scrollTo('pasal-5')" class="w-full text-left px-2.5 py-2 rounded-lg font-medium transition-colors text-slate-600 hover:text-slate-900 hover:bg-slate-50 block">
                                5. Matriks Kompensasi Kredit
                            </button>
                            <button @click="scrollTo('pasal-6')" class="w-full text-left px-2.5 py-2 rounded-lg font-medium transition-colors text-slate-600 hover:text-slate-900 hover:bg-slate-50 block">
                                6. Prosedur Klaim Resmi
                            </button>
                            <button @click="scrollTo('pasal-7')" class="w-full text-left px-2.5 py-2 rounded-lg font-medium transition-colors text-slate-600 hover:text-slate-900 hover:bg-slate-50 block">
                                7. Ketentuan Pencairan Kredit
                            </button>
                            <button @click="scrollTo('pasal-8')" class="w-full text-left px-2.5 py-2 rounded-lg font-medium transition-colors text-slate-600 hover:text-slate-900 hover:bg-slate-50 block">
                                8. Batas Tertinggi Liabilitas
                            </button>
                        </nav>
                    </div>

                    <!-- Infrastructure Status Card -->
                    <div class="bg-white rounded-xl border border-slate-200 p-4 text-xs space-y-2.5 shadow-2xs">
                        <div class="font-bold text-slate-900 flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            <span>Status Jaringan &amp; Node</span>
                        </div>
                        <p class="text-slate-500 leading-relaxed">
                            Pemantauan waktu nyata (real-time telemetry) untuk seluruh cluster datacenter:
                        </p>
                        <a href="{{ route('status') }}" class="inline-flex items-center gap-1 font-semibold text-slate-900 hover:underline">
                            <span>Buka Status Page VexaHost</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>
            </aside>

            <!-- Main Legal Article Body -->
            <main class="flex-1 min-w-0 space-y-12">

                <!-- Pasal 1 -->
                <section id="pasal-1" class="scroll-mt-36 bg-white rounded-xl border border-slate-200 p-6 sm:p-8 shadow-2xs">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="px-2.5 py-1 rounded text-xs font-mono-code font-bold bg-slate-100 text-slate-800 border border-slate-200">
                            PASAL 1
                        </span>
                        <h2 class="text-xl font-bold text-slate-900">
                            Ikhtisar Komitmen Jaminan Ketersediaan (Uptime Guarantee)
                        </h2>
                    </div>
                    <div class="space-y-4 text-sm text-slate-600 leading-relaxed">
                        <p>
                            VexaHost berkomitmen menyediakan infrastruktur komputasi awan yang tangguh, stabil, dan berkinerja tinggi. Kami menetapkan target garansi ketersediaan layanan minimal sebesar <strong>99.90% (sembilan puluh sembilan koma sembilan puluh persen)</strong> dalam setiap 1 (satu) periode penagihan bulan kalender.
                        </p>
                        <p>
                            Jaminan ini berlaku untuk seluruh tingkatan paket Cloud VPS, Managed Database, dan AI Agent Runtime yang berstatus aktif dan telah lunas dibayarkan oleh Pelanggan.
                        </p>
                    </div>
                </section>

                <!-- Pasal 2 -->
                <section id="pasal-2" class="scroll-mt-36 bg-white rounded-xl border border-slate-200 p-6 sm:p-8 shadow-2xs">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="px-2.5 py-1 rounded text-xs font-mono-code font-bold bg-slate-100 text-slate-800 border border-slate-200">
                            PASAL 2
                        </span>
                        <h2 class="text-xl font-bold text-slate-900">
                            Metodologi Perhitungan Uptime &amp; Formula Matematis
                        </h2>
                    </div>
                    <div class="space-y-4 text-sm text-slate-600 leading-relaxed">
                        <p>
                            2.1. Persentase uptime bulanan dihitung dengan formula baku industri sebagai berikut:
                        </p>
                        <div class="p-4 rounded-lg bg-slate-50 border border-slate-200 font-mono-code text-xs text-slate-800 my-2">
                            Persentase Uptime = [ (Total Menit Kalender - Total Menit Downtime) / Total Menit Kalender ] &times; 100%
                        </div>
                        <p>
                            2.2. Definisi <strong>Downtime</strong> adalah kondisi ketika alamat IP publik dari instans VPS Pelanggan mengalami kehilangan paket (packet loss) 100% secara terus-menerus selama minimal 5 (lima) menit berurutan, sebagaimana diverifikasi oleh probe monitoring jaringan independen VexaHost.
                        </p>
                        <p>
                            2.3. Dalam bulan kalender standar 30 hari (43.200 menit), toleransi downtime kumulatif maksimal yang diperkenankan untuk memenuhi standar 99.90% adalah <strong>43 menit 12 detik</strong>.
                        </p>
                    </div>
                </section>

                <!-- Pasal 3 -->
                <section id="pasal-3" class="scroll-mt-36 bg-white rounded-xl border border-slate-200 p-6 sm:p-8 shadow-2xs">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="px-2.5 py-1 rounded text-xs font-mono-code font-bold bg-slate-100 text-slate-800 border border-slate-200">
                            PASAL 3
                        </span>
                        <h2 class="text-xl font-bold text-slate-900">
                            Ruang Lingkup Infrastruktur yang Dijamin
                        </h2>
                    </div>
                    <div class="space-y-4 text-sm text-slate-600 leading-relaxed">
                        <p>
                            Garansi SLA mencakup seluruh komponen sistem yang berada di bawah kendali langsung operasional VexaHost:
                        </p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                            <div class="p-3.5 rounded-lg border border-slate-200 bg-slate-50">
                                <strong class="text-slate-900 block mb-1">Perangkat Keras Host Node</strong>
                                Ketersediaan fisik prosesor host (Intel Xeon / AMD EPYC), memori RAM ECC, motherboard server, dan drive Enterprise NVMe SSD RAID-10.
                            </div>
                            <div class="p-3.5 rounded-lg border border-slate-200 bg-slate-50">
                                <strong class="text-slate-900 block mb-1">Jaringan Backbone Datacenter</strong>
                                Ketersediaan router core, switch Top-of-Rack (ToR), dan rute transit BGP upstream di fasilitas datacenter Jakarta (Cloudeka Lintasarta) dan Singapore (Tencent Cloud).
                            </div>
                            <div class="p-3.5 rounded-lg border border-slate-200 bg-slate-50">
                                <strong class="text-slate-900 block mb-1">Pasokan Daya &amp; Fasilitas Tier-3</strong>
                                Redundansi daya listrik ganda (Dual Feed Power), genset diesel backup otomatis, serta sistem pendingin HVAC N+1.
                            </div>
                            <div class="p-3.5 rounded-lg border border-slate-200 bg-slate-50">
                                <strong class="text-slate-900 block mb-1">Hypervisor KVM Host</strong>
                                Ketersediaan kernel virtualisasi layer host dalam mengalokasikan siklus komputasi secara adil tanpa overcommit agresif.
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Pasal 4 -->
                <section id="pasal-4" class="scroll-mt-36 bg-white rounded-xl border border-slate-200 p-6 sm:p-8 shadow-2xs">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="px-2.5 py-1 rounded text-xs font-mono-code font-bold bg-slate-100 text-slate-800 border border-slate-200">
                            PASAL 4
                        </span>
                        <h2 class="text-xl font-bold text-slate-900">
                            Pengecualian Jaminan SLA (Exclusions)
                        </h2>
                    </div>
                    <div class="space-y-4 text-sm text-slate-600 leading-relaxed">
                        <p>
                            Penurunan performa, latensi tinggi sesaat, atau ketidaktersediaan server <strong>TIDAK dihitung</strong> sebagai pelanggaran SLA apabila disebabkan oleh salah satu faktor di bawah ini:
                        </p>
                        <ul class="space-y-2 pl-4 border-l-2 border-slate-200 text-xs">
                            <li>
                                <strong>Pemeliharaan Terjadwal (Scheduled Maintenance):</strong> Pekerjaan peningkatan firmware, patching keamanan hypervisor, atau penataan jaringan yang telah diumumkan minimal 24 (dua puluh empat) jam sebelumnya melalui email resmi atau halaman status.
                            </li>
                            <li>
                                <strong>Kesalahan Konfigurasi Internal Pengguna:</strong> Kegagalan sistem akibat Pelanggan salah mengubah konfigurasi firewall (UFW/iptables), kesalahan edit file SSH daemon, kernel panic akibat kompilasi manual, atau penguncian akses port mandiri.
                            </li>
                            <li>
                                <strong>Kehabisan Resource Guest OS:</strong> Server yang tidak merespons akibat Out-of-Memory (OOM killer), pemenuhan kapasitas storage disk 100%, atau loop tak terbatas dari aplikasi internal Pelanggan.
                            </li>
                            <li>
                                <strong>Tindakan Disipliner AUP:</strong> Penghentian sementara (suspension) yang dilakukan oleh sistem atau admin akibat indikasi pelanggaran kebijakan penggunaan (DDoS, spamming, botnet, dsb).
                            </li>
                            <li>
                                <strong>Keadaan Kahar (Force Majeure):</strong> Bencana alam skala nasional, peperangan, huru-hara, kebakaran datacenter massal di luar batas kendali manusia, atau putusnya kabel fiber optik bawah laut internasional.
                            </li>
                        </ul>
                    </div>
                </section>

                <!-- Pasal 5 -->
                <section id="pasal-5" class="scroll-mt-36 bg-white rounded-xl border border-slate-200 p-6 sm:p-8 shadow-2xs">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="px-2.5 py-1 rounded text-xs font-mono-code font-bold bg-slate-100 text-slate-800 border border-slate-200">
                            PASAL 5
                        </span>
                        <h2 class="text-xl font-bold text-slate-900">
                            Matriks Kompensasi Kredit Layanan (Service Credit)
                        </h2>
                    </div>
                    <div class="space-y-4 text-sm text-slate-600 leading-relaxed">
                        <p>
                            Jika ketersediaan bulanan aktual berada di bawah jaminan 99.90%, Pelanggan berhak menerima Service Credit berdasarkan tabel perhitungan berikut:
                        </p>

                        <!-- SLA Compensation Table -->
                        <div class="overflow-x-auto my-4">
                            <table class="w-full text-left border-collapse border border-slate-200 text-xs">
                                <thead>
                                    <tr class="bg-slate-100 text-slate-900">
                                        <th class="p-3 border border-slate-200 font-bold w-1/3">Persentase Uptime Aktual Bulanan</th>
                                        <th class="p-3 border border-slate-200 font-bold w-1/3">Durasi Downtime Kumulatif</th>
                                        <th class="p-3 border border-slate-200 font-bold">Besaran Kredit Kompensasi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200">
                                    <tr>
                                        <td class="p-3 border border-slate-200 font-mono-code font-semibold text-slate-900">99.00% – 99.89%</td>
                                        <td class="p-3 border border-slate-200">44 Menit &ndash; 4 Jam 20 Menit</td>
                                        <td class="p-3 border border-slate-200 font-semibold text-emerald-700">10% Biaya Sewa Bulanan</td>
                                    </tr>
                                    <tr>
                                        <td class="p-3 border border-slate-200 font-mono-code font-semibold text-slate-900">95.00% – 98.99%</td>
                                        <td class="p-3 border border-slate-200">4 Jam 21 Menit &ndash; 21 Jam 36 Menit</td>
                                        <td class="p-3 border border-slate-200 font-semibold text-emerald-700">25% Biaya Sewa Bulanan</td>
                                    </tr>
                                    <tr>
                                        <td class="p-3 border border-slate-200 font-mono-code font-semibold text-slate-900">&lt; 95.00%</td>
                                        <td class="p-3 border border-slate-200">&gt; 21 Jam 36 Menit</td>
                                        <td class="p-3 border border-slate-200 font-semibold text-emerald-700">50% Biaya Sewa Bulanan</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

                <!-- Pasal 6 -->
                <section id="pasal-6" class="scroll-mt-36 bg-white rounded-xl border border-slate-200 p-6 sm:p-8 shadow-2xs">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="px-2.5 py-1 rounded text-xs font-mono-code font-bold bg-slate-100 text-slate-800 border border-slate-200">
                            PASAL 6
                        </span>
                        <h2 class="text-xl font-bold text-slate-900">
                            Tata Cara &amp; Batas Waktu Pengajuan Klaim Resmi
                        </h2>
                    </div>
                    <div class="space-y-4 text-sm text-slate-600 leading-relaxed">
                        <p>
                            6.1. Klaim Service Credit tidak diberikan secara otomatis. Pelanggan wajib mengajukan permohonan resmi melalui <strong>Tiket Bantuan Dashboard VexaHost</strong> dalam waktu maksimal <strong>7 (tujuh) hari kalender</strong> terhitung sejak insiden gangguan berakhir.
                        </p>
                        <p>
                            6.2. Permohonan klaim wajib menyertakan rincian data:
                        </p>
                        <ul class="list-disc list-inside space-y-1 pl-2 text-xs text-slate-700">
                            <li>Nomor ID Layanan VPS atau Alamat IP Publik terkait.</li>
                            <li>Waktu tanggal dan jam mulai gangguan beserta estimasi durasi downtime.</li>
                            <li>Log traceroute, ping, atau bukti diagnostik jaringan saat insiden terjadi (jika ada).</li>
                        </ul>
                        <p>
                            6.3. Tim Operasional Jaringan (NOC) VexaHost akan memvalidasi klaim terhadap rekaman telemetri node dalam waktu maksimal 3 (tiga) hari kerja operasional.
                        </p>
                    </div>
                </section>

                <!-- Pasal 7 -->
                <section id="pasal-7" class="scroll-mt-36 bg-white rounded-xl border border-slate-200 p-6 sm:p-8 shadow-2xs">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="px-2.5 py-1 rounded text-xs font-mono-code font-bold bg-slate-100 text-slate-800 border border-slate-200">
                            PASAL 7
                        </span>
                        <h2 class="text-xl font-bold text-slate-900">
                            Ketentuan Pencairan &amp; Penggunaan Kredit Layanan
                        </h2>
                    </div>
                    <div class="space-y-4 text-sm text-slate-600 leading-relaxed">
                        <p>
                            7.1. Service Credit yang telah disetujui akan dimasukkan sebagai saldo kredit akun atau pemotongan nominal secara langsung pada faktur tagihan perpanjangan (invoice renewal) periode berikutnya.
                        </p>
                        <p>
                            7.2. Service Credit <strong>tidak dapat dicairkan dalam bentuk uang tunai</strong>, tidak dapat ditransfer antar akun pengguna yang berbeda, dan tidak dapat digunakan untuk transaksi di luar ekosistem VexaHost.
                        </p>
                    </div>
                </section>

                <!-- Pasal 8 -->
                <section id="pasal-8" class="scroll-mt-36 bg-white rounded-xl border border-slate-200 p-6 sm:p-8 shadow-2xs">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="px-2.5 py-1 rounded text-xs font-mono-code font-bold bg-slate-100 text-slate-800 border border-slate-200">
                            PASAL 8
                        </span>
                        <h2 class="text-xl font-bold text-slate-900">
                            Batas Tertinggi Liabilitas Kompensasi
                        </h2>
                    </div>
                    <div class="space-y-4 text-sm text-slate-600 leading-relaxed">
                        <p>
                            Total kumulatif Service Credit yang dapat diberikan kepada Pelanggan dalam 1 (satu) bulan penagihan dibatasi maksimal sebesar <strong>50% (lima puluh persen)</strong> dari total biaya langganan bulanan unit server yang mengalami gangguan.
                        </p>
                        <p>
                            Pemberian Service Credit merupakan satu-satunya upaya hukum dan ganti rugi eksklusif yang tersedia bagi Pelanggan atas kegagalan ketersediaan layanan berdasarkan perjanjian SLA ini.
                        </p>
                    </div>

                    <!-- Sign-off block -->
                    <div class="mt-8 pt-6 border-t border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 text-xs text-slate-500">
                        <div>
                            <p class="font-normal text-slate-900">VexaHost Cloud Indonesia</p>
                            <p class="text-slate-500 mt-0.5">Departemen Network Operations Center (NOC) &amp; Jaminan Mutu Infrastruktur</p>
                        </div>
                        <div class="font-mono-code text-[11px] text-slate-400">
                            Dokumen Resmi: SLA-2026-V2
                        </div>
                    </div>
                </section>
            </main>
        </div>
    </div>
</div>
@endsection
