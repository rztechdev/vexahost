@extends('layouts.app', ['title' => 'Service Level Agreement (SLA) — VexaHost'])

@section('content')
<section class="bg-[#0B0F19] text-white pt-16 pb-12 border-b border-slate-800">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex items-center space-x-2 text-xs text-slate-400 mb-6 font-mono-code">
            <a href="{{ route('home') }}" class="hover:text-white transition-colors">Beranda</a>
            <span>/</span>
            <span class="text-[#6588BC]">Service Level Agreement</span>
        </nav>
        <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs font-semibold mb-3">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
            99.9% Uptime Guarantee
        </div>
        <h1 class="text-3xl sm:text-4xl font-bold tracking-tight text-white mb-3">
            Service Level Agreement (SLA)
        </h1>
        <p class="text-xs text-slate-400 font-mono-code">
            Komitmen keandalan infrastruktur cloud VexaHost &bull; RZ Digital Creative
        </p>
    </div>
</section>

<section class="py-14 bg-white border-b border-slate-200">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 prose prose-slate max-w-none text-sm leading-relaxed text-slate-600 space-y-8">
        <div>
            <h2 class="text-lg font-bold text-slate-900 mb-3">1. Ikhtisar Komitmen</h2>
            <p>
                VexaHost berkomitmen menyediakan infrastruktur Virtual Private Server (VPS) yang berdaya tahan tinggi, stabil, dan andal. Dokumen Service Level Agreement (SLA) ini menjabarkan standar ketersediaan jaringan, perangkat keras, dan skema kompensasi jika terjadi gangguan layanan di luar toleransi operasional kami.
            </p>
        </div>

        <div>
            <h2 class="text-lg font-bold text-slate-900 mb-3">2. Jaminan Uptime 99.9% Bulanan</h2>
            <p>
                Kami menjamin ketersediaan jaringan dan node server KVM minimal sebesar <strong>99.9%</strong> dalam setiap periode penagihan 1 (satu) bulan kalender. Perhitungan uptime diukur oleh sistem monitoring independen kami pada port jaringan router core dan host node server.
            </p>
        </div>

        <div>
            <h2 class="text-lg font-bold text-slate-900 mb-3">3. Cakupan Garansi SLA</h2>
            <p class="mb-2">Garansi ketersediaan ini mencakup:</p>
            <ul class="list-disc list-inside space-y-1.5 pl-2 text-slate-700">
                <li><strong>Ketersediaan Jaringan Backbone:</strong> Rute transit upstream dan peering datacenter di Singapore maupun Jakarta.</li>
                <li><strong>Ketersediaan Daya &amp; Fasilitas:</strong> Sistem UPS N+1 dan generator pendingin datacenter Tier-3.</li>
                <li><strong>Integritas Hardware Host Node:</strong> Ketersediaan fisik prosesor, memori RAM, controller RAID, dan drive NVMe SSD.</li>
            </ul>
        </div>

        <div>
            <h2 class="text-lg font-bold text-slate-900 mb-3">4. Pengecualian SLA (Exclusions)</h2>
            <p class="mb-2">Penurunan performa atau downtime tidak dihitung dalam garansi SLA apabila disebabkan oleh:</p>
            <ul class="list-disc list-inside space-y-1 pl-2 text-slate-700">
                <li>Pemeliharaan terjadwal (Scheduled Maintenance) yang diumumkan minimal 24 jam sebelumnya melalui email/status page.</li>
                <li>Kesalahan konfigurasi sistem oleh Pengguna (contoh: iptables/firewall memblokir port SSH sendiri, kernel panic akibat compile manual).</li>
                <li>Penggunaan berlebih (exhaustion) resource disk space atau RAM oleh software Pelanggan yang menyebabkan OS freeze.</li>
                <li>Keadaan kahar (Force Majeure) seperti gempa bumi, banjir bencana alam, pemadaman listrik massal nasional, atau perang.</li>
                <li>Serangan siber masif yang melampaui proteksi mitigasi standar hingga dialihkan ke sinkhole routing.</li>
            </ul>
        </div>

        <div>
            <h2 class="text-lg font-bold text-slate-900 mb-3">5. Skema Kompensasi (Service Credit)</h2>
            <div class="overflow-x-auto my-4">
                <table class="w-full text-left border-collapse border border-slate-200 text-xs">
                    <thead>
                        <tr class="bg-slate-100 text-slate-800">
                            <th class="p-3 border border-slate-200 font-bold">Uptime Bulanan Aktual</th>
                            <th class="p-3 border border-slate-200 font-bold">Besaran Kredit Layanan (Kompensasi)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        <tr>
                            <td class="p-3 border border-slate-200 font-mono-code">99.00% – 99.89%</td>
                            <td class="p-3 border border-slate-200">10% dari biaya sewa bulanan paket terkait</td>
                        </tr>
                        <tr>
                            <td class="p-3 border border-slate-200 font-mono-code">95.00% – 98.99%</td>
                            <td class="p-3 border border-slate-200">25% dari biaya sewa bulanan paket terkait</td>
                        </tr>
                        <tr>
                            <td class="p-3 border border-slate-200 font-mono-code">&lt; 95.00%</td>
                            <td class="p-3 border border-slate-200">50% dari biaya sewa bulanan paket terkait</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <p class="text-xs text-slate-500">
                Kredit layanan akan dipotongkan secara otomatis pada faktur perpanjangan bulan berikutnya dan tidak dapat diuangkan (non-refundable cash).
            </p>
        </div>

        <div>
            <h2 class="text-lg font-bold text-slate-900 mb-3">6. Tata Cara Pengajuan Klaim</h2>
            <p>
                Klaim SLA dapat diajukan oleh pemilik akun aktif melalui tiket bantuan resmi di dashboard VexaHost dengan menyertakan catatan waktu downtime dalam waktu maksimal 7 (tujuh) hari kalender sejak insiden berakhir.
            </p>
        </div>
    </div>
</section>
@endsection
