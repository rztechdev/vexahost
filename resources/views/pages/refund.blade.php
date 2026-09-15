@extends('layouts.app', ['title' => 'Kebijakan Pengembalian Dana (Refund Policy) — VexaHost'])

@section('content')
<section class="bg-[#0B0F19] text-white pt-16 pb-12 border-b border-slate-800">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex items-center space-x-2 text-xs text-slate-400 mb-6 font-mono-code">
            <a href="{{ route('home') }}" class="hover:text-white transition-colors">Beranda</a>
            <span>/</span>
            <span class="text-[#6588BC]">Kebijakan Refund</span>
        </nav>
        <h1 class="text-3xl sm:text-4xl font-bold tracking-tight text-white mb-3">
            Kebijakan Pengembalian Dana &amp; Pembatalan
        </h1>
        <p class="text-xs text-slate-400 font-mono-code">
            Transparansi garansi layanan &bull; RZ Digital Creative
        </p>
    </div>
</section>

<section class="py-14 bg-white border-b border-slate-200">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 prose prose-slate max-w-none text-sm leading-relaxed text-slate-600 space-y-8">
        <div>
            <h2 class="text-lg font-bold text-slate-900 mb-3">1. Komitmen Kepuasan Pelanggan</h2>
            <p>
                Di VexaHost (RZ Digital Creative), kami selalu berusaha memberikan performa server cloud terbaik dan stabilitas tinggi. Jika Anda menghadapi kendala teknis fundamental pada infrastruktur kami yang tidak mampu diselesaikan oleh tim dukungan kami, kami menyediakan ketentuan pengembalian dana (refund) yang jelas dan adil.
            </p>
        </div>

        <div>
            <h2 class="text-lg font-bold text-slate-900 mb-3">2. Garansi Teknis 48 Jam Pertama</h2>
            <p>
                Pelanggan baru berhak mengajukan pengembalian dana 100% dalam waktu <strong>48 jam sejak server aktif pertama kali</strong>, apabila:
            </p>
            <ul class="list-disc list-inside space-y-1.5 pl-2 text-slate-700">
                <li>Terjadi kegagalan hardware, routing jaringan, atau kerusakan virtualisasi KVM dari sisi VexaHost yang tidak dapat diperbaiki dalam 24 jam.</li>
                <li>Spesifikasi compute vCPU, RAM, atau storage yang dialokasikan tidak sesuai dengan paket yang dipesan.</li>
            </ul>
        </div>

        <div>
            <h2 class="text-lg font-bold text-slate-900 mb-3">3. Kondisi Pengecualian (Non-Refundable)</h2>
            <p class="mb-2">Permintaan refund tidak berlaku dalam kondisi berikut:</p>
            <ul class="list-disc list-inside space-y-1.5 pl-2 text-slate-700">
                <li>Server yang disuspend atau diterminasi akibat pelanggaran <em>Acceptable Use Policy</em> (AUP), seperti port scanning, spam email, DDoS, bot ilegal, atau penambangan kripto.</li>
                <li>Pengguna salah memilih sistem operasi atau salah mengonfigurasi firewall internal sehingga terkunci dari server.</li>
                <li>Kelebihan kuota traffic atau penggunaan resource aplikasi pihak ketiga yang tidak kompatibel dengan arsitektur Linux standar.</li>
                <li>Faktur perpanjangan masa aktif (renewal) untuk bulan kedua dan seterusnya.</li>
                <li>Pengajuan yang diajukan setelah melewati batas waktu 48 jam sejak instans di-provisioning.</li>
            </ul>
        </div>

        <div>
            <h2 class="text-lg font-bold text-slate-900 mb-3">4. Pesanan Melalui Toko Resmi Shopee</h2>
            <p>
                Untuk transaksi yang dilakukan melalui platform Shopee, kebijakan pembatalan dan garansi pengembalian dana tunduk pada mekanisme Garansi Shopee. Pastikan Anda menghubungi tim admin kami melalui chat Shopee sebelum mengonfirmasi penerimaan pesanan.
            </p>
        </div>

        <div>
            <h2 class="text-lg font-bold text-slate-900 mb-3">5. Tata Cara &amp; Durasi Pengembalian</h2>
            <p>
                Untuk mengajukan permohonan pengembalian dana, kirimkan tiket bantuan di dashboard atau hubungi WhatsApp Customer Service dengan menyertakan Nomor Invoice dan alasan kendala teknis. Jika disetujui, dana akan dikembalikan ke rekening bank lokal (BCA, Mandiri, BRI, BNI) atau e-wallet (GoPay, OVO, DANA) Anda dalam waktu 1–3 hari kerja operasional bank.
            </p>
        </div>
    </div>
</section>
@endsection
