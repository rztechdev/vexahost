<div class="space-y-6">
    {{-- Header Slide Konfirmasi --}}
    <div class="border border-slate-200 rounded-2xl p-5 sm:p-6 bg-white shadow-xs">
        <div class="flex items-center gap-3">
            <span class="w-6 h-6 rounded-full bg-black text-white text-xs font-semibold flex items-center justify-center" x-text="isDirectCheckout ? '3' : '4'"></span>
            <h2 class="font-bold text-slate-900 text-lg">Konfirmasi Pembayaran</h2>
        </div>
    </div>

    {{-- Card 1: Metode Pembayaran Terpilih --}}
    <div class="border border-slate-200 rounded-2xl p-5 sm:p-6 bg-white shadow-xs space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wide">Metode Pembayaran Terpilih</h3>
            </div>
            <button type="button" @click="currentSlide = (isDirectCheckout ? 2 : 2)"
                    class="text-xs font-semibold text-[#4A6FA5] hover:text-black transition-colors flex items-center gap-1.5 px-3 py-1.5 rounded-md hover:bg-slate-100 border border-slate-200">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                </svg>
                <span>Ubah Metode</span>
            </button>
        </div>

        <template x-if="selectedPaymentMethod">
            <div class="space-y-4">
                <div class="flex items-center justify-between gap-4 p-4 rounded-lg bg-slate-50 border border-slate-200">
                    <div class="flex items-center gap-3.5">
                        <div class="w-14 h-10 bg-white border border-slate-200 rounded-lg flex items-center justify-center p-1.5 shrink-0">
                            <img :src="'/images/payments/' + selectedPaymentMethod.image"
                                 :alt="selectedPaymentMethod.name"
                                 class="max-w-full max-h-full object-contain">
                        </div>
                        <h4 class="text-sm font-bold text-slate-900" x-text="selectedPaymentMethod.name"></h4>
                    </div>
                </div>

                {{-- QRIS Barcode Display (Centered in Slide Konfirmasi) --}}
                <template x-if="paymentMethod === 'qris'">
                    <div class="p-6 rounded-xl bg-slate-50 border border-slate-200 text-center">
                        <div class="flex flex-col items-center justify-center">
                            {{-- QR Code Image --}}
                            <div class="w-60 h-60 mx-auto flex items-center justify-center p-2 bg-white rounded-lg border border-slate-200">
                                <img :src="qrisSvgDataUri || '{{ asset('images/qris.jpeg') }}'" alt="QRIS VexaHost" class="max-w-full max-h-full object-contain">
                            </div>

                            {{-- Nominal Tagihan --}}
                            <div class="mt-4">
                                <span class="text-xs text-slate-500 block">Total Tagihan:</span>
                                <span class="text-2xl font-black font-mono-code text-slate-900" x-text="formatRupiah(totalPrice)"></span>
                            </div>

                            {{-- Unduh / Buka QRIS Button --}}
                            <div class="mt-3">
                                <a :href="qrisSvgDataUri || '{{ asset('images/qris.jpeg') }}'" target="_blank" download="qris-vexahost.svg"
                                   class="inline-flex items-center gap-1.5 text-xs font-semibold px-3.5 py-1.5 rounded-lg bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 shadow-2xs transition-colors">
                                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                    <span>Unduh Gambar QRIS</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </template>

                {{-- Automatic Instant Payment Notice in Slide Konfirmasi --}}
                <template x-if="['midtrans_snap', 'mandiri_va', 'bni_va', 'bri_va', 'permata_va', 'cimb_va', 'other_va', 'gopay', 'bca_va'].includes(paymentMethod)">
                    <div class="p-6 rounded-xl bg-slate-50 border border-slate-200 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <div class="w-28 h-12 mb-3 flex items-center justify-center bg-white border border-slate-200 rounded-xl p-2 shadow-2xs">
                                <img :src="'/images/payments/' + (selectedPaymentMethod?.image || 'qris.svg')" :alt="selectedPaymentMethod?.name || 'Pembayaran'" class="max-h-8 max-w-full object-contain">
                            </div>

                            <h4 class="text-sm font-bold text-slate-900" x-text="selectedPaymentMethod?.name || 'Pembayaran Otomatis'"></h4>

                            <div class="mt-3 p-3.5 bg-white rounded-xl border border-slate-200 inline-block min-w-[220px] text-center shadow-2xs">
                                <span class="text-xs text-slate-500 block">Total Tagihan:</span>
                                <span class="text-2xl font-black font-mono-code text-slate-900" x-text="formatRupiah(totalPrice)"></span>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </template>

        <template x-if="!selectedPaymentMethod">
            <div class="p-4 rounded-lg bg-amber-50 border border-amber-200 text-amber-800 text-xs flex items-center justify-between">
                <span>Belum ada metode pembayaran yang dipilih.</span>
                <button type="button" @click="currentSlide = (isDirectCheckout ? 2 : 2)" class="underline font-bold">Pilih Sekarang →</button>
            </div>
        </template>
    </div>

    {{-- Card 2: Full Breakdown Harga --}}
    <div class="border border-slate-200 rounded-2xl p-5 sm:p-6 bg-white shadow-xs space-y-4">
        <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
            <svg class="w-4 h-4 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
            </svg>
            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wide">Rincian Tagihan</h3>
        </div>

        <div class="space-y-3 text-sm">
            <div class="flex items-center justify-between">
                <div>
                    <span class="font-medium text-slate-800" x-text="currentSpec ? currentSpec.name : 'Paket VPS'"></span>
                    <span class="text-xs text-slate-400 block">Langganan 1 Bulan</span>
                </div>
                <span class="font-mono-code font-bold text-slate-900" x-text="formatRupiah(currentBaseMonthlyPrice)"></span>
            </div>

            <div class="border-t border-slate-200 pt-3 flex items-center justify-between">
                <div>
                    <span class="text-sm font-bold text-slate-900 block">Total Tagihan Bersih</span>
                </div>
                <div class="text-right">
                    <span class="font-mono-code text-2xl font-black text-emerald-600" x-text="formatRupiah(totalPrice)"></span>
                </div>
            </div>
        </div>
    </div>

    {{-- Card 3: Ringkasan Spesifikasi Server --}}
    <div class="border border-slate-200 rounded-2xl p-5 sm:p-6 bg-slate-50/70 shadow-xs space-y-4">
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

    {{-- Info Keamanan Ringkas --}}
    <div class="flex items-center gap-2.5 px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-600">
        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
        </svg>
        <span>Transaksi aman &amp; terverifikasi otomatis. Kredensial server dikirim ke email Anda setelah pembayaran.</span>
    </div>

    {{-- Card 5: Persetujuan Ketentuan Penggunaan (WAJIB) --}}
    <div class="border border-slate-200 rounded-2xl p-4 sm:p-5 bg-slate-50 shadow-xs space-y-3">
        <div class="flex items-center gap-2">
            <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wide">Ketentuan Penggunaan Layanan</h3>
        </div>

        <p class="text-xs text-slate-600 leading-relaxed">
            Dilarang untuk: serangan DDoS, spam/phishing, konten judi/ilegal, penambangan kripto, pemindaian port, layanan VPN/proxy, scraping, <strong>agregator torrent</strong>, atau aktivitas yang menyalahi hukum. Pelanggaran dapat mengakibatkan terminasi instan tanpa refund.
        </p>

        <label class="flex items-start gap-3 cursor-pointer pt-1">
            <input type="checkbox" name="terms_accepted" value="1" required
                   x-model="termsAccepted"
                   class="w-4 h-4 mt-0.5 rounded border-slate-400 text-black focus:ring-black shrink-0">
            <span class="text-xs text-slate-700 leading-relaxed">
                Saya menyetujui
                <a href="{{ route('terms') }}" target="_blank" rel="noopener"
                   class="font-bold text-slate-900 underline underline-offset-2">Ketentuan Layanan</a> dan
                <a href="{{ route('refund') }}" target="_blank" rel="noopener"
                   class="font-bold text-slate-900 underline underline-offset-2">Kebijakan Pengembalian Dana</a>
                VexaHost.
                <span class="text-[11px] text-slate-400 font-mono-code ml-1">({{ config('legal.terms_version') }})</span>
            </span>
        </label>

        @error('terms_accepted')
            <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>
