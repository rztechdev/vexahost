@extends('layouts.admin', ['title' => 'Manajemen Paket VPS', 'headerTitle' => 'Katalog Paket VPS Cloud'])

@section('content')
<div class="space-y-6" x-data="{
    createModalOpen: false,
    editModalOpen: false,
    deleteModalOpen: false,
    editData: {
        id: '',
        name: '',
        category: 'vps',
        tagline: '',
        cpu: 1,
        ram: 1,
        disk: 20,
        bandwidth: 1000,
        cost_price: 0,
        sell_price: 0,
        is_active: true
    },
    deleteData: {
        id: '',
        name: ''
    },
    openEdit(spec) {
        this.editData = {
            id: spec.id,
            name: spec.name,
            category: spec.category || 'vps',
            tagline: spec.tagline || '',
            cpu: spec.cpu,
            ram: spec.ram,
            disk: spec.disk,
            bandwidth: spec.bandwidth,
            cost_price: spec.cost_price,
            sell_price: spec.sell_price,
            is_active: Boolean(spec.is_active)
        };
        this.editModalOpen = true;
    },
    openDelete(spec) {
        this.deleteData = {
            id: spec.id,
            name: spec.name
        };
        this.deleteModalOpen = true;
    }
}">

    <!-- Stats Summary Row -->
    @php
        $totalSpecs = $specs->count();
        $activeSpecs = $specs->where('is_active', true)->count();
        $inactiveSpecs = $totalSpecs - $activeSpecs;
        $avgMargin = $totalSpecs > 0 ? $specs->avg(fn($s) => $s->sell_price - $s->cost_price) : 0;
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-lg border border-slate-200">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-1">Total Paket VPS</span>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl font-extrabold text-slate-900 font-mono-code">{{ $totalSpecs }}</span>
                <span class="text-xs text-slate-500">Katalog Tersedia</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-lg border border-slate-200">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-1">Paket Aktif Dijual</span>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl font-extrabold text-emerald-600 font-mono-code">{{ $activeSpecs }}</span>
                <span class="text-xs text-emerald-600 font-medium">Tampil di Checkout</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-lg border border-slate-200">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-1">Paket Nonaktif</span>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl font-extrabold text-slate-600 font-mono-code">{{ $inactiveSpecs }}</span>
                <span class="text-xs text-slate-400">Diarsipkan</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-lg border border-slate-200">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-1">Rata-rata Margin</span>
            <div class="flex items-baseline justify-between">
                <span class="text-xl font-extrabold text-slate-900 font-mono-code">Rp {{ number_format($avgMargin, 0, ',', '.') }}</span>
                <span class="text-xs text-slate-500">Per Bulan/Unit</span>
            </div>
        </div>
    </div>

    <!-- Main Table Container -->
    <div class="bg-white rounded-lg border border-slate-200 overflow-hidden">
        <div class="p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="font-bold text-slate-900 text-base">Katalog Spesifikasi Cloud VPS</h3>
                <p class="text-xs text-slate-500">Kelola spesifikasi hardware, margin profit, dan ketersediaan paket untuk pelanggan.</p>
            </div>
            <div>
                <button @click="createModalOpen = true"
                        type="button"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-black hover:bg-neutral-800 text-white text-xs font-bold transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Tambah Paket Baru</span>
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-600 font-semibold uppercase tracking-wider border-y border-slate-200">
                    <tr>
                        <th class="px-5 py-3.5">Nama Paket</th>
                        <th class="px-5 py-3.5">Spesifikasi Hardware</th>
                        <th class="px-5 py-3.5">Provider Cloud</th>
                        <th class="px-5 py-3.5">Harga Pokok (Modal)</th>
                        <th class="px-5 py-3.5">Harga Jual</th>
                        <th class="px-5 py-3.5">Margin Profit</th>
                        <th class="px-5 py-3.5 text-center">Total Order</th>
                        <th class="px-5 py-3.5 text-center">Status</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($specs as $spec)
                        @php
                            $isCore = in_array(strtolower($spec->name), array_map('strtolower', $corePlanNames), true) || in_array((int)$spec->id, [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13], true);
                            $margin = $spec->sell_price - $spec->cost_price;
                            $marginPercent = $spec->sell_price > 0 ? round(($margin / $spec->sell_price) * 100, 1) : 0;
                            $provider = $spec->default_provider;
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <!-- Nama Paket -->
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-slate-900 text-sm block">{{ $spec->name }}</span>
                                    @if($spec->category === 'managed_db' || $spec->isDatabasePackage())
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-emerald-100 text-emerald-800 border border-emerald-200">
                                            Managed DB
                                        </span>
                                    @elseif($spec->isAiPackage())
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-blue-100 text-blue-800 border border-blue-200">
                                            AI Combo
                                        </span>
                                    @endif
                                    @if($isCore)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-700 border border-slate-200" title="Paket sistem inti (dilindungi dari penghapusan)">
                                            <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                            Inti
                                        </span>
                                    @else
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                            Kustom
                                        </span>
                                    @endif
                                </div>
                                @if($spec->tagline)
                                    <span class="text-[11px] text-slate-500 italic block">{{ $spec->tagline }}</span>
                                @endif
                                <span class="text-[11px] text-slate-400 font-mono-code">ID: #SPEC-{{ str_pad($spec->id, 3, '0', STR_PAD_LEFT) }}</span>
                            </td>

                            <!-- Spesifikasi Hardware -->
                            <td class="px-5 py-3.5 text-slate-700">
                                <div class="font-mono-code font-semibold text-slate-900">
                                    {{ $spec->cpu }} vCPU &bull; {{ $spec->ram }} GB RAM
                                </div>
                                <div class="text-[11px] text-slate-500">
                                    {{ $spec->disk }} GB NVMe SSD &bull; {{ $spec->bandwidth >= 1000 ? ($spec->bandwidth / 1000) . ' TB' : $spec->bandwidth . ' GB' }} BW
                                </div>
                            </td>

                            <!-- Provider Cloud -->
                            <td class="px-5 py-3.5">
                                @if($provider === 'cloudeka')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded text-[11px] font-semibold bg-sky-50 text-sky-800 border border-sky-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-sky-600"></span>
                                        Cloudeka (Lintasarta)
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded text-[11px] font-semibold bg-indigo-50 text-indigo-800 border border-indigo-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-600"></span>
                                        Tencent Cloud
                                    </span>
                                @endif
                            </td>

                            <!-- Harga Pokok -->
                            <td class="px-5 py-3.5 font-mono-code text-slate-600">
                                Rp {{ number_format($spec->cost_price, 0, ',', '.') }}
                            </td>

                            <!-- Harga Jual -->
                            <td class="px-5 py-3.5 font-mono-code font-bold text-slate-900">
                                Rp {{ number_format($spec->sell_price, 0, ',', '.') }}
                            </td>

                            <!-- Margin -->
                            <td class="px-5 py-3.5">
                                <span class="font-mono-code font-semibold text-emerald-700 block">
                                    +Rp {{ number_format($margin, 0, ',', '.') }}
                                </span>
                                <span class="text-[10px] text-slate-500 font-mono-code">({{ $marginPercent }}%)</span>
                            </td>

                            <!-- Total Order -->
                            <td class="px-5 py-3.5 text-center font-mono-code font-semibold text-slate-700">
                                {{ $spec->orders_count }}
                            </td>

                            <!-- Status -->
                            <td class="px-5 py-3.5 text-center">
                                @if($spec->is_active)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-slate-500 border border-slate-200">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>

                            <!-- Aksi -->
                            <td class="px-5 py-3.5 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <!-- Edit Button -->
                                    <button @click="openEdit({{ $spec->toJson() }})"
                                            type="button"
                                            class="p-1.5 rounded text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors"
                                            title="Edit Spesifikasi & Harga">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>

                                    <!-- Delete Button (Conditional) -->
                                    @if($isCore)
                                        <button type="button"
                                                disabled
                                                class="p-1.5 rounded text-slate-300 cursor-not-allowed"
                                                title="Paket inti dilindungi sistem dan tidak dapat dihapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                        </button>
                                    @elseif($spec->orders_count > 0)
                                        <button type="button"
                                                disabled
                                                class="p-1.5 rounded text-slate-300 cursor-not-allowed"
                                                title="Paket memiliki {{ $spec->orders_count }} riwayat pesanan, tidak dapat dihapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    @else
                                        <button @click="openDelete({{ $spec->toJson() }})"
                                                type="button"
                                                class="p-1.5 rounded text-rose-600 hover:text-rose-800 hover:bg-rose-50 transition-colors"
                                                title="Hapus Paket Kustom">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-5 py-10 text-center text-slate-400">
                                Belum ada spesifikasi paket VPS di database.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Tambah Paket Baru -->
    <div x-show="createModalOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
         style="display: none;"
         @keydown.escape.window="createModalOpen = false">

        <div @click.away="createModalOpen = false"
             class="bg-white rounded-xl border border-slate-200 shadow-xl max-w-lg w-full overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-900 text-base">Tambah Paket VPS Baru</h3>
                    <p class="text-xs text-slate-500">Konfigurasi spesifikasi dan harga paket baru</p>
                </div>
                <button @click="createModalOpen = false" class="text-slate-400 hover:text-slate-600 p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('admin.packages.store') }}" method="POST" class="p-6 space-y-4">
                @csrf

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Nama Paket</label>
                        <input type="text" name="name" required placeholder="Contoh: Starter Node, Pro Developer"
                               class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-black">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Kategori Layanan</label>
                        <select name="category" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-black">
                            <option value="vps">Cloud VPS KVM</option>
                            <option value="ai_combo">VexaHost AI Combo</option>
                            <option value="managed_db">Managed Database VPS</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">CPU (vCPU)</label>
                        <input type="number" name="cpu" min="1" max="128" value="2" required
                               class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-black">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">RAM (GB)</label>
                        <input type="number" name="ram" min="1" max="1024" value="4" required
                               class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-black">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Disk NVMe (GB)</label>
                        <input type="number" name="disk" min="5" max="10000" value="60" required
                               class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-black">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Bandwidth (GB)</label>
                        <input type="number" name="bandwidth" min="1" max="100000" value="1000" required
                               class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-black">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Harga Modal (Rp)</label>
                        <input type="number" name="cost_price" min="0" value="70000" required
                               class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-black">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Harga Jual (Rp)</label>
                        <input type="number" name="sell_price" min="0" value="120000" required
                               class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-black">
                    </div>
                </div>

                <div class="flex items-center gap-2 pt-2">
                    <input type="checkbox" name="is_active" id="create_is_active" value="1" checked
                           class="w-4 h-4 rounded border-slate-300 text-black focus:ring-black">
                    <label for="create_is_active" class="text-xs font-medium text-slate-700 cursor-pointer">
                        Aktifkan paket segera (tersedia di form checkout publik)
                    </label>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button @click="createModalOpen = false" type="button"
                            class="px-4 py-2 rounded-lg border border-slate-300 text-slate-700 text-xs font-semibold hover:bg-slate-50 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 rounded-lg bg-black text-white text-xs font-bold hover:bg-neutral-800 transition-colors">
                        Simpan Paket
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Paket -->
    <div x-show="editModalOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
         style="display: none;"
         @keydown.escape.window="editModalOpen = false">

        <div @click.away="editModalOpen = false"
             class="bg-white rounded-xl border border-slate-200 shadow-xl max-w-lg w-full overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-900 text-base">Edit Paket VPS</h3>
                    <p class="text-xs text-slate-500">Perbarui spesifikasi atau harga paket <span class="font-semibold text-slate-800" x-text="editData.name"></span></p>
                </div>
                <button @click="editModalOpen = false" class="text-slate-400 hover:text-slate-600 p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form :action="`{{ url('admin/packages') }}/${editData.id}`" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Nama Paket</label>
                        <input type="text" name="name" x-model="editData.name" required
                               class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-black">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Kategori Layanan</label>
                        <select name="category" x-model="editData.category" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-black">
                            <option value="vps">Cloud VPS KVM</option>
                            <option value="ai_combo">VexaHost AI Combo</option>
                            <option value="managed_db">Managed Database VPS</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">CPU (vCPU)</label>
                        <input type="number" name="cpu" x-model="editData.cpu" min="1" max="128" required
                               class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-black">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">RAM (GB)</label>
                        <input type="number" name="ram" x-model="editData.ram" min="1" max="1024" required
                               class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-black">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Disk NVMe (GB)</label>
                        <input type="number" name="disk" x-model="editData.disk" min="5" max="10000" required
                               class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-black">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Bandwidth (GB)</label>
                        <input type="number" name="bandwidth" x-model="editData.bandwidth" min="1" max="100000" required
                               class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-black">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Harga Modal (Rp)</label>
                        <input type="number" name="cost_price" x-model="editData.cost_price" min="0" required
                               class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-black">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Harga Jual (Rp)</label>
                        <input type="number" name="sell_price" x-model="editData.sell_price" min="0" required
                               class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-black">
                    </div>
                </div>

                <div class="flex items-center gap-2 pt-2">
                    <input type="checkbox" name="is_active" id="edit_is_active" value="1" :checked="editData.is_active"
                           class="w-4 h-4 rounded border-slate-300 text-black focus:ring-black">
                    <label for="edit_is_active" class="text-xs font-medium text-slate-700 cursor-pointer">
                        Paket Aktif (tampil di form pemesanan pelanggan)
                    </label>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button @click="editModalOpen = false" type="button"
                            class="px-4 py-2 rounded-lg border border-slate-300 text-slate-700 text-xs font-semibold hover:bg-slate-50 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 rounded-lg bg-black text-white text-xs font-bold hover:bg-neutral-800 transition-colors">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus -->
    <div x-show="deleteModalOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
         style="display: none;"
         @keydown.escape.window="deleteModalOpen = false">

        <div @click.away="deleteModalOpen = false"
             class="bg-white rounded-xl border border-slate-200 shadow-xl max-w-md w-full p-6 text-center">
            <div class="w-12 h-12 rounded-full bg-rose-100 text-rose-600 mx-auto flex items-center justify-center mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>

            <h3 class="font-bold text-slate-900 text-base mb-2">Hapus Paket VPS?</h3>
            <p class="text-xs text-slate-500 mb-6 leading-relaxed">
                Anda yakin ingin menghapus paket <span class="font-bold text-slate-900" x-text="deleteData.name"></span>? Tindakan ini permanen dan tidak dapat dibatalkan.
            </p>

            <form :action="`{{ url('admin/packages') }}/${deleteData.id}`" method="POST" class="flex items-center justify-center gap-3">
                @csrf
                @method('DELETE')

                <button @click="deleteModalOpen = false" type="button"
                        class="px-4 py-2 rounded-lg border border-slate-300 text-slate-700 text-xs font-semibold hover:bg-slate-50 transition-colors">
                    Batalkan
                </button>
                <button type="submit"
                        class="px-4 py-2 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition-colors">
                    Ya, Hapus Paket
                </button>
            </form>
        </div>
    </div>

</div>
@endsection
