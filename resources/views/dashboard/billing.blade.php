@extends('layouts.dashboard', ['title' => 'Tagihan & Invoice', 'headerTitle' => 'Tagihan & Invoice'])

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-lg border border-slate-200 overflow-hidden">
        <div class="p-5 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-900 text-base">Layanan Berlangganan</h3>
                <p class="text-xs text-slate-500 mt-1">Kelola perpanjangan otomatis tanpa perlu menghubungi support.</p>
            </div>
            <span class="text-xs text-slate-500">{{ $subscriptions->count() }} layanan</span>
        </div>
        <div class="divide-y divide-slate-100">
            @forelse($subscriptions as $subscription)
                <div class="p-5 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-semibold text-slate-900">{{ $subscription->vpsInstance->hostname ?? $subscription->vpsSpec->name ?? 'VPS' }}</span>
                            <span class="px-2 py-0.5 rounded text-[10px] uppercase font-semibold border border-slate-300 bg-slate-50">{{ str_replace('_', ' ', $subscription->status) }}</span>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">Periode berikutnya: {{ $subscription->next_billing_at?->format('d M Y') ?? $subscription->current_period_end?->format('d M Y') ?? '-' }} · Rp {{ number_format($subscription->unit_amount, 0, ',', '.') }}/{{ $subscription->billing_cycle === 'yearly' ? 'tahun' : 'bulan' }}</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2 text-xs">
                        @if($subscription->cancelled_at)
                            <form method="POST" action="{{ route('dashboard.subscriptions.resume', $subscription->id) }}">@csrf<button class="px-3 py-1.5 rounded bg-black text-white font-semibold">Lanjutkan</button></form>
                        @else
                            <form method="POST" action="{{ route('dashboard.subscriptions.auto-renew', $subscription->id) }}">@csrf<input type="hidden" name="auto_renew" value="{{ $subscription->auto_renew ? 0 : 1 }}"><button class="px-3 py-1.5 rounded border border-slate-300 font-semibold">{{ $subscription->auto_renew ? 'Matikan Auto-renew' : 'Aktifkan Auto-renew' }}</button></form>
                            @if($subscription->isCancellable())
                                <form method="POST" action="{{ route('dashboard.subscriptions.cancel', $subscription->id) }}" onsubmit="return confirm('Batalkan perpanjangan layanan ini?')">@csrf<button class="px-3 py-1.5 rounded border border-red-200 text-red-700 font-semibold">Batalkan</button></form>
                            @endif
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-xs text-slate-400">Belum ada langganan aktif.</div>
            @endforelse
        </div>
    </div>

    <!-- Top Summary (No line under title) -->
    <div class="bg-white rounded-lg border border-slate-200 p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-base font-bold text-slate-900">Langganan & Faktur Pembayaran</h2>
            <p class="text-xs text-slate-500 mt-1">
                Unduh atau cetak faktur untuk administrasi atau pembukuan Anda.
            </p>
        </div>
        <div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold border border-slate-300 bg-slate-50 text-slate-900">
                Status Akun: Aktif
            </span>
        </div>
    </div>

    <!-- Invoices Table (No line under title) -->
    <div class="bg-white rounded-lg border border-slate-200 overflow-hidden">
        <div class="p-5 flex items-center justify-between">
            <h3 class="font-bold text-slate-900 text-base">Riwayat Faktur</h3>
            <span class="text-xs text-slate-500">Total: {{ $invoices->total() }} Faktur</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-600 font-semibold uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-3">Nomor Faktur</th>
                        <th class="px-5 py-3">Layanan</th>
                        <th class="px-5 py-3">Tanggal Terbit</th>
                        <th class="px-5 py-3">Jatuh Tempo</th>
                        <th class="px-5 py-3">Jumlah</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($invoices as $invoice)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-5 py-3.5 font-mono-code font-bold text-slate-900">
                                {{ $invoice->invoice_number }}
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="font-semibold text-slate-900">{{ $invoice->order->vpsSpec->name ?? 'Cloud VPS' }}</span>
                                <span class="text-slate-400 block text-[11px]">Stack: {{ $invoice->order->control_panel_label }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-slate-600">
                                {{ $invoice->issued_at ? $invoice->issued_at->format('d M Y') : $invoice->created_at->format('d M Y') }}
                            </td>
                            <td class="px-5 py-3.5 text-slate-600">
                                {{ $invoice->due_at ? $invoice->due_at->format('d M Y') : '-' }}
                            </td>
                            <td class="px-5 py-3.5 font-mono-code font-bold text-slate-900">
                                Rp {{ number_format($invoice->amount, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="px-2.5 py-0.5 rounded text-xs font-semibold uppercase border border-slate-300 bg-slate-50 text-slate-900">
                                    {{ $invoice->status === 'paid' ? 'Lunas' : ucfirst($invoice->status) }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <a href="{{ route('dashboard.invoice.print', $invoice->id) }}" target="_blank" 
                                   class="inline-flex items-center gap-1 px-3 py-1 rounded bg-black hover:bg-neutral-800 text-white text-xs font-semibold transition-colors">
                                    Cetak
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-10 text-center text-slate-400">
                                Belum ada faktur tagihan yang diterbitkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($invoices->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $invoices->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
