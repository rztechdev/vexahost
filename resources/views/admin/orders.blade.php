@extends('layouts.admin', ['title' => 'Manajemen Orders', 'headerTitle' => 'Antrean & Manajemen Order'])

@section('content')
<div class="space-y-6" x-data="{
    provisionModal: false,
    currentOrder: null,
    publicIp: '',
    privateIp: '',
    hostname: '',
    appUrl: '',
    os: 'Ubuntu 24.04 LTS',
    openProvision(order) {
        this.currentOrder = order;
        this.hostname = order.hostname || ('vps-' + order.customer.username.toLowerCase());
        this.publicIp = '';
        this.privateIp = '';
        this.appUrl = '';
        this.os = order.os_label || (order.os === 'ubuntu2404' ? 'Ubuntu 24.04 LTS' : order.os);
        this.provisionModal = true;
    }
}">
    <!-- Top Filter Tabs (No lines under title) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-1">
        <div class="flex items-center gap-2 overflow-x-auto text-xs font-medium">
            <a href="{{ route('admin.orders') }}" 
               class="px-3.5 py-2 rounded-lg transition-colors whitespace-nowrap {{ !request('status') && !request('channel') ? 'bg-black text-white font-bold' : 'bg-white text-slate-700 border border-slate-300 hover:bg-slate-50' }}">
                Semua Order
            </a>

            <a href="{{ route('admin.orders') }}?status=pending" 
               class="px-3.5 py-2 rounded-lg transition-colors whitespace-nowrap flex items-center gap-1.5 {{ request('status') === 'pending' ? 'bg-black text-white font-bold' : 'bg-white text-slate-700 border border-slate-300 hover:bg-slate-50' }}">
                <span>Perlu Provisioning</span>
                @php $pendingCount = \App\Models\Order::where('status', 'pending')->count(); @endphp
                @if($pendingCount > 0)
                    <span class="px-1.5 py-0.2 rounded text-[10px] font-bold {{ request('status') === 'pending' ? 'bg-white text-black' : 'bg-slate-200 text-black' }}">
                        {{ $pendingCount }}
                    </span>
                @endif
            </a>

            <a href="{{ route('admin.orders') }}?status=active" 
               class="px-3.5 py-2 rounded-lg transition-colors whitespace-nowrap {{ request('status') === 'active' ? 'bg-black text-white font-bold' : 'bg-white text-slate-700 border border-slate-300 hover:bg-slate-50' }}">
                Aktif Running
            </a>

            <a href="{{ route('admin.orders') }}?channel=shopee" 
               class="px-3.5 py-2 rounded-lg transition-colors whitespace-nowrap flex items-center gap-1 {{ request('channel') === 'shopee' ? 'bg-black text-white font-bold' : 'bg-white text-slate-700 border border-slate-300 hover:bg-slate-50' }}">
                <span>Shopee Orders</span>
            </a>

            <a href="{{ route('admin.orders') }}?channel=website" 
               class="px-3.5 py-2 rounded-lg transition-colors whitespace-nowrap {{ request('channel') === 'website' ? 'bg-black text-white font-bold' : 'bg-white text-slate-700 border border-slate-300 hover:bg-slate-50' }}">
                Website Direct
            </a>
        </div>

        <a href="{{ route('admin.shopee') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-black hover:bg-neutral-800 text-white font-bold text-xs transition-colors self-start sm:self-auto shrink-0">
            <span>+ Order Shopee Baru</span>
        </a>
    </div>

    <!-- Orders Table (No lines under headings) -->
    <div class="bg-white rounded-lg border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-600 font-semibold uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-3">ID Order</th>
                        <th class="px-5 py-3">Pelanggan</th>
                        <th class="px-5 py-3">Channel</th>
                        <th class="px-5 py-3">Paket & Panel</th>
                        <th class="px-5 py-3">Lokasi & OS</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3">Nominal</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($orders as $order)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-5 py-3.5 font-mono-code font-bold text-slate-900">
                                #ORD-{{ $order->id }}
                                @if($order->shopee_order_id)
                                    <span class="block text-[11px] text-slate-500 font-mono-code font-normal">{{ $order->shopee_order_id }}</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-slate-200 text-slate-800 flex items-center justify-center font-bold text-xs">
                                        {{ strtoupper(substr($order->customer->full_name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <span class="font-bold text-slate-900 block">{{ $order->customer->full_name }}</span>
                                        <span class="text-slate-500 font-mono-code text-[11px]">&#64;{{ $order->customer->username }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="px-2 py-0.5 rounded text-[11px] font-semibold border border-slate-300 bg-slate-50 text-slate-900">
                                    {{ ucfirst($order->channel) }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="font-semibold text-slate-900">{{ $order->vpsSpec->name ?? 'Standard' }}</span>
                                    @if($order->isAiPackage())
                                        <span class="px-1.5 py-0.2 rounded text-[10px] font-bold uppercase bg-blue-100 text-blue-800 border border-blue-200">
                                            AI Combo
                                        </span>
                                    @endif
                                    @if($order->hostname)
                                        <span class="text-[10px] font-mono-code px-1.5 py-0.5 rounded bg-slate-100 text-slate-700 font-semibold" title="VPS Name">
                                            {{ $order->hostname }}
                                        </span>
                                    @endif
                                </div>
                                <span class="text-[11px] text-slate-500 font-medium block">{{ $order->control_panel_label }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-slate-700">
                                <span class="block font-medium">{{ $order->provider_label }} · {{ ucfirst($order->datacenter_location) }}</span>
                                <span class="text-[11px] text-slate-500">{{ $order->os_label }}</span>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="px-2.5 py-0.5 rounded text-[11px] font-semibold uppercase border border-slate-300 bg-slate-50 text-slate-900">
                                    {{ $order->status }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 font-mono-code font-bold text-slate-900">
                                Rp {{ number_format($order->amount, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    @if($order->status === 'pending')
                                        <button @click="openProvision({{ json_encode(['id' => $order->id, 'hostname' => $order->hostname, 'provider' => $order->provider_label, 'datacenter' => ucfirst($order->datacenter_location), 'os' => $order->os, 'os_label' => $order->os_label, 'stack' => $order->control_panel_label, 'customer' => ['username' => $order->customer->username ?? 'user']]) }})" 
                                                class="px-3 py-1.5 rounded bg-black hover:bg-neutral-800 text-white font-bold text-xs transition-colors">
                                            Provision
                                        </button>
                                        <form action="{{ route('admin.orders.cancel', $order->id) }}" method="POST" data-confirm="Batalkan order ini?">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1.5 rounded border border-slate-300 text-slate-700 hover:bg-slate-100 text-xs">
                                                Batal
                                            </button>
                                        </form>
                                    @else
                                        @if($order->invoice)
                                            <a href="{{ route('dashboard.invoice.print', $order->invoice->id) }}" target="_blank" class="text-slate-900 underline font-bold text-xs">
                                                Faktur PDF
                                            </a>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-12 text-center text-slate-400">
                                Tidak ada data pesanan pada kriteria yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

    <!-- Provisioning Modal -->
    <div x-show="provisionModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40" style="display: none;">
        <div class="bg-white rounded-lg border border-slate-200 p-6 max-w-md w-full" @click.away="provisionModal = false">
            <h3 class="text-base font-bold text-slate-900 mb-1">Provisioning Server VexaHost</h3>
            <p class="text-xs text-slate-500 mb-3">
                Input alokasi IP publik dan hostname untuk order <strong x-text="currentOrder ? '#ORD-' + currentOrder.id : ''"></strong>.
            </p>

            <div x-show="currentOrder" class="mb-4 p-3 bg-slate-50 rounded-lg border border-slate-200 text-xs space-y-1">
                <div class="flex justify-between"><span class="text-slate-500">Provider & DC:</span><span class="font-medium text-slate-800" x-text="(currentOrder?.provider || '') + ' · ' + (currentOrder?.datacenter || '')"></span></div>
                <div class="flex justify-between"><span class="text-slate-500">Stack:</span><span class="font-medium text-slate-800" x-text="currentOrder?.stack || ''"></span></div>
            </div>

            <form :action="currentOrder ? '/admin/orders/' + currentOrder.id + '/provision' : '#'" method="POST" class="space-y-3.5 text-xs">
                @csrf

                <div>
                    <label class="block font-medium text-slate-700 mb-1">Public IPv4 Node *</label>
                    <input type="text" name="public_ip" required x-model="publicIp" placeholder="139.180.xxx.xxx"
                           class="w-full px-3 py-2 rounded-lg border border-slate-300 font-mono-code focus:outline-none">
                </div>

                <div>
                    <label class="block font-medium text-slate-700 mb-1">Private VPC IP</label>
                    <input type="text" name="private_ip" x-model="privateIp" placeholder="10.0.1.xxx"
                           class="w-full px-3 py-2 rounded-lg border border-slate-300 font-mono-code focus:outline-none">
                </div>

                <div>
                    <label class="block font-medium text-slate-700 mb-1">Server Hostname *</label>
                    <input type="text" name="hostname" required x-model="hostname" placeholder="vps-nama-klien"
                           class="w-full px-3 py-2 rounded-lg border border-slate-300 font-mono-code focus:outline-none">
                </div>

                <div>
                    <label class="block font-medium text-slate-700 mb-1">Distribusi OS</label>
                    <input type="text" name="os" required x-model="os"
                           class="w-full px-3 py-2 rounded-lg border border-slate-300 bg-slate-50 focus:outline-none">
                </div>

                <div>
                    <label class="block font-medium text-slate-700 mb-1">App URL (Khusus AI Agent / Web App - Opsional)</label>
                    <input type="text" name="app_url" x-model="appUrl" placeholder="http://139.180.xxx.xxx:8443"
                           class="w-full px-3 py-2 rounded-lg border border-slate-300 font-mono-code focus:outline-none">
                    <p class="text-[10px] text-slate-400 mt-0.5">Kosongkan jika ingin sistem menentukan URL default otomatis.</p>
                </div>

                <div class="pt-4 flex items-center justify-end gap-2">
                    <button type="button" @click="provisionModal = false" class="px-4 py-2 rounded-lg text-slate-700 hover:bg-slate-100 font-medium">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-lg bg-black hover:bg-neutral-800 text-white font-bold">
                        Aktifkan VPS Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
