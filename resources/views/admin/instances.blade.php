@extends('layouts.admin', ['title' => 'Cloud Instances', 'headerTitle' => 'Manajemen Server & Cloud Instances', 'backUrl' => route('admin.index'), 'backLabel' => 'Kembali ke Dashboard Admin'])

@section('content')
<div class="space-y-6">
    <!-- Stat Summary Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg border border-slate-200 p-4">
            <span class="text-xs text-slate-500 font-medium">Total Cloud Instances</span>
            <p class="text-2xl font-bold font-mono-code text-slate-900 mt-1">{{ $categoryCounts['all'] ?? 0 }}</p>
            <span class="text-[11px] text-slate-500 mt-0.5 block">Semua node aktif</span>
        </div>

        <div class="bg-white rounded-lg border border-slate-200 p-4">
            <span class="text-xs text-slate-500 font-medium">Cloud VPS Standar</span>
            <p class="text-2xl font-bold font-mono-code text-slate-900 mt-1">{{ $categoryCounts['vps'] ?? 0 }}</p>
            <span class="text-[11px] text-slate-500 mt-0.5 block">Compute & VM node</span>
        </div>

        <div class="bg-white rounded-lg border border-slate-200 p-4">
            <span class="text-xs text-slate-500 font-medium">AI Agent & Workstation</span>
            <p class="text-2xl font-bold font-mono-code text-slate-900 mt-1">{{ $categoryCounts['ai'] ?? 0 }}</p>
            <span class="text-[11px] text-slate-500 mt-0.5 block">Hermes, VSCode, Ollama</span>
        </div>

        <div class="bg-white rounded-lg border border-slate-200 p-4">
            <span class="text-xs text-slate-500 font-medium">Managed Database</span>
            <p class="text-2xl font-bold font-mono-code text-slate-900 mt-1">{{ $categoryCounts['database'] ?? 0 }}</p>
            <span class="text-[11px] text-slate-500 mt-0.5 block">PostgreSQL, MySQL, Redis</span>
        </div>
    </div>

    <!-- Category Tabs and Search Filters -->
    <div class="space-y-3">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
            <!-- Category Pills -->
            <div class="flex items-center gap-2 overflow-x-auto text-xs font-medium pb-1 md:pb-0">
                <a href="{{ route('admin.instances', array_merge(request()->except(['category', 'page']), ['category' => 'all'])) }}"
                   class="px-3.5 py-2 rounded-lg transition-colors whitespace-nowrap {{ ($category ?? 'all') === 'all' ? 'bg-black text-white font-bold' : 'bg-white text-slate-700 border border-slate-300 hover:bg-slate-50' }}">
                    Semua Instance ({{ $categoryCounts['all'] ?? 0 }})
                </a>

                <a href="{{ route('admin.instances', array_merge(request()->except(['category', 'page']), ['category' => 'vps'])) }}"
                   class="px-3.5 py-2 rounded-lg transition-colors whitespace-nowrap {{ ($category ?? '') === 'vps' ? 'bg-black text-white font-bold' : 'bg-white text-slate-700 border border-slate-300 hover:bg-slate-50' }}">
                    Cloud VPS ({{ $categoryCounts['vps'] ?? 0 }})
                </a>

                <a href="{{ route('admin.instances', array_merge(request()->except(['category', 'page']), ['category' => 'ai'])) }}"
                   class="px-3.5 py-2 rounded-lg transition-colors whitespace-nowrap {{ ($category ?? '') === 'ai' ? 'bg-black text-white font-bold' : 'bg-white text-slate-700 border border-slate-300 hover:bg-slate-50' }}">
                    AI Agent & Workstation ({{ $categoryCounts['ai'] ?? 0 }})
                </a>

                <a href="{{ route('admin.instances', array_merge(request()->except(['category', 'page']), ['category' => 'database'])) }}"
                   class="px-3.5 py-2 rounded-lg transition-colors whitespace-nowrap {{ ($category ?? '') === 'database' ? 'bg-black text-white font-bold' : 'bg-white text-slate-700 border border-slate-300 hover:bg-slate-50' }}">
                    Managed Database ({{ $categoryCounts['database'] ?? 0 }})
                </a>
            </div>

            <!-- Search and Status Filter Form -->
            <form method="GET" action="{{ route('admin.instances') }}" class="flex items-center gap-2">
                <input type="hidden" name="category" value="{{ $category ?? 'all' }}">

                <select name="status" class="px-3 py-2 rounded-lg border border-slate-300 text-xs font-medium bg-white focus:outline-none">
                    <option value="all" {{ ($status ?? 'all') === 'all' ? 'selected' : '' }}>Semua Status</option>
                    <option value="running" {{ ($status ?? '') === 'running' ? 'selected' : '' }}>Running ({{ $statusCounts['running'] ?? 0 }})</option>
                    <option value="stopped" {{ ($status ?? '') === 'stopped' ? 'selected' : '' }}>Stopped ({{ $statusCounts['stopped'] ?? 0 }})</option>
                    <option value="provisioning" {{ ($status ?? '') === 'provisioning' ? 'selected' : '' }}>Provisioning ({{ $statusCounts['provisioning'] ?? 0 }})</option>
                    <option value="suspended" {{ ($status ?? '') === 'suspended' ? 'selected' : '' }}>Suspended ({{ $statusCounts['suspended'] ?? 0 }})</option>
                    <option value="terminated" {{ ($status ?? '') === 'terminated' ? 'selected' : '' }}>Terminated ({{ $statusCounts['terminated'] ?? 0 }})</option>
                </select>

                <div class="relative">
                    <input type="text"
                           name="search"
                           value="{{ $search ?? '' }}"
                           placeholder="Cari Hostname, IP, Pelanggan, App..."
                           class="w-48 sm:w-64 px-3 py-2 text-xs rounded-lg border border-slate-300 bg-white placeholder-slate-400 focus:outline-none">
                </div>

                <button type="submit" class="px-3.5 py-2 rounded-lg bg-black text-white text-xs font-bold hover:bg-neutral-800 transition-colors">
                    Cari
                </button>

                @if(!empty($search) || ($status ?? 'all') !== 'all' || ($category ?? 'all') !== 'all')
                    <a href="{{ route('admin.instances') }}" class="px-3 py-2 rounded-lg bg-white border border-slate-300 text-slate-700 text-xs font-medium hover:bg-slate-50 transition-colors">
                        Reset
                    </a>
                @endif
            </form>
        </div>
    </div>

    <!-- Main Instance Table -->
    <div class="bg-white rounded-lg border border-slate-200 overflow-hidden">
        <div class="p-5 flex items-center justify-between border-b border-slate-100">
            <div>
                <h3 class="font-bold text-slate-900 text-base">Daftar VPS Instance Aktif</h3>
                <p class="text-xs text-slate-500">Instance yang dialokasikan pada node Cloud Infrastructure VexaHost</p>
            </div>
            <span class="text-xs font-mono-code font-bold text-slate-900 bg-slate-100 px-3 py-1 rounded border border-slate-200">
                Total: {{ $instances->total() }} Instance
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-600 font-semibold uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-3">Hostname & Kategori</th>
                        <th class="px-5 py-3">Pelanggan</th>
                        <th class="px-5 py-3">Layanan & Akses</th>
                        <th class="px-5 py-3">Jaringan & Node</th>
                        <th class="px-5 py-3">Spesifikasi</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Ubah Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($instances as $instance)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <!-- Hostname & Kategori -->
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="font-bold text-slate-900 block font-mono-code">{{ $instance->hostname }}</span>
                                    @if($instance->isAiPackage())
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold uppercase bg-slate-100 text-slate-800 border border-slate-300">
                                            AI Agent
                                        </span>
                                    @elseif($instance->isDatabasePackage())
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold uppercase bg-slate-100 text-slate-800 border border-slate-300">
                                            Managed DB
                                        </span>
                                    @else
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold uppercase bg-slate-50 text-slate-700 border border-slate-200">
                                            Cloud VPS
                                        </span>
                                    @endif
                                </div>
                                <span class="text-slate-400 font-mono-code text-[11px] block mt-0.5">
                                    #VX-{{ str_pad($instance->id, 5, '0', STR_PAD_LEFT) }}
                                </span>
                            </td>

                            <!-- Pelanggan -->
                            <td class="px-5 py-3.5">
                                <span class="font-semibold text-slate-900 block">{{ $instance->customer->full_name }}</span>
                                <span class="text-slate-500 font-mono-code text-[11px] block">&#64;{{ $instance->customer->username }}</span>
                                <span class="text-slate-400 text-[11px] block truncate max-w-[170px]">{{ $instance->customer->email }}</span>
                            </td>

                            <!-- Layanan & Akses -->
                            <td class="px-5 py-3.5">
                                @if($instance->isAiPackage())
                                    <span class="font-semibold text-slate-900 block">
                                        {{ $instance->app_name ?: ($instance->control_panel_label ?: 'AI Workstation') }}
                                    </span>
                                    <span class="text-slate-500 text-[11px] block">{{ $instance->os_label }}</span>
                                    @if($instance->app_url)
                                        <a href="{{ $instance->app_url }}" target="_blank" rel="noopener" class="text-[11px] text-slate-800 hover:text-black font-mono-code underline block truncate max-w-[190px] mt-0.5">
                                            Web GUI: {{ $instance->app_url }}
                                        </a>
                                    @endif
                                @elseif($instance->isDatabasePackage())
                                    <span class="font-semibold text-slate-900 font-mono-code block">
                                        {{ strtoupper($instance->db_engine ?: 'PostgreSQL') }} :{{ $instance->db_port_resolved }}
                                    </span>
                                    <span class="text-slate-500 font-mono-code text-[11px] block">
                                        DB: {{ $instance->db_name ?: 'vexadb_production' }}
                                    </span>
                                    @if($instance->web_manager_url_resolved)
                                        <a href="{{ $instance->web_manager_url_resolved }}" target="_blank" rel="noopener" class="text-[11px] text-slate-800 hover:text-black font-mono-code underline block truncate max-w-[190px] mt-0.5">
                                            Web Manager: {{ $instance->web_manager_url_resolved }}
                                        </a>
                                    @else
                                        <span class="text-slate-400 text-[11px] block">Akses Remote Port Direct</span>
                                    @endif
                                @else
                                    <span class="font-medium text-slate-900 block">{{ $instance->os_label }}</span>
                                    <span class="text-slate-500 text-[11px] block">{{ $instance->control_panel_label ?: 'Standard SSH' }}</span>
                                @endif
                            </td>

                            <!-- Jaringan & Node -->
                            <td class="px-5 py-3.5 font-mono-code">
                                <span class="text-slate-900 font-bold block">{{ $instance->public_ip ?? 'Pending' }}</span>
                                <span class="text-slate-400 text-[11px] block">{{ $instance->private_ip ?? '-' }}</span>
                                <span class="text-slate-500 font-sans text-[10px] uppercase block mt-0.5">{{ $instance->provider_label }}</span>
                            </td>

                            <!-- Spesifikasi -->
                            <td class="px-5 py-3.5 text-slate-600">
                                <span class="font-medium text-slate-900">{{ $instance->cpu ?? 1 }} vCPU &bull; {{ $instance->ram ?? 1 }} GB RAM</span>
                                <span class="text-slate-400 text-[11px] block">{{ $instance->disk ?? 20 }} GB SSD NVMe</span>
                            </td>

                            <!-- Status -->
                            <td class="px-5 py-3.5">
                                <span class="px-2.5 py-0.5 rounded text-[11px] font-semibold uppercase border border-slate-300 bg-slate-50 text-slate-900 font-mono-code">
                                    {{ $instance->status }}
                                </span>
                            </td>

                            <!-- Ubah Status -->
                            <td class="px-5 py-3.5 text-right">
                                <form action="{{ route('admin.instances.status', $instance->id) }}" method="POST" class="inline-flex items-center gap-1.5">
                                    @csrf
                                    <select name="status" class="px-2.5 py-1.5 rounded border border-slate-300 text-xs font-medium bg-white focus:outline-none">
                                        <option value="running" {{ $instance->status === 'running' ? 'selected' : '' }}>Running</option>
                                        <option value="stopped" {{ $instance->status === 'stopped' ? 'selected' : '' }}>Stopped</option>
                                        <option value="provisioning" {{ $instance->status === 'provisioning' ? 'selected' : '' }}>Provisioning</option>
                                        <option value="error" {{ $instance->status === 'error' ? 'selected' : '' }}>Error</option>
                                        <option value="suspended" {{ $instance->status === 'suspended' ? 'selected' : '' }}>Suspended</option>
                                        <option value="terminated" {{ $instance->status === 'terminated' ? 'selected' : '' }}>Terminated</option>
                                    </select>
                                    <button type="submit" class="px-3 py-1.5 rounded bg-black text-white font-bold text-xs hover:bg-neutral-800 transition-colors">
                                        Simpan
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-10 text-center text-slate-400">
                                Tidak ada instance yang sesuai dengan kriteria filter saat ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($instances->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $instances->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
