<div class="space-y-6">
    {{-- Header Slide Konfirmasi --}}
    <div class="border border-slate-200 rounded-xl p-6 bg-white shadow-xs">
        <div class="flex items-center gap-3 mb-2">
            <span class="w-6 h-6 rounded-full bg-black text-white text-xs font-semibold flex items-center justify-center" x-text="isDirectCheckout ? '3' : '4'"></span>
            <h2 class="font-bold text-slate-900 text-lg">Konfirmasi Pembayaran</h2>
        </div>
        <p class="text-sm text-slate-600">
            Periksa kembali detail pesanan, rincian biaya, dan metode pembayaran Anda sebelum melanjutkan pembayaran.
        </p>
    </div>

    {{-- Card 1: Metode Pembayaran Terpilih --}}
    <div class="border border-slate-200 rounded-xl p-6 bg-white shadow-xs space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wide">Metode Pembayaran Terpilih</h3>
            </div>
            <button type="button" @click="currentSlide = 2"
                    class="text-xs font-semibold text-[#4A6FA5] hover:text-black transition-colors flex items-center gap-1.5 px-3 py-1.5 rounded-md hover:bg-slate-100 border border-slate-200">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                </svg>
                <span>Ubah Metode</span>
            </button>
        </div>

        <template x-if="selectedPaymentMethod">
            <div class="space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 rounded-lg bg-slate-50 border border-slate-200">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-12 bg-white border border-slate-200 rounded-lg flex items-center justify-center p-2 shrink-0">
                            <img :src="'/images/payments/' + selectedPaymentMethod.image"
                                 :alt="selectedPaymentMethod.name"
                                 class="max-w-full max-h-full object-contain">
                        </div>
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <h4 class="text-base font-bold text-slate-900" x-text="selectedPaymentMethod.name"></h4>
                            </div>
                            <p class="text-xs text-slate-500 mt-0.5" x-text="selectedPaymentMethod.description"></p>
                        </div>
                    </div>

                    <div class="text-xs text-slate-500 sm:text-right shrink-0">
                        <span class="block text-slate-400">Batas Waktu Bayar:</span>
                        <span class="font-bold text-slate-800">24 Jam setelah checkout</span>
                    </div>
                </div>

                {{-- QRIS Barcode Display (Centered in Slide Konfirmasi) --}}
                <template x-if="paymentMethod === 'qris'">
                    <div class="p-6 rounded-xl bg-slate-50 border border-slate-200 text-center">
                        <div class="flex flex-col items-center justify-center">
                            {{-- QR Code Image (Tanpa Garis Disekelilingnya) --}}
                            <div class="w-64 h-64 sm:w-72 sm:h-72 mx-auto flex items-center justify-center p-2 bg-white rounded-lg">
                                <img :src="qrisSvgDataUri || '{{ asset('images/qris.jpeg') }}'" alt="QRIS VexaHost" class="max-w-full max-h-full object-contain">
                            </div>

                            {{-- Nominal Tagihan --}}
                            <div class="mt-4">
                                <span class="text-xs text-slate-500 block">Total Nominal yang Harus Dibayar:</span>
                                <span class="text-2xl font-black font-mono-code text-slate-900" x-text="formatRupiah(totalPrice)"></span>
                            </div>

                            {{-- Unduh / Buka QRIS Button --}}
                            <div class="mt-2.5">
                                <a :href="qrisSvgDataUri || '{{ asset('images/qris.jpeg') }}'" target="_blank" download="qris-vexahost.svg"
                                   class="inline-flex items-center gap-1.5 text-xs font-semibold px-3.5 py-1.5 rounded-lg bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 shadow-2xs transition-colors">
                                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                    <span>Buka / Unduh Gambar QRIS</span>
                                </a>
                            </div>

                            <p class="text-xs text-slate-500 max-w-md mx-auto mt-3">
                                Scan QR code di atas menggunakan BCA, Mandiri, BRI, BNI, GoPay, OVO, DANA, ShopeePay, atau aplikasi mobile banking lainnya.
                            </p>
                        </div>
                    </div>
                </template>

                {{-- Lynk.id Payment Notice in Slide Konfirmasi --}}
                <template x-if="paymentMethod === 'lynk'">
                    <div class="p-6 rounded-xl bg-slate-50 border border-slate-200 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <div class="w-24 h-12 mb-3 flex items-center justify-center bg-white border border-slate-200 rounded-xl p-2 shadow-2xs">
                                <img src="{{ asset('images/payments/lynk.svg') }}" alt="Lynk.id" class="max-h-8 max-w-full object-contain">
                            </div>

                            <h4 class="text-base font-bold text-slate-900">Pembayaran Instan via Lynk.id</h4>
                            <p class="text-xs text-slate-500 max-w-md mx-auto mt-1 mb-4 leading-relaxed">
                                Mendukung pembayaran lengkap: <strong>QRIS, Virtual Account (BCA, Mandiri, BNI, BRI), E-Wallet (GoPay, OVO, DANA, ShopeePay)</strong>, dan Kartu Debit/Kredit.
                            </p>

                            <div class="p-4 bg-white rounded-xl border border-slate-200 inline-block min-w-[240px] text-center shadow-2xs">
                                <span class="text-xs text-slate-500 block">Total Nominal yang Harus Dibayar:</span>
                                <span class="text-2xl font-black font-mono-code text-slate-900" x-text="formatRupiah(totalPrice)"></span>
                            </div>

                            <div class="mt-4 flex items-center gap-2 text-xs text-slate-600 bg-blue-50 border border-blue-200 px-4 py-2.5 rounded-lg max-w-md mx-auto text-left">
                                <svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>Saat menekan tombol <strong>Bayar Sekarang</strong> di bawah, Anda akan otomatis dialihkan ke link checkout Lynk.id untuk menyelesaikan pembayaran.</span>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </template>

        <template x-if="!selectedPaymentMethod">
            <div class="p-4 rounded-lg bg-amber-50 border border-amber-200 text-amber-800 text-xs flex items-center justify-between">
                <span>Belum ada metode pembayaran yang dipilih.</span>
                <button type="button" @click="currentSlide = 2" class="underline font-bold">Pilih Sekarang →</button>
            </div>
        </template>
    </div>

    {{-- Card 2: Full Breakdown Harga (Rincian Lengkap Tagihan) --}}
    <div class="border border-slate-200 rounded-xl p-6 bg-white shadow-xs space-y-4">
        <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
            <svg class="w-4 h-4 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
            </svg>
            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wide">Rincian Tagihan (Full Breakdown)</h3>
        </div>

        <div class="space-y-2.5 text-sm">
            <div class="flex items-center justify-between">
                <div>
                    <span class="font-medium text-slate-800" x-text="currentSpec ? currentSpec.name : 'Paket VPS'"></span>
                    <span class="text-xs text-slate-500 block">Langganan 1 Bulan</span>
                </div>
                <span class="font-mono-code font-bold text-slate-900" x-text="formatRupiah(currentBaseMonthlyPrice)"></span>
            </div>

            <div class="flex items-center justify-between text-xs text-slate-600">
                <span>Biaya Setup &amp; Provisioning Server</span>
                <span class="font-semibold text-emerald-600">GRATIS (Rp 0)</span>
            </div>

            <div class="flex items-center justify-between text-xs text-slate-600">
                <span>Proteksi Anti-DDoS &amp; Automated Health Check</span>
                <span class="font-semibold text-emerald-600">Termasuk</span>
            </div>

            <div class="flex items-center justify-between text-xs text-slate-600">
                <span>Pajak (PPN 11%)</span>
                <span class="text-slate-500 font-medium">Rp 0 (Sudah Termasuk)</span>
            </div>

            <div class="border-t border-slate-200 pt-4 mt-3 flex items-center justify-between">
                <div>
                    <span class="text-sm font-bold text-slate-900 block">Total Tagihan Bersih</span>
                    <span class="text-xs text-slate-500">Tarif flat perpanjangan bulanan</span>
                </div>
                <div class="text-right">
                    <span class="font-mono-code text-2xl font-black text-emerald-600" x-text="formatRupiah(totalPrice)"></span>
                </div>
            </div>
        </div>
    </div>

    {{-- Card 3: Ringkasan Spesifikasi Server --}}
    <div class="border border-slate-200 rounded-xl p-6 bg-slate-50/70 shadow-xs space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-200">
            <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wide">Ringkasan Konfigurasi Layanan</h3>
            <span class="px-2 py-0.5 rounded text-[11px] font-bold uppercase bg-white border border-slate-200 text-slate-700"
                  x-text="isDatabasePackage ? 'Managed Database' : (isAiPackage ? 'AI Combo' : 'Cloud VPS')"></span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
            <div class="bg-white p-3 rounded-lg border border-slate-200">
                <span class="text-slate-400 block mb-0.5">Hostname / VPS Name:</span>
                <span class="font-bold font-mono-code text-slate-900" x-text="vpsName || '-'"></span>
            </div>
            <div class="bg-white p-3 rounded-lg border border-slate-200">
                <span class="text-slate-400 block mb-0.5">Paket &amp; Resource:</span>
                <span class="font-bold text-slate-900" x-text="currentSpec ? (currentSpec.name + ' (' + currentSpec.cpu + ' Core / ' + currentSpec.ram + ' GB RAM / ' + currentSpec.disk + ' GB NVMe)') : '-'"></span>
            </div>
            <div class="bg-white p-3 rounded-lg border border-slate-200">
                <span class="text-slate-400 block mb-0.5">Datacenter &amp; Provider:</span>
                <span class="font-bold text-slate-900" x-text="(provider === 'tencent' ? 'Tencent Cloud' : 'Cloudeka by Lintasarta') + ' · ' + (datacenter === 'indonesia' ? 'Jakarta, ID' : 'Singapore')"></span>
            </div>
            <div class="bg-white p-3 rounded-lg border border-slate-200">
                <span class="text-slate-400 block mb-0.5">Sistem Operasi / Stack:</span>
                <span class="font-bold text-slate-900" x-text="isDatabasePackage ? ('Engine: ' + dbEngineLabel + ' (' + (dbManager === 'cloudbeaver' ? 'CloudBeaver Web GUI' : 'CLI Only') + ')') : ((os === 'ubuntu2404' ? 'Ubuntu 24.04' : 'Ubuntu 22.04') + ' · ' + controlPanelLabel)"></span>
            </div>
        </div>
    </div>

    {{-- Card 4: Jaminan Keamanan & Informasi Transaksi --}}
    <div class="border border-slate-200 rounded-xl p-4 bg-white space-y-2 text-xs text-slate-600 shadow-xs">
        <div class="flex items-center gap-2 font-semibold text-slate-800">
            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            <span>Transaksi Aman &amp; Terverifikasi Otomatis</span>
        </div>
        <ul class="space-y-1.5 pl-6 list-disc text-slate-500">
            <li>Anda dapat langsung melakukan scan QR Code QRIS di atas atau di halaman instruksi pembayaran setelah menekan tombol <strong>Bayar Sekarang</strong>.</li>
            <li>Invoice resmi dan konfirmasi pembayaran akan otomatis dikirimkan ke alamat email Anda.</li>
            <li>Server langsung disiapkan secara otomatis oleh sistem kami segera setelah pembayaran berhasil diverifikasi.</li>
        </ul>
    </div>

    {{-- Card 5: Persetujuan Ketentuan Penggunaan (WAJIB).
         Persetujuan ini dicatat ke tabel terms_acceptances beserta versi naskah,
         waktu, dan alamat IP. Validasi ulang dilakukan di sisi server. --}}
    <div class="border border-slate-300 rounded-xl p-5 bg-slate-50 shadow-xs">
        <div class="flex items-center gap-2 mb-3">
            <svg class="w-4 h-4 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M5 19h14a2 2 0 001.84-2.75L13.74 4a2 2 0 00-3.48 0l-7.1 12.25A2 2 0 004.99 19z"/>
            </svg>
            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wide">Ketentuan Penggunaan Layanan</h3>
        </div>

        <p class="text-xs text-slate-600 leading-relaxed mb-3">
            Layanan ini <strong>dilarang</strong> digunakan untuk: serangan siber dan DDoS, spam dan phishing,
            penambangan kripto, konten ilegal dan judi online, pemindaian port agresif,
            <strong>layanan VPN dan proxy</strong>, <strong>scraping dan crawling</strong>,
            <strong>agregator torrent</strong>, serta <strong>bot dan skrip otomatis</strong> yang menyalahi
            ketentuan pihak ketiga.
        </p>

        <div class="rounded-lg border border-amber-200 bg-amber-50 p-3 mb-4">
            <p class="text-[11px] text-amber-900 leading-relaxed">
                Pelanggaran dapat mengakibatkan <strong>terminasi instan tanpa pengembalian dana</strong>, dan
                berdampak pada seluruh pelanggan lain karena pembatasan dapat dikenakan di tingkat akun
                penyedia infrastruktur hulu. Pencadangan data sepenuhnya menjadi tanggung jawab Anda.
            </p>
        </div>

        <label class="flex items-start gap-3 cursor-pointer">
            <input type="checkbox" name="terms_accepted" value="1" required
                   x-model="termsAccepted"
                   class="w-4 h-4 mt-0.5 rounded border-slate-400 text-black focus:ring-black shrink-0">
            <span class="text-xs text-slate-700 leading-relaxed">
                Saya telah membaca dan menyetujui
                <a href="{{ route('terms') }}" target="_blank" rel="noopener"
                   class="font-bold text-slate-900 underline underline-offset-2">Ketentuan Layanan</a>,
                <a href="{{ route('sla') }}" target="_blank" rel="noopener"
                   class="font-bold text-slate-900 underline underline-offset-2">SLA</a>, dan
                <a href="{{ route('refund') }}" target="_blank" rel="noopener"
                   class="font-bold text-slate-900 underline underline-offset-2">Kebijakan Pengembalian Dana</a>
                VexaHost, termasuk seluruh larangan penggunaan di atas.
                <span class="block mt-1 text-[11px] text-slate-500 font-mono-code">
                    Versi naskah: {{ config('legal.terms_version') }}
                </span>
            </span>
        </label>

        @error('terms_accepted')
            <p class="mt-2 text-xs font-semibold text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>
