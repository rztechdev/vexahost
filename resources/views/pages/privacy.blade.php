@extends('layouts.app', ['title' => 'Kebijakan Privasi (Privacy Policy) — VexaHost'])

@section('content')
<section class="bg-[#0B0F19] text-white pt-16 pb-12 border-b border-slate-800">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex items-center space-x-2 text-xs text-slate-400 mb-6 font-mono-code">
            <a href="{{ route('home') }}" class="hover:text-white transition-colors">Beranda</a>
            <span>/</span>
            <span class="text-[#6588BC]">Kebijakan Privasi</span>
        </nav>
        <h1 class="text-3xl sm:text-4xl font-bold tracking-tight text-white mb-3">
            Kebijakan Privasi (Privacy Policy)
        </h1>
        <p class="text-xs text-slate-400 font-mono-code">
            Terakhir diperbarui: 13 September 2026 &bull; Diterbitkan oleh RZ Digital Creative
        </p>
    </div>
</section>

<section class="py-14 bg-white border-b border-slate-200">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 prose prose-slate max-w-none text-sm leading-relaxed text-slate-600 space-y-8">
        <div>
            <h2 class="text-lg font-bold text-slate-900 mb-3">1. Komitmen Privasi Kami</h2>
            <p>
                <strong>RZ Digital Creative</strong> selaku pengembang dan pengelola VexaHost berkomitmen melindungi kerahasiaan dan privasi data pribadi seluruh pengguna layanan kami. Kebijakan Privasi ini menjelaskan bagaimana kami mengumpulkan, menggunakan, menyimpan, dan menjaga informasi Anda sesuai standar kepatuhan privasi yang berlaku di Indonesia.
            </p>
        </div>

        <div>
            <h2 class="text-lg font-bold text-slate-900 mb-3">2. Data yang Kami Kumpulkan</h2>
            <p class="mb-3">Kami hanya mengumpulkan data yang dibutuhkan untuk operasional layanan VPS:</p>
            <ul class="list-disc list-inside space-y-1.5 pl-2 text-slate-700">
                <li><strong>Informasi Identitas:</strong> Nama lengkap dan alamat email yang Anda daftarkan.</li>
                <li><strong>Informasi Kontak:</strong> Nomor telepon / WhatsApp untuk keperluan konfirmasi pesanan dan darurat server.</li>
                <li><strong>Informasi Transaksi:</strong> Metode pembayaran yang dipilih, status pembayaran, dan riwayat faktur (kami tidak pernah menyimpan nomor kartu kredit secara langsung).</li>
                <li><strong>Informasi Teknis:</strong> Alamat IP, log akses otentikasi dashboard, dan tipe peramban demi keperluan audit keamanan.</li>
            </ul>
        </div>

        <div>
            <h2 class="text-lg font-bold text-slate-900 mb-3">3. Kerahasiaan Isi Server Virtual (VPS)</h2>
            <p>
                VexaHost menganut prinsip <em>Zero-Knowledge Server Access</em>. Kami <strong>TIDAK PERNAH</strong> membuka, menyalin, menganalisis, atau menjual file, database, kode sumber, atau data internal apapun yang Anda simpan di dalam instans VPS Anda. Akses tim teknis ke server Anda hanya akan dilakukan atas izin tertulis resmi dari Anda melalui tiket bantuan teknis.
            </p>
        </div>

        <div>
            <h2 class="text-lg font-bold text-slate-900 mb-3">4. Penggunaan Informasi</h2>
            <p>Data pribadi Anda digunakan untuk:</p>
            <ul class="list-disc list-inside space-y-1 pl-2 text-slate-700">
                <li>Membuat dan mengonfigurasi instans server VPS serta mengirimkan kredensial login.</li>
                <li>Memproses pembayaran tagihan secara otomatis melalui saluran Midtrans atau toko resmi Shopee.</li>
                <li>Mengirimkan pemberitahuan penting seputar pemeliharaan jaringan (maintenance) dan peringatan masa aktif.</li>
                <li>Mencegah tindakan penipuan transaksi dan aktivitas siber terlarang.</li>
            </ul>
        </div>

        <div>
            <h2 class="text-lg font-bold text-slate-900 mb-3">5. Keamanan Data</h2>
            <p>
                Seluruh lalu lintas web dashboard VexaHost dilindungi enkripsi SSL/TLS 256-bit standar industri. Password akun disimpan dalam format hash kriptografis yang aman dan tidak dapat dibaca oleh staf kami.
            </p>
        </div>

        <div>
            <h2 class="text-lg font-bold text-slate-900 mb-3">6. Hubungi Kami Terkait Privasi</h2>
            <p>
                Jika Anda memiliki pertanyaan seputar perlindungan data Anda atau ingin mengajukan penghapusan akun, silakan hubungi kami di <code class="font-mono-code text-slate-800 bg-slate-100 px-1 py-0.5 rounded">support@vexahost.com</code>.
            </p>
        </div>
    </div>
</section>
@endsection
