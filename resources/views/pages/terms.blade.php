@extends('layouts.app', ['title' => 'Ketentuan Layanan (Terms of Service) — VexaHost'])

@section('content')
<section class="bg-[#0B0F19] text-white pt-16 pb-12 border-b border-slate-800">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex items-center space-x-2 text-xs text-slate-400 mb-6 font-mono-code">
            <a href="{{ route('home') }}" class="hover:text-white transition-colors">Beranda</a>
            <span>/</span>
            <span class="text-[#6588BC]">Ketentuan Layanan</span>
        </nav>
        <h1 class="text-3xl sm:text-4xl font-bold tracking-tight text-white mb-3">
            Ketentuan Layanan (Terms of Service)
        </h1>
        <p class="text-xs text-slate-400 font-mono-code">
            Terakhir diperbarui: 13 September 2026 &bull; Diterbitkan oleh RZ Digital Creative
        </p>
    </div>
</section>

<section class="py-14 bg-white border-b border-slate-200">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 prose prose-slate max-w-none text-sm leading-relaxed text-slate-600 space-y-8">
        <div>
            <h2 class="text-lg font-bold text-slate-900 mb-3">1. Pendahuluan</h2>
            <p>
                Selamat datang di VexaHost (selanjutnya disebut "Layanan", "Kami", atau "Platform"), yang dimiliki dan dioperasikan oleh <strong>RZ Digital Creative</strong>. Dengan mendaftar, memesan, atau menggunakan layanan VPS cloud kami, Anda ("Pengguna" atau "Pelanggan") menyatakan telah membaca, memahami, dan menyetujui seluruh ketentuan dalam perjanjian ini.
            </p>
        </div>

        <div>
            <h2 class="text-lg font-bold text-slate-900 mb-3">2. Penyediaan Layanan &amp; Akses Server</h2>
            <p>
                VexaHost menyediakan layanan Virtual Private Server (VPS) berbasis teknologi virtualisasi KVM dengan akses root SSH penuh. Pengguna bertanggung jawab penuh atas segala bentuk konfigurasi sistem operasi, software, script, dan data yang dijalankan pada server masing-masing.
            </p>
        </div>

        <div id="aup">
            <h2 class="text-lg font-bold text-slate-900 mb-3">3. Kebijakan Penggunaan yang Diizinkan (Acceptable Use Policy - AUP)</h2>
            <p class="mb-3">
                Pengguna dilarang keras menggunakan infrastruktur VexaHost untuk aktivitas ilegal, merusak, atau melanggar peraturan perundang-undangan Republik Indonesia dan hukum internasional, termasuk namun tidak terbatas pada:
            </p>
            <ul class="list-disc list-inside space-y-1.5 pl-2 text-slate-700">
                <li>Melakukan serangan Denial of Service (DDoS/DoS) atau bertindak sebagai sumber traffic serangan siber.</li>
                <li>Pengiriman email massal yang tidak diminta (SPAM / Email Phishing).</li>
                <li>Penambangan mata uang kripto (Crypto Mining) tanpa izin khusus yang membebani utilitas CPU 100% konstan.</li>
                <li>Penyebaran malware, ransomware, botnet controller, virus, atau alat hacking.</li>
                <li>Penyebaran konten perjudian online, pornografi, ujaran kebencian, atau materi yang melanggar hak cipta (DMCA).</li>
                <li>Aktivitas pemindaian port publik (Port Scanning) secara agresif terhadap host eksternal.</li>
            </ul>
            <p class="mt-3 text-xs text-rose-700 bg-rose-50 border border-rose-200 p-3 rounded-lg">
                <strong>Peringatan Tegas:</strong> Pelanggaran terhadap poin AUP di atas akan mengakibatkan pemutusan instan (suspension/termination) layanan tanpa pengembalian dana (no refund) dan pelaporan data kepada pihak penegak hukum yang berwenang.
            </p>
        </div>

        <div>
            <h2 class="text-lg font-bold text-slate-900 mb-3">4. Pembayaran, Faktur, &amp; Perpanjangan</h2>
            <p>
                Semua biaya sewa server dibayarkan di muka (prepaid) sesuai siklus penagihan bulanan yang dipilih. Faktur tagihan perpanjangan akan diterbitkan 7 (tujuh) hari kalender sebelum masa aktif berakhir. Layanan yang tidak diperpanjang hingga melewati tanggal jatuh tempo akan dihentikan sementara (suspend) dan data akan dihapus permanen 3 (tiga) hari setelah tanggal jatuh tempo.
            </p>
        </div>

        <div>
            <h2 class="text-lg font-bold text-slate-900 mb-3">5. Pencadangan Data (Backup) &amp; Tanggung Jawab</h2>
            <p>
                Meskipun VexaHost menggunakan arsitektur storage NVMe RAID-10 dengan proteksi redundansi perangkat keras, Pelanggan bertanggung jawab penuh untuk melakukan pencadangan data (backup) aplikasi secara teratur ke lokasi penyimpanan eksternal. VexaHost tidak bertanggung jawab atas kehilangan data yang diakibatkan oleh kelalaian konfigurasi Pengguna atau eksploitasi keamanan pada aplikasi Pelanggan.
            </p>
        </div>

        <div>
            <h2 class="text-lg font-bold text-slate-900 mb-3">6. Perubahan Ketentuan</h2>
            <p>
                RZ Digital Creative berhak merevisi isi Ketentuan Layanan ini sewaktu-waktu demi meningkatkan kualitas dan kepatuhan hukum platform. Perubahan akan diumumkan melalui situs resmi atau komunikasi email.
            </p>
        </div>
    </div>
</section>
@endsection
