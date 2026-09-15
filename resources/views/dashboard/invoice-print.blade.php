<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }} — VexaHost</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-mono-code { font-family: 'JetBrains Mono', monospace; }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; }
            .print-card { border: none !important; }
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 p-4 sm:p-8">
    <div class="max-w-3xl mx-auto">
        <!-- Floating Print Bar -->
        <div class="no-print mb-6 flex items-center justify-between bg-slate-900 text-white p-4 rounded-lg">
            <div class="text-xs">
                <span class="font-bold">Faktur Resmi:</span> {{ $invoice->invoice_number }}
            </div>
            <div class="flex items-center gap-2">
                <button onclick="window.print()" class="px-4 py-2 rounded-lg bg-[#4A6FA5] hover:bg-[#3D5E8C] text-white text-xs font-semibold transition-colors flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak / Simpan PDF</span>
                </button>
                <button onclick="window.close()" class="px-3 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs">
                    Tutup
                </button>
            </div>
        </div>

        <!-- Invoice Sheet -->
        <div class="print-card bg-white rounded-lg border border-slate-200 p-8 sm:p-12 relative overflow-hidden">
            
            <!-- Paid Stamp -->
            @if($invoice->status === 'paid')
                <div class="absolute right-8 top-28 border-2 border-[#6ABD73] text-[#6ABD73] rounded-lg px-5 py-1.5 rotate-[-8deg] pointer-events-none select-none text-center">
                    <span class="text-xl font-bold uppercase tracking-wider block font-mono-code">LUNAS</span>
                    <span class="text-[10px] font-semibold block">PAID &bull; VEXAHOST</span>
                </div>
            @endif

            <!-- Invoice Header -->
            <div class="flex justify-between items-start pb-6 border-b border-slate-200">
                <div class="flex items-center space-x-3.5">
                    <img src="{{ asset('images/logo.png') }}" alt="VexaHost" class="h-12 w-auto object-contain">
                    <div>
                        <span class="text-2xl font-bold tracking-tight text-slate-900">Vexa<span class="text-[#4A6FA5]">Host</span></span>
                        <p class="text-xs text-slate-500">Cloud Infrastructure Reseller</p>
                    </div>
                </div>
                <div class="text-right">
                    <h1 class="text-lg font-bold text-slate-900 uppercase font-mono-code">FAKTUR / INVOICE</h1>
                    <p class="text-xs font-semibold text-[#4A6FA5] font-mono-code mt-0.5">{{ $invoice->invoice_number }}</p>
                </div>
            </div>

            <!-- Parties Details Grid -->
            <div class="grid grid-cols-2 gap-8 py-6 border-b border-slate-200 text-xs">
                <div>
                    <span class="text-slate-400 font-semibold block mb-1">Diterbitkan Oleh:</span>
                    <p class="font-bold text-slate-900">VexaHost Cloud Indonesia</p>
                    <p class="text-slate-600">Ryan (CEO / CTO)</p>
                    <p class="text-slate-600">Email: admin@vexahost.com</p>
                    <p class="text-slate-600">Dikelola Oleh: RZ Digital Creative</p>
                    <p class="text-slate-600">Infrastruktur: VexaHost Enterprise Cloud</p>
                </div>

                <div>
                    <span class="text-slate-400 font-semibold block mb-1">Ditagihkan Kepada:</span>
                    <p class="font-bold text-slate-900">{{ $invoice->order->customer->full_name }}</p>
                    <p class="text-slate-600">Email: {{ $invoice->order->customer->email }}</p>
                    <p class="text-slate-600 font-mono-code">Username: &#64;{{ $invoice->order->customer->username }}</p>
                    @if($invoice->order->customer->phone)
                        <p class="text-slate-600">Telp/WA: {{ $invoice->order->customer->phone }}</p>
                    @endif
                </div>
            </div>

            <!-- Dates & Payment Method -->
            <div class="grid grid-cols-3 gap-4 py-4 border-b border-slate-200 text-xs">
                <div>
                    <span class="text-slate-400 block font-medium">Tanggal Diterbitkan</span>
                    <span class="font-semibold text-slate-800">{{ $invoice->issued_at ? $invoice->issued_at->format('d F Y') : $invoice->created_at->format('d F Y') }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block font-medium">Jatuh Tempo</span>
                    <span class="font-semibold text-slate-800">{{ $invoice->due_at ? $invoice->due_at->format('d F Y') : '-' }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block font-medium">Metode Pembayaran</span>
                    <span class="font-semibold text-slate-800 uppercase">{{ str_replace('_', ' ', $invoice->order->payment_method ?? 'Midtrans') }}</span>
                </div>
            </div>

            <!-- Items Table -->
            <div class="py-6">
                <table class="w-full text-left text-xs">
                    <thead class="text-slate-500 uppercase font-semibold text-[11px] border-b border-slate-200">
                        <tr>
                            <th class="pb-2.5">Deskripsi Layanan</th>
                            <th class="pb-2.5 text-center">Durasi</th>
                            <th class="pb-2.5 text-right">Harga</th>
                            <th class="pb-2.5 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr>
                            <td class="py-3.5">
                                <p class="font-semibold text-slate-900">{{ $invoice->order->vpsSpec->name ?? 'Cloud VPS Instance' }}</p>
                                <p class="text-slate-500 text-[11px] mt-0.5">
                                    {{ $invoice->order->vpsSpec->cpu ?? 2 }} vCPU, {{ $invoice->order->vpsSpec->ram ?? 4 }} GB RAM, {{ $invoice->order->vpsSpec->disk ?? 60 }} GB SSD, {{ $invoice->order->vpsSpec->bandwidth ?? 30 }} Mbps
                                </p>
                                <p class="text-slate-400 text-[10px]">Datacenter: {{ $invoice->order->datacenter_location }} &bull; Stack: {{ $invoice->order->control_panel_label }}</p>
                            </td>
                            <td class="py-3.5 text-center text-slate-600">
                                @php
                                    $cycleLabel = match($invoice->order->billing_cycle ?? 'monthly') {
                                        'monthly' => '1 Bulan',
                                        'quarterly' => '3 Bulan',
                                        'semi_annual' => '6 Bulan',
                                        'annual' => '12 Bulan',
                                        default => '1 Bulan',
                                    };
                                @endphp
                                {{ $cycleLabel }}
                            </td>
                            <td class="py-3.5 text-right text-slate-800 font-mono-code">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</td>
                            <td class="py-3.5 text-right font-semibold text-slate-900 font-mono-code">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="py-2.5 text-slate-500">Biaya Setup & Inisialisasi Panel</td>
                            <td class="py-2.5 text-center text-slate-500">1x</td>
                            <td class="py-2.5 text-right text-slate-500 font-mono-code">Rp 0</td>
                            <td class="py-2.5 text-right text-[#6ABD73] font-semibold font-mono-code">Rp 0</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Totals -->
            <div class="pt-4 border-t border-slate-200 flex justify-end text-xs">
                <div class="w-56 space-y-1.5">
                    <div class="flex justify-between text-slate-600">
                        <span>Subtotal:</span>
                        <span class="font-mono-code font-semibold">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-slate-600">
                        <span>PPN (0%):</span>
                        <span class="font-mono-code font-semibold">Rp 0</span>
                    </div>
                    <div class="flex justify-between text-sm font-bold text-slate-900 pt-2 border-t border-slate-200">
                        <span>Total Bayar:</span>
                        <span class="font-mono-code text-[#4A6FA5]">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Footer Note -->
            <div class="mt-10 pt-4 border-t border-slate-100 text-center text-[11px] text-slate-400">
                <p>Faktur diterbitkan secara elektronik oleh sistem penagihan VexaHost.</p>
                <p>Kontak bantuan: admin@vexahost.com</p>
            </div>
        </div>
    </div>
</body>
</html>
