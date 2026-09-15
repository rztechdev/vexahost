@extends('layouts.app', ['title' => 'Pembayaran — VexaHost'])

@section('content')
<div class="py-12 bg-slate-50 min-h-screen" x-data="paymentGateway()">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Back to Checkout --}}
        <a href="{{ route('checkout', $order->vps_spec_id) }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-black transition-colors mb-8">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            Kembali ke Checkout
        </a>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Left: Payment Details --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden">
                    {{-- Header --}}
                    <div class="bg-black text-white p-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h1 class="text-xl font-bold">Pembayaran {{ $order->payment_method_name }}</h1>
                                    <p class="text-sm text-white/80">Selesaikan pembayaran untuk mengaktifkan VPS Anda</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-xs text-white/60">Nomor Invoice</div>
                                <div class="font-bold font-mono-code">{{ $order->invoice->invoice_number ?? 'INV-' . $order->id }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Payment Content --}}
                    <div class="p-6">
                        {{-- QRIS Payment --}}
                        @if(in_array($order->payment_method, ['qris']))
                        <div class="text-center mb-8">
                            <div class="w-64 h-64 mx-auto mb-6 bg-white border-2 border-slate-300 rounded-lg flex items-center justify-center p-4">
                                <img src="{{ asset('image/qris.jpeg') }}" alt="QRIS Payment" class="max-w-full max-h-full object-contain">
                            </div>
                            <div class="space-y-3">
                                <p class="text-sm text-slate-600">Scan QR code di atas menggunakan aplikasi e-wallet atau mobile banking Anda</p>
                                <div class="flex items-center justify-center gap-3">
                                    @foreach(['gopay' => 'gopay', 'ovo' => 'ewallet_ovo', 'dana' => 'dana', 'shopeepay' => 'ewallet_shopeepay', 'bca' => 'va_bca', 'mandiri' => 'va_mandiri', 'bri' => 'va_bri', 'bni' => 'va_bni'] as $app => $file)
                                        <div class="w-10 h-10 bg-slate-100 rounded-lg flex items-center justify-center p-2">
                                            <img src="{{ asset('images/payments/' . $file . '.svg') }}" alt="{{ strtoupper($app) }}" class="h-6">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- Virtual Account Payment --}}
                        @if(in_array($order->payment_method, ['bca_va', 'mandiri_va', 'bni_va', 'bri_va']))
                        <div class="text-center mb-8">
                            <div class="bg-slate-50 border border-slate-200 rounded-lg p-8 max-w-md mx-auto">
                                <div class="w-16 h-16 mx-auto mb-4 bg-emerald-100 rounded-full flex items-center justify-center">
                                    <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <h3 class="font-bold text-lg text-slate-900 mb-2">Virtual Account</h3>
                                <div class="bg-white border border-slate-300 rounded-lg p-4 mb-4">
                                    <div class="text-xs text-slate-500 mb-1">Nomor Virtual Account</div>
                                    <div class="font-bold text-xl font-mono-code text-slate-900" x-text="virtualAccountNumber"></div>
                                </div>
                                <p class="text-sm text-slate-600">
                                    Transfer ke nomor Virtual Account di atas melalui ATM, mobile banking, atau internet banking
                                </p>
                            </div>
                        </div>
                        @endif

                        {{-- E-Wallet Payment --}}
                        @if(in_array($order->payment_method, ['gopay', 'ovo', 'dana', 'shopeepay']))
                        <div class="text-center mb-8">
                            <div class="flex flex-col items-center">
                                <div class="w-32 h-32 mb-6 bg-white border-2 border-slate-300 rounded-lg flex items-center justify-center p-4">
                                    @php
                                        $walletImages = [
                                            'gopay' => 'gopay.svg',
                                            'ovo' => 'ewallet_ovo.svg', 
                                            'dana' => 'dana.svg',
                                            'shopeepay' => 'ewallet_shopeepay.svg'
                                        ];
                                    @endphp
                                    <img src="{{ asset('images/payments/' . ($walletImages[$order->payment_method] ?? 'qris.jpeg')) }}" 
                                         alt="{{ strtoupper($order->payment_method) }}" 
                                         class="max-w-full max-h-full object-contain">
                                </div>
                                <p class="text-sm text-slate-600 mb-4">
                                    Buka aplikasi {{ strtoupper($order->payment_method) }} Anda dan scan QR code atau gunakan metode pembayaran yang tersedia
                                </p>
                                <div class="flex items-center justify-center gap-2">
                                    <span class="text-xs text-slate-500">Tersedia di:</span>
                                    <div class="flex items-center gap-1">
                                        <svg class="w-4 h-4 text-slate-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13a1 1 0 102 0V9.414l1.293 1.293a1 1 0 001.414-1.414z" clip-rule="evenodd"></path></svg>
                                        <span class="text-xs font-medium">App Store</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <svg class="w-4 h-4 text-slate-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 000 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path></svg>
                                        <span class="text-xs font-medium">Google Play</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- Payment Instructions --}}
                        <div class="border border-slate-200 rounded-lg p-6 mb-8">
                            <h3 class="font-semibold text-slate-900 mb-4">Instruksi Pembayaran</h3>
                            <div class="space-y-4">
                                <div class="flex items-start gap-3">
                                    <span class="w-6 h-6 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center text-xs font-bold flex-shrink-0">1</span>
                                    <div>
                                        <p class="font-medium text-sm text-slate-900 mb-1">Lakukan pembayaran</p>
                                        <p class="text-xs text-slate-600">
                                            @if($order->payment_method === 'qris')
                                                Scan QR code menggunakan aplikasi e-wallet atau mobile banking
                                            @elseif(str_contains($order->payment_method, '_va'))
                                                Transfer ke nomor Virtual Account yang ditampilkan
                                            @else
                                                Gunakan aplikasi {{ strtoupper($order->payment_method) }} untuk melakukan pembayaran
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <span class="w-6 h-6 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center text-xs font-bold flex-shrink-0">2</span>
                                    <div>
                                        <p class="font-medium text-sm text-slate-900 mb-1">Tunggu konfirmasi</p>
                                        <p class="text-xs text-slate-600">
                                            Sistem akan otomatis mendeteksi pembayaran dalam 1-5 menit
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <span class="w-6 h-6 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center text-xs font-bold flex-shrink-0">3</span>
                                    <div>
                                        <p class="font-medium text-sm text-slate-900 mb-1">Server di-setup</p>
                                        <p class="text-xs text-slate-600">
                                            Setelah pembayaran terkonfirmasi, tim teknis akan setup server Anda (1-2 jam kerja)
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="text-center space-y-3">
                            <a href="{{ route('order.payment.status', $order->id) }}"
                               class="inline-block px-8 py-3 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold transition-colors">
                                Cek Status Pembayaran
                            </a>
                            <p class="text-xs text-slate-500">
                                Setelah membayar, sistem akan mendeteksi otomatis via payment gateway.
                                Anda juga bisa cek status manual di halaman ini.
                            </p>

                            @if(!empty($devSimulateEnabled))
                                {{-- DEV ONLY: Simulasi pembayaran, hanya muncul kalau APP_ENV=local + APP_DEV_SIMULATE_PAYMENT=true --}}
                                <form method="POST" action="{{ route('order.payment.dev-simulate', $order->id) }}"
                                      class="pt-4 border-t border-amber-200 mt-4">
                                    @csrf
                                    <div class="bg-amber-50 border border-amber-300 rounded-lg p-3 mb-3 text-left">
                                        <p class="text-xs font-bold text-amber-800">⚠️ DEV MODE</p>
                                        <p class="text-xs text-amber-700 mt-1">
                                            Tombol simulasi ini hanya aktif di environment lokal untuk keperluan testing.
                                            Di production, konfirmasi pembayaran WAJIB via webhook gateway.
                                        </p>
                                    </div>
                                    <button type="submit"
                                            class="w-full px-4 py-2 rounded-lg bg-amber-600 hover:bg-amber-700 text-white font-semibold text-sm">
                                        [DEV] Simulate Payment Settlement
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right: Order Summary --}}
            <div class="lg:col-span-1">
                <div class="sticky top-24">
                    <div class="bg-white rounded-lg border border-slate-200 shadow-sm p-6 space-y-5 mb-6">
                        <h3 class="font-semibold text-slate-900 text-base">Ringkasan Pesanan</h3>
                        
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-slate-500">VPS Name</span>
                                <span class="font-medium text-slate-900 font-mono-code">{{ $order->hostname ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Paket</span>
                                <span class="font-medium text-slate-900">{{ $order->vpsSpec->name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Provider</span>
                                <span class="font-medium text-slate-900">{{ $order->provider_label }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Datacenter</span>
                                <span class="font-medium text-slate-900 capitalize">{{ $order->datacenter_location }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">OS</span>
                                <span class="font-medium text-slate-900">{{ $order->os_label }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Stack</span>
                                <span class="font-medium text-slate-900">{{ $order->control_panel_label }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Periode</span>
                                <span class="font-medium text-slate-900 font-semibold">
                                    @if($order->billing_cycle === 'annual')
                                        12 Bulan (1 Tahun)
                                    @elseif($order->billing_cycle === 'semi_annual')
                                        6 Bulan
                                    @elseif($order->billing_cycle === 'quarterly')
                                        3 Bulan
                                    @else
                                        1 Bulan
                                    @endif
                                </span>
                            </div>
                            <div class="flex justify-between border-t border-slate-200 pt-3">
                                <span class="text-slate-500">Total Pembayaran</span>
                                <span class="font-bold text-slate-900 text-lg font-mono-code">Rp {{ number_format($order->amount, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <div class="border-t border-slate-200 pt-4">
                            <div class="flex items-center gap-2 text-sm text-slate-600 mb-3">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>Batas waktu pembayaran:</span>
                            </div>
                            <div class="bg-red-50 border border-red-200 rounded-lg p-3 text-center">
                                <p class="font-bold text-red-700 text-sm">
                                    {{ $order->invoice->due_at ? $order->invoice->due_at->format('d M Y, H:i') : now()->addDay()->format('d M Y, H:i') }} WIB
                                </p>
                                <p class="text-xs text-red-600 mt-1">Pesanan akan dibatalkan otomatis setelah batas waktu</p>
                            </div>
                        </div>
                    </div>

                    {{-- Support Info --}}
                    <div class="bg-white rounded-lg border border-slate-200 shadow-sm p-6">
                        <h4 class="font-semibold text-slate-900 text-sm mb-3">Butuh Bantuan?</h4>
                        <div class="space-y-3 text-xs text-slate-600">
                            <div class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-slate-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                                <div>
                                    <p class="font-medium text-slate-700">WhatsApp Support</p>
                                    <p class="text-slate-500">+62 812-3456-7890</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-slate-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                <div>
                                    <p class="font-medium text-slate-700">Email Support</p>
                                    <p class="text-slate-500">support@vexahost.id</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Success Modal --}}
    <div x-show="showSuccessModal" x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-lg max-w-md w-full p-6" @click.away="showSuccessModal = false">
            <div class="text-center mb-6">
                <div class="w-16 h-16 rounded-full bg-emerald-100 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-2">Pembayaran Berhasil!</h3>
                <p class="text-sm text-slate-600">
                    Pembayaran Anda telah dikonfirmasi. Tim teknis sedang mempersiapkan server VPS Anda.
                </p>
            </div>
            <div class="space-y-3 text-sm mb-6">
                <div class="flex justify-between">
                    <span class="text-slate-500">Invoice:</span>
                    <span class="font-mono-code font-semibold">{{ $order->invoice->invoice_number ?? 'INV-' . $order->id }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Jumlah:</span>
                    <span class="font-bold font-mono-code">Rp {{ number_format($order->amount, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Status:</span>
                    <span class="font-semibold text-emerald-600">LUNAS</span>
                </div>
            </div>
            <div class="text-center">
                <a href="{{ route('order.success', $order->id) }}" class="inline-block px-6 py-2.5 rounded-lg bg-black hover:bg-neutral-800 text-white font-bold transition-colors w-full">
                    Lihat Detail Pesanan
                </a>
                <p class="text-xs text-slate-500 mt-3">
                    Invoice telah dikirim ke email Anda
                </p>
            </div>
        </div>
    </div>
</div>

<script>
function paymentGateway() {
    return {
        isProcessing: false,
        showSuccessModal: false,
        virtualAccountNumber: this.generateVirtualAccount(),
        
        generateVirtualAccount() {
            const prefix = {
                'bca_va': '88012',
                'mandiri_va': '88888', 
                'bni_va': '88123',
                'bri_va': '88234'
            }['{{ $order->payment_method }}'] || '88000';
            
            const random = Math.floor(Math.random() * 100000000).toString().padStart(8, '0');
            return prefix + random;
        },
        
        // NOTE: fungsi completePayment() sudah dihapus (P0 security fix).
        // Konfirmasi pembayaran WAJIB via webhook payment gateway
        // (POST /api/webhooks/payment) dengan signature yang diverifikasi.
        // Untuk testing lokal: gunakan tombol "[DEV] Simulate Payment Settlement"
        // yang muncul kalau APP_ENV=local + APP_DEV_SIMULATE_PAYMENT=true.
    };
}
</script>
@endsection
