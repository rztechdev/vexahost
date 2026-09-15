@extends('layouts.admin', ['title' => 'Admin Console', 'headerTitle' => 'Dashboard Operasional'])

@section('content')
<div class="space-y-6">

    <!-- Hostinger hPanel Style Server Status Header Card -->
    <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-start sm:items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-[#4A6FA5] flex items-center justify-center font-bold text-lg shrink-0 border border-blue-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2"/></svg>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-base font-bold text-slate-900">VexaHost Reseller Node Cluster</h2>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        Semua Sistem Beroperasi
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-1">
                    Datacenter: <strong>Singapore (Tier-3 Gateway)</strong> & <strong>Jakarta (Cyber Building)</strong> &bull; Total VPS Aktif: <span class="font-bold text-slate-800">{{ $active_vps }} unit</span>
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2.5 shrink-0">
            <a href="{{ route('admin.shopee') }}" class="px-4 py-2 rounded-lg bg-orange-600 hover:bg-orange-700 text-white font-semibold text-xs shadow-xs transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M19 6h-2c0-2.76-2.24-5-5-5S7 3.24 7 6H5c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm-7-3c1.66 0 3 1.34 3 3H9c0-1.66 1.34-3 3-3zm7 17H5V8h14v12z"/></svg>
                <span>+ Proses Shopee</span>
            </a>
            <a href="{{ route('admin.orders') }}" class="px-4 py-2 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs transition-colors">
                Antrean Order ({{ $pending_orders }})
            </a>
        </div>
    </div>

    <!-- KPI Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500">Antrean Pending</p>
                <p class="text-2xl font-bold text-amber-600 mt-1 font-mono-code">{{ $pending_orders }}</p>
                <a href="{{ route('admin.orders') }}?status=pending" class="text-[11px] text-[#4A6FA5] font-medium hover:underline mt-1 inline-block">
                    Perlu provisioning &rarr;
                </a>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500">VPS Berjalan</p>
                <p class="text-2xl font-bold text-[#6ABD73] mt-1 font-mono-code">{{ $active_vps }}</p>
                <p class="text-[11px] text-slate-400 mt-1">Status running 99% SLA</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-[#6ABD73] flex items-center justify-center font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2"/></svg>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500">Total Pelanggan</p>
                <p class="text-2xl font-bold text-slate-800 mt-1 font-mono-code">{{ $total_customers }}</p>
                <p class="text-[11px] text-slate-400 mt-1">Web: {{ $orders_web }} &bull; Shopee: {{ $orders_shopee }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-[#4A6FA5] flex items-center justify-center font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500">Estimasi Omzet</p>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono-code">Rp {{ number_format($total_revenue, 0, ',', '.') }}</p>
                <p class="text-[11px] text-slate-400 mt-1">Faktur terbayar aktif</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
    </div>

    <!-- Dual Channel Performance & Package Distribution -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Channel Penjualan -->
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-xs space-y-4">
            <h3 class="text-sm font-bold text-slate-900 pb-2 border-b border-slate-100">Channel Penjualan</h3>

            <div class="space-y-3 text-xs">
                <div class="p-3 rounded-lg bg-blue-50/60 border border-blue-100 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-[#4A6FA5] text-white flex items-center justify-center font-bold text-xs">
                            WEB
                        </div>
                        <div>
                            <span class="font-bold text-slate-900 block">Website Direct</span>
                            <span class="text-slate-500 text-[11px]">Midtrans SNAP & QRIS</span>
                        </div>
                    </div>
                    <span class="font-mono-code font-bold text-slate-900">{{ $orders_web }} Order</span>
                </div>

                <div class="p-3 rounded-lg bg-orange-50/60 border border-orange-100 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-orange-600 text-white flex items-center justify-center font-bold text-xs">
                            SHP
                        </div>
                        <div>
                            <span class="font-bold text-slate-900 block">Shopee Marketplace</span>
                            <span class="text-slate-500 text-[11px]">Order auto-credential</span>
                        </div>
                    </div>
                    <span class="font-mono-code font-bold text-slate-900">{{ $orders_shopee }} Order</span>
                </div>
            </div>

            <a href="{{ route('admin.shopee') }}" class="w-full py-2 px-3 rounded-lg bg-orange-600 hover:bg-orange-700 text-white font-semibold text-xs block text-center transition-colors">
                + Input Transaksi Shopee
            </a>
        </div>

        <!-- Popularitas Paket -->
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-xs space-y-4">
            <h3 class="text-sm font-bold text-slate-900 pb-2 border-b border-slate-100">Distribusi Paket VPS</h3>

            <div class="space-y-3 text-xs">
                @foreach($specs as $spec)
                    <div>
                        <div class="flex justify-between items-center mb-1 font-medium">
                            <span class="text-slate-800">{{ $spec->name }}</span>
                            <span class="font-mono-code text-slate-500 font-semibold">{{ $spec->orders_count }} order</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                            <div class="bg-[#4A6FA5] h-2 rounded-full" style="width: {{ $specs->sum('orders_count') > 0 ? ($spec->orders_count / $specs->sum('orders_count')) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Open Support Tickets -->
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-xs space-y-3 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-2 border-b border-slate-100 mb-3">
                    <h3 class="text-sm font-bold text-slate-900">Tiket Dukungan Masuk</h3>
                    <a href="{{ route('admin.tickets') }}" class="text-xs text-[#4A6FA5] font-semibold hover:underline">Semua &rarr;</a>
                </div>

                <div class="space-y-2 text-xs">
                    @forelse($open_tickets as $ticket)
                        <a href="{{ route('admin.tickets.show', $ticket->id) }}" class="p-2.5 rounded-lg border border-slate-200 hover:bg-slate-50 block transition-colors">
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-bold text-slate-900 truncate max-w-[160px]">{{ $ticket->subject }}</span>
                                <span class="px-1.5 py-0.2 rounded text-[10px] font-semibold uppercase {{ $ticket->priority === 'high' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700' }}">
                                    {{ $ticket->priority }}
                                </span>
                            </div>
                            <span class="text-slate-500 text-[11px]">{{ $ticket->customer->full_name }} &bull; {{ $ticket->created_at->diffForHumans() }}</span>
                        </a>
                    @empty
                        <p class="text-slate-400 text-center py-6">Tidak ada tiket terbuka.</p>
                    @endforelse
                </div>
            </div>

            <a href="{{ route('admin.tickets') }}" class="w-full py-2 px-3 rounded-lg border border-slate-300 hover:bg-slate-50 text-slate-700 font-medium text-xs text-center block transition-colors">
                Buka Helpdesk Admin
            </a>
        </div>
    </div>

    <!-- Recent Orders Table -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-xs">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-900 text-sm">Pesanan Terbaru</h3>
                <p class="text-xs text-slate-500">Daftar transaksi masuk dari Website dan Shopee</p>
            </div>
            <a href="{{ route('admin.orders') }}" class="text-xs font-semibold text-[#4A6FA5] hover:underline">
                Lihat Semua Antrean &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-semibold uppercase tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-3">ID Order</th>
                        <th class="px-5 py-3">Pelanggan</th>
                        <th class="px-5 py-3">Channel</th>
                        <th class="px-5 py-3">Paket</th>
                        <th class="px-5 py-3">Panel</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3">Total</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($recent_orders as $order)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="px-5 py-3.5 font-mono-code font-semibold text-slate-900">#ORD-{{ $order->id }}</td>
                            <td class="px-5 py-3.5">
                                <span class="font-semibold text-slate-900 block">{{ $order->customer->full_name }}</span>
                                <span class="text-slate-400 font-mono-code text-[11px]">{{ $order->customer->email }}</span>
                            </td>
                            <td class="px-5 py-3.5">
                                @if($order->channel === 'shopee')
                                    <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-orange-100 text-orange-800">Shopee</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-blue-100 text-blue-800">Website</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 font-medium text-slate-900">{{ $order->vpsSpec->name ?? 'Standard' }}</td>
                            <td class="px-5 py-3.5 text-slate-500 font-semibold">{{ $order->control_panel_label }}</td>
                            <td class="px-5 py-3.5">
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold uppercase
                                    @if($order->status === 'active') bg-emerald-50 text-[#6ABD73] border border-emerald-200
                                    @elseif($order->status === 'pending') bg-amber-50 text-amber-700 border border-amber-200
                                    @elseif($order->status === 'cancelled') bg-rose-50 text-rose-700 border border-rose-200
                                    @else bg-blue-50 text-blue-700 border border-blue-200
                                    @endif">
                                    {{ $order->status }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 font-mono-code font-bold text-slate-900">Rp {{ number_format($order->amount, 0, ',', '.') }}</td>
                            <td class="px-5 py-3.5 text-right">
                                <a href="{{ route('admin.orders') }}" class="text-[#4A6FA5] font-semibold hover:underline">
                                    Detail &rarr;
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
