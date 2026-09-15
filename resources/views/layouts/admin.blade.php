<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Admin' }} — VexaHost</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-mono-code { font-family: 'JetBrains Mono', monospace; }
    </style>
</head>
<body class="h-full antialiased bg-white text-slate-900" x-data="{ sidebarOpen: false, userDropdown: false }">
    <div class="min-h-full flex flex-col md:flex-row">

        <div x-show="sidebarOpen" class="fixed inset-0 z-40 bg-black/30 md:hidden" @click="sidebarOpen = false" style="display:none;"></div>

        <!-- Sidebar (Wide w-80, Large Font text-base, No Category Headings) -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
               class="fixed inset-y-0 left-0 z-50 w-80 bg-white border-r border-slate-200 flex flex-col transition-transform duration-200 md:static md:translate-x-0">

            <!-- Logo (No border-b) -->
            <div class="h-16 flex items-center justify-between px-6 pt-2">
                <a href="{{ route('admin.index') }}" class="flex items-center gap-3">
                    <img src="{{ asset('images/logo.png') }}" alt="VexaHost" class="h-10 w-auto object-contain shrink-0">
                    <span class="text-xl font-bold text-slate-900">VexaHost</span>
                    <span class="px-2 py-0.5 rounded text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">Admin</span>
                </a>
                <button @click="sidebarOpen = false" class="md:hidden text-slate-400 hover:text-slate-900">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Nav Links (Large Font text-base, Generous padding, No Category Headings) -->
            <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
                <a href="{{ route('admin.index') }}"
                   class="flex items-center gap-3.5 px-4 py-3 rounded-lg text-base transition-colors {{ request()->routeIs('admin.index') ? 'bg-slate-100 text-slate-900 font-bold' : 'text-slate-700 hover:bg-slate-50 font-medium' }}">
                    <svg class="w-5 h-5 text-slate-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    <span>Ringkasan</span>
                </a>

                <a href="{{ route('admin.orders') }}"
                   class="flex items-center justify-between px-4 py-3 rounded-lg text-base transition-colors {{ request()->routeIs('admin.orders') ? 'bg-slate-100 text-slate-900 font-bold' : 'text-slate-700 hover:bg-slate-50 font-medium' }}">
                    <div class="flex items-center gap-3.5">
                        <svg class="w-5 h-5 text-slate-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        <span>Antrean Order</span>
                    </div>
                    @php $pendingCount = $adminPendingOrders ?? \App\Models\Order::where('status', 'pending')->count(); @endphp
                    @if($pendingCount > 0)
                        <span class="text-xs font-bold bg-black text-white px-2 py-0.5 rounded">{{ $pendingCount }}</span>
                    @endif
                </a>

                <a href="{{ route('admin.shopee') }}"
                   class="flex items-center gap-3.5 px-4 py-3 rounded-lg text-base transition-colors {{ request()->routeIs('admin.shopee') ? 'bg-slate-100 text-slate-900 font-bold' : 'text-slate-700 hover:bg-slate-50 font-medium' }}">
                    <svg class="w-5 h-5 text-slate-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    <span>Proses Shopee</span>
                </a>

                <a href="{{ route('admin.instances') }}"
                   class="flex items-center gap-3.5 px-4 py-3 rounded-lg text-base transition-colors {{ request()->routeIs('admin.instances') ? 'bg-slate-100 text-slate-900 font-bold' : 'text-slate-700 hover:bg-slate-50 font-medium' }}">
                    <svg class="w-5 h-5 text-slate-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2"/></svg>
                    <span>Cloud Instances</span>
                </a>

                <a href="{{ route('admin.packages.index') }}"
                   class="flex items-center gap-3.5 px-4 py-3 rounded-lg text-base transition-colors {{ request()->routeIs('admin.packages*') ? 'bg-slate-100 text-slate-900 font-bold' : 'text-slate-700 hover:bg-slate-50 font-medium' }}">
                    <svg class="w-5 h-5 text-slate-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    <span>Paket VPS</span>
                </a>

                <a href="{{ route('admin.monitoring') }}"
                   class="flex items-center gap-3.5 px-4 py-3 rounded-lg text-base transition-colors {{ request()->routeIs('admin.monitoring') ? 'bg-slate-100 text-slate-900 font-bold' : 'text-slate-700 hover:bg-slate-50 font-medium' }}">
                    <span class="w-5 h-5 rounded-full border-2 border-slate-700 flex items-center justify-center"><span class="w-1.5 h-1.5 rounded-full bg-slate-700"></span></span>
                    <span>Monitoring</span>
                </a>

                <a href="{{ route('admin.reports') }}"
                   class="flex items-center gap-3.5 px-4 py-3 rounded-lg text-base transition-colors {{ request()->routeIs('admin.reports*') ? 'bg-slate-100 text-slate-900 font-bold' : 'text-slate-700 hover:bg-slate-50 font-medium' }}">
                    <span class="w-5 h-5 flex items-end gap-0.5"><span class="w-1.5 h-2 bg-slate-700"></span><span class="w-1.5 h-3.5 bg-slate-700"></span><span class="w-1.5 h-5 bg-slate-700"></span></span>
                    <span>Reports</span>
                </a>

                <a href="{{ route('admin.customers') }}"
                   class="flex items-center gap-3.5 px-4 py-3 rounded-lg text-base transition-colors {{ request()->routeIs('admin.customers') ? 'bg-slate-100 text-slate-900 font-bold' : 'text-slate-700 hover:bg-slate-50 font-medium' }}">
                    <svg class="w-5 h-5 text-slate-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <span>Pelanggan</span>
                </a>

                <a href="{{ route('admin.tickets') }}"
                   class="flex items-center justify-between px-4 py-3 rounded-lg text-base transition-colors {{ request()->routeIs('admin.tickets*') ? 'bg-slate-100 text-slate-900 font-bold' : 'text-slate-700 hover:bg-slate-50 font-medium' }}">
                    <div class="flex items-center gap-3.5">
                        <svg class="w-5 h-5 text-slate-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                        <span>Tiket Bantuan</span>
                    </div>
                    @php $openTicketCount = $adminOpenTickets ?? \App\Models\SupportTicket::whereIn('status', ['open', 'in_progress'])->count(); @endphp
                    @if($openTicketCount > 0)
                        <span class="text-xs font-bold bg-black text-white px-2 py-0.5 rounded">{{ $openTicketCount }}</span>
                    @endif
                </a>
            </nav>
        </aside>

        <!-- Main Content (No border-b lines under titles) -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Header (NO border-b) -->
            <header class="h-16 bg-white flex items-center justify-between px-6 sm:px-10">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="md:hidden p-2 rounded-lg text-slate-700 hover:bg-slate-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <h1 class="text-base font-bold text-slate-900">{{ $headerTitle ?? 'Admin' }}</h1>
                </div>

                <div class="flex items-center gap-4">
                    <a href="{{ route('dashboard.index') }}" class="hidden sm:inline text-sm font-medium text-slate-500 hover:text-slate-900">Client Portal</a>

                    <div class="relative" @click.away="userDropdown = false">
                        <button @click="userDropdown = !userDropdown" class="flex items-center gap-2 focus:outline-none">
                            <div class="w-8 h-8 rounded-full bg-black text-white flex items-center justify-center text-xs font-bold">{{ strtoupper(substr(auth()->user()->full_name ?? auth()->user()->username ?? 'A', 0, 2)) }}</div>
                            <span class="hidden sm:block text-sm font-semibold text-slate-800">{{ auth()->user()->full_name ?? auth()->user()->username }}</span>
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>

                        <div x-show="userDropdown" x-transition class="absolute right-0 mt-2 w-52 bg-white border border-slate-200 rounded-lg shadow-lg py-1.5 z-50 text-sm" style="display:none;">
                            <div class="px-4 py-2 border-b border-slate-100">
                                <p class="font-bold text-slate-900">{{ auth()->user()->full_name ?? auth()->user()->username }} (Admin)</p>
                                <p class="text-xs text-slate-500 font-mono-code">{{ auth()->user()->email }}</p>
                            </div>
                            <a href="{{ route('dashboard.index') }}" class="block px-4 py-2 text-slate-700 hover:bg-slate-50">Client Portal</a>
                            <a href="{{ route('home') }}" class="block px-4 py-2 text-slate-700 hover:bg-slate-50">Halaman Utama</a>
                            <div class="border-t border-slate-100 my-1"></div>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-slate-700 hover:bg-slate-50 font-medium">Keluar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Alerts -->
            <div class="px-6 sm:px-10 pt-2">
                @if(session('success'))
                    <div x-data="{ show: true }"
                         x-show="show"
                         x-init="setTimeout(() => show = false, 4000)"
                         x-transition:leave="transition ease-in duration-300"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-2"
                         class="border border-emerald-200 bg-emerald-50 p-3 rounded-lg text-sm text-emerald-800 mb-4 flex items-center justify-between shadow-xs">
                        <span>{{ session('success') }}</span>
                        <button @click="show = false" class="text-emerald-500 hover:text-emerald-800 p-1 text-base leading-none">&times;</button>
                    </div>
                @endif
                @if(session('error'))
                    <div x-data="{ show: true }"
                         x-show="show"
                         x-init="setTimeout(() => show = false, 5000)"
                         x-transition:leave="transition ease-in duration-300"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-2"
                         class="border border-rose-200 bg-rose-50 p-3 rounded-lg text-sm text-rose-800 mb-4 flex items-center justify-between shadow-xs">
                        <span>{{ session('error') }}</span>
                        <button @click="show = false" class="text-rose-500 hover:text-rose-800 p-1 text-base leading-none">&times;</button>
                    </div>
                @endif
            </div>

            <!-- Main Page Body -->
            <main class="flex-1 px-6 sm:px-10 pb-12 pt-2">
                @yield('content')
            </main>

            <!-- Footer (No border-t) -->
            <footer class="py-4 px-6 sm:px-10 text-xs text-slate-400">
                &copy; 2026 VexaHost Admin
            </footer>
        </div>
    </div>
</body>
</html>
