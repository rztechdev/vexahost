<div class="border border-slate-200 rounded-xl p-6 bg-white shadow-xs">
    <div class="flex items-center gap-3 mb-2">
        <span class="w-6 h-6 rounded-full bg-black text-white text-xs font-semibold flex items-center justify-center" x-text="isDirectCheckout ? '2' : '3'"></span>
        <h2 class="font-bold text-slate-900 text-lg">Pilih Metode Pembayaran</h2>
    </div>

    <p class="text-sm text-slate-600 mb-6">
        Pilih salah satu metode pembayaran yang tersedia. Anda dapat memeriksa rincian tagihan lengkap di langkah berikutnya.
    </p>

    {{-- Payment Selection Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 mb-6">
        @foreach([
            'midtrans_snap' => ['QRIS Otomatis', 'qris.svg', 'Semua e-wallet & mobile banking instan', 'Instan QR'],
            'gopay' => ['GoPay', 'gopay.svg', 'Aplikasi GoPay & Gojek', 'E-Wallet'],
            'mandiri_va' => ['Mandiri VA', 'va_mandiri.svg', 'Virtual Account otomatis 24 jam', 'Virtual Account'],
            'bni_va' => ['BNI VA', 'va_bni.svg', 'Virtual Account otomatis 24 jam', 'Virtual Account'],
            'bri_va' => ['BRI VA', 'va_bri.svg', 'Virtual Account otomatis 24 jam', 'Virtual Account'],
            'permata_va' => ['Permata VA', 'va_permata.svg', 'Virtual Account otomatis 24 jam', 'Virtual Account'],
            'cimb_va' => ['CIMB Niaga VA', 'va_cimb.svg', 'Virtual Account otomatis 24 jam', 'Virtual Account'],
            'other_va' => ['Bank Lainnya (VA)', 'va_permata.svg', 'Transfer ATM & Bank Lainnya', 'Virtual Account'],
            'bca_va' => ['BCA VA', 'va_bca.svg', 'Sedang dinonaktifkan', 'Virtual Account'],
            'bsi_va' => ['BSI VA', 'va_bsi.svg', 'Sedang dinonaktifkan', 'Virtual Account'],
            'danamon_va' => ['Danamon VA', 'va_danamon.svg', 'Sedang dinonaktifkan', 'Virtual Account'],
            'seabank_va' => ['SeaBank VA', 'va_seabank.svg', 'Sedang dinonaktifkan', 'Virtual Account'],
            'credit_card' => ['Kartu Kredit / Debit', 'credit_card.svg', 'Sedang dinonaktifkan', 'Kartu Pembayaran'],
            'ovo' => ['OVO', 'ewallet_ovo.svg', 'Sedang dinonaktifkan', 'E-Wallet'],
            'dana' => ['DANA', 'dana.svg', 'Sedang dinonaktifkan', 'E-Wallet'],
            'shopeepay' => ['ShopeePay', 'ewallet_shopeepay.svg', 'Sedang dinonaktifkan', 'E-Wallet'],
            'qris' => ['QRIS Manual', 'qris.svg', 'Sedang dinonaktifkan', 'QRIS Manual'],
            'lynk' => ['Lynk.id Checkout', 'lynk.svg', 'Sedang dinonaktifkan', 'Instan Gateway'],
        ] as $method => $details)
            @php
                $unsupportedInMidtrans = ['bca_va', 'bsi_va', 'danamon_va', 'seabank_va', 'credit_card', 'ovo', 'dana', 'shopeepay', 'qris', 'lynk'];
                $isMethodActive = \App\Models\PaymentGateway::isMethodActive($method);
                if (in_array($method, $unsupportedInMidtrans, true)) {
                    $isMethodActive = false;
                }
            @endphp

            @if($isMethodActive)
                {{-- Active Method --}}
                <label class="p-4 rounded-xl border cursor-pointer transition-all flex flex-col justify-between relative text-left select-none"
                       :class="paymentMethod === '{{ $method }}' ? 'border-black bg-slate-50 ring-2 ring-black shadow-xs' : 'border-slate-200 hover:border-slate-300 bg-white'"
                       @click="selectPayment('{{ $method }}')">
                    <input type="radio" name="payment_method" value="{{ $method }}" x-model="paymentMethod" class="sr-only">

                    {{-- Active Indicator / Badge --}}
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wider"
                              :class="paymentMethod === '{{ $method }}' ? 'bg-black text-white' : 'bg-slate-100 text-slate-600'">
                            {{ $details[3] }}
                        </span>
                        <div class="w-4 h-4 rounded-full border flex items-center justify-center transition-all"
                             :class="paymentMethod === '{{ $method }}' ? 'border-black bg-black text-white' : 'border-slate-300 bg-white'">
                            <svg x-show="paymentMethod === '{{ $method }}'" class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>

                    {{-- Logo Container --}}
                    <div class="w-full h-12 mb-3 flex items-center justify-center bg-white border border-slate-100 rounded-lg p-2">
                        <img src="{{ asset('images/payments/' . $details[1]) }}" alt="{{ $details[0] }}" class="max-h-8 max-w-full object-contain">
                    </div>

                    {{-- Name & Description --}}
                    <div>
                        <h3 class="font-bold text-sm text-slate-900">{{ $details[0] }}</h3>
                        <p class="text-xs text-slate-500 mt-0.5 line-clamp-1">{{ $details[2] }}</p>
                    </div>
                </label>
            @else
                {{-- Inactive Method: Tampilan abu-abu, kursor nonaktif --}}
                <div class="p-4 rounded-xl border border-slate-200 bg-white opacity-50 cursor-not-allowed transition-all flex flex-col justify-between relative text-left select-none"
                     title="{{ $details[0] }} sedang tidak aktif"
                     @click="selectPayment('{{ $method }}', false)">

                    {{-- Badge & Inactive Circle Indicator --}}
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wider bg-slate-100 text-slate-600">
                            {{ $details[3] }}
                        </span>
                        <div class="w-4 h-4 rounded-full border border-slate-300 bg-white flex items-center justify-center"></div>
                    </div>

                    {{-- Logo Container --}}
                    <div class="w-full h-12 mb-3 flex items-center justify-center bg-white border border-slate-100 rounded-lg p-2">
                        <img src="{{ asset('images/payments/' . $details[1]) }}" alt="{{ $details[0] }}" class="max-h-8 max-w-full object-contain">
                    </div>

                    {{-- Name & Description --}}
                    <div>
                        <h3 class="font-bold text-sm text-slate-900">{{ $details[0] }}</h3>
                        <p class="text-xs text-slate-500 mt-0.5 line-clamp-1">{{ $details[2] }}</p>
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    {{-- Selection Feedback Banner --}}
    <div x-show="paymentMethod" x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
         class="flex items-center gap-3 p-3.5 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs">
        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        <span>
            Metode pembayaran terpilih: <strong class="font-bold" x-text="paymentMethods[paymentMethod]?.name || paymentMethod"></strong>. Klik tombol <strong>Lanjut ke Konfirmasi Pembayaran</strong> di bawah untuk memeriksa rincian tagihan.
        </span>
    </div>
</div>