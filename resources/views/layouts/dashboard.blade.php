<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Client Portal' }} — VexaHost</title>
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
    @include('partials.impersonation-banner')
    <div class="h-screen w-full flex flex-col md:flex-row overflow-hidden bg-white">

        <div x-show="sidebarOpen" class="fixed inset-0 z-40 bg-black/30 md:hidden" @click="sidebarOpen = false" style="display:none;"></div>

        <!-- Sidebar (Wide w-80, Stationary on Desktop, Border-r) -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
               class="fixed inset-y-0 left-0 z-50 w-80 bg-white border-r border-slate-200 flex flex-col transition-transform duration-200 md:static md:inset-auto md:h-full md:shrink-0 md:translate-x-0">

            <!-- Logo (No border-b) -->
            <div class="h-16 flex items-center justify-between px-6 pt-2 shrink-0">
                <a href="{{ route('dashboard.index') }}" class="flex items-center gap-3">
                    <img src="{{ asset('images/logo.png') }}" alt="VexaHost" class="h-10 w-auto object-contain shrink-0">
                    <span class="text-xl font-bold text-slate-900">VexaHost</span>
                </a>
                <button @click="sidebarOpen = false" class="md:hidden text-slate-400 hover:text-slate-900">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Nav Links (Large Font text-base / text-[16px], Generous padding) -->
            <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
                <a href="{{ route('dashboard.index') }}"
                   class="flex items-center gap-3.5 px-4 py-3 rounded-lg text-base transition-colors {{ request()->routeIs('dashboard.index') || request()->routeIs('dashboard.vps.*') ? 'bg-slate-100 text-slate-900 font-bold' : 'text-slate-700 hover:bg-slate-50 font-medium' }}">
                    <svg class="w-5 h-5 text-slate-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2"/></svg>
                    <span>VPS Instances</span>
                </a>

                <a href="{{ route('dashboard.billing') }}"
                   class="flex items-center gap-3.5 px-4 py-3 rounded-lg text-base transition-colors {{ request()->routeIs('dashboard.billing') ? 'bg-slate-100 text-slate-900 font-bold' : 'text-slate-700 hover:bg-slate-50 font-medium' }}">
                    <svg class="w-5 h-5 text-slate-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    <span>Tagihan & Invoice</span>
                </a>

                <a href="{{ route('dashboard.support') }}"
                   class="flex items-center justify-between px-4 py-3 rounded-lg text-base transition-colors {{ request()->routeIs('dashboard.support*') ? 'bg-slate-100 text-slate-900 font-bold' : 'text-slate-700 hover:bg-slate-50 font-medium' }}">
                    <div class="flex items-center gap-3.5">
                        <svg class="w-5 h-5 text-slate-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                        <span>Tiket Bantuan</span>
                    </div>
                    @php $userOpenTickets = $userOpenTickets ?? 0; @endphp
                    @if($userOpenTickets > 0)
                        <span class="text-xs font-bold bg-black text-white px-2 py-0.5 rounded">{{ $userOpenTickets }}</span>
                    @endif
                </a>

                <a href="{{ route('home') }}#pricing"
                   class="flex items-center justify-between px-4 py-3 rounded-lg text-base transition-colors text-slate-700 hover:bg-slate-50 font-medium group">
                    <div class="flex items-center gap-3.5">
                        <svg class="w-5 h-5 text-slate-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2"/></svg>
                        <span>Tambah VPS</span>
                    </div>
                    <svg class="w-4 h-4 text-slate-400 group-hover:text-slate-700 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-all shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 17L17 7M17 7H7M17 7V17"/></svg>
                </a>

                <a href="{{ route('home') }}#ai-packages"
                   class="flex items-center justify-between px-4 py-3 rounded-lg text-base transition-colors text-slate-700 hover:bg-slate-50 font-medium group">
                    <div class="flex items-center gap-3.5">
                        <svg class="w-5 h-5 text-slate-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <span>Tambah AI Agent</span>
                    </div>
                    <svg class="w-4 h-4 text-slate-400 group-hover:text-slate-700 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-all shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 17L17 7M17 7H7M17 7V17"/></svg>
                </a>

                <a href="{{ route('home') }}#database-packages"
                   class="flex items-center justify-between px-4 py-3 rounded-lg text-base transition-colors text-slate-700 hover:bg-slate-50 font-medium group">
                    <div class="flex items-center gap-3.5">
                        <svg class="w-5 h-5 text-slate-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/></svg>
                        <span>Tambah Database</span>
                    </div>
                    <svg class="w-4 h-4 text-slate-400 group-hover:text-slate-700 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-all shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 17L17 7M17 7H7M17 7V17"/></svg>
                </a>

                <a href="https://wa.flustra.id" target="_blank" rel="noopener noreferrer"
                   class="flex items-center justify-between px-4 py-3 rounded-lg text-base transition-colors text-slate-700 hover:bg-slate-50 font-medium group">
                    <div class="flex items-center gap-3.5">
                        <svg class="w-5 h-5 text-slate-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        <span>WA Gateway</span>
                    </div>
                    <svg class="w-4 h-4 text-slate-400 group-hover:text-slate-700 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-all shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 17L17 7M17 7H7M17 7V17"/></svg>
                </a>

                <a href="{{ route('dashboard.settings') }}"
                   class="flex items-center gap-3.5 px-4 py-3 rounded-lg text-base transition-colors {{ request()->routeIs('dashboard.settings') ? 'bg-slate-100 text-slate-900 font-bold' : 'text-slate-700 hover:bg-slate-50 font-medium' }}">
                    <svg class="w-5 h-5 text-slate-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span>Pengaturan Akun</span>
                </a>

                <a href="{{ route('organizations.index') }}"
                   class="flex items-center gap-3.5 px-4 py-3 rounded-lg text-base transition-colors {{ request()->routeIs('organizations.*') ? 'bg-slate-100 text-slate-900 font-bold' : 'text-slate-700 hover:bg-slate-50 font-medium' }}">
                    <svg class="w-5 h-5 text-slate-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5V4H2v16h5m10 0v-4H7v4m10 0H7m3-8h4m-6-4h8"/></svg>
                    <span>Organisasi</span>
                </a>

                <a href="{{ route('security.settings') }}"
                   class="flex items-center gap-3.5 px-4 py-3 rounded-lg text-base transition-colors {{ request()->routeIs('security.*') ? 'bg-slate-100 text-slate-900 font-bold' : 'text-slate-700 hover:bg-slate-50 font-medium' }}">
                    <svg class="w-5 h-5 text-slate-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    <span>Keamanan</span>
                </a>
            </nav>
        </aside>

        <!-- Main Content Area (No lines under headings, Independent Scroll) -->
        <div class="flex-1 flex flex-col min-w-0 h-full overflow-y-auto">
            <!-- Header with divider border -->
            <header class="h-16 shrink-0 bg-white border-b border-slate-200 flex items-center justify-between px-6 sm:px-10">
                <div class="flex items-center gap-3">
                    @if(isset($backUrl))
                        <a href="{{ $backUrl }}" class="p-1.5 -ml-1 text-slate-500 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition-colors flex items-center justify-center" title="{{ $backLabel ?? 'Kembali' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        </a>
                    @endif
                    <h1 class="text-base font-bold text-slate-900">{{ $headerTitle ?? 'Dashboard' }}</h1>
                </div>

                <div class="flex items-center gap-3">
                    <!-- User Profile Dropdown in Header -->
                    <div class="relative" @click.away="userDropdown = false">
                        <button @click="userDropdown = !userDropdown" class="flex items-center gap-1.5 p-1 rounded-lg hover:bg-slate-100 transition-colors focus:outline-none" title="{{ auth()->user()->full_name }}">
                            <div class="rounded-full bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600 shrink-0"
                                 style="width: 32px; height: 32px; min-width: 32px; min-height: 32px; border-radius: 9999px; aspect-ratio: 1 / 1;">
                                <svg class="text-blue-600 shrink-0" style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>

                        <div x-show="userDropdown" x-transition class="absolute right-0 mt-2 w-60 bg-white border border-slate-200 rounded-lg shadow-lg py-1.5 z-50 text-sm" style="display:none;">
                            <div class="px-4 py-3 border-b border-slate-100 flex items-center gap-3">
                                <div class="rounded-full bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600 shrink-0"
                                     style="width: 40px; height: 40px; min-width: 40px; min-height: 40px; border-radius: 9999px; aspect-ratio: 1 / 1;">
                                    <svg class="text-blue-600 shrink-0" style="width: 22px; height: 22px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="font-bold text-slate-900 truncate">{{ auth()->user()->full_name }}</p>
                                    <p class="text-xs text-slate-500 font-mono-code truncate">{{ auth()->user()->email }}</p>
                                </div>
                            </div>
                            <a href="{{ route('dashboard.settings') }}" class="block px-4 py-2 text-slate-700 hover:bg-slate-50">Pengaturan Akun</a>
                            @if(auth()->user()->is_admin)
                                <a href="{{ route('admin.index') }}" class="block px-4 py-2 text-slate-700 hover:bg-slate-50 font-medium">Panel Admin</a>
                            @endif
                            <a href="{{ route('home') }}" class="block px-4 py-2 text-slate-700 hover:bg-slate-50">Halaman Utama</a>
                            <div class="border-t border-slate-100 my-1"></div>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-slate-700 hover:bg-slate-50 font-medium">Keluar</button>
                            </form>
                        </div>
                    </div>

                    <!-- Mobile Hamburger Button on the Right -->
                    <button @click="sidebarOpen = true" class="md:hidden p-2 rounded-lg text-slate-700 hover:bg-slate-100 -mr-1.5" aria-label="Buka Menu">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
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
                @if(session('warning'))
                    <div x-data="{ show: true }"
                         x-show="show"
                         x-init="setTimeout(() => show = false, 5000)"
                         x-transition:leave="transition ease-in duration-300"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-2"
                         class="border border-amber-200 bg-amber-50 p-3 rounded-lg text-sm text-amber-800 mb-4 flex items-center justify-between shadow-xs">
                        <span>{{ session('warning') }}</span>
                        <button @click="show = false" class="text-amber-500 hover:text-amber-800 p-1 text-base leading-none">&times;</button>
                    </div>
                @endif
                @if(session('info'))
                    <div x-data="{ show: true }"
                         x-show="show"
                         x-init="setTimeout(() => show = false, 4000)"
                         x-transition:leave="transition ease-in duration-300"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-2"
                         class="border border-blue-200 bg-blue-50 p-3 rounded-lg text-sm text-blue-800 mb-4 flex items-center justify-between shadow-xs">
                        <span>{{ session('info') }}</span>
                        <button @click="show = false" class="text-blue-500 hover:text-blue-800 p-1 text-base leading-none">&times;</button>
                    </div>
                @endif
            </div>

            <!-- Main Page Body (No lines under headings) -->
            <main class="flex-1 px-6 sm:px-10 pb-12 pt-2">
                {{-- PHASE 1 - Spanduk pemberitahuan maintenance terjadwal.
                     Muncul otomatis H-notice_days sebelum jadwal berjalan. --}}
                @if(!empty($upcomingMaintenance) && count($upcomingMaintenance) > 0)
                    @foreach($upcomingMaintenance as $window)
                        <div class="bg-white p-4 rounded-lg border border-amber-200 mb-4">
                            <div class="flex items-start gap-3">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded text-[11px] font-semibold bg-amber-50 text-amber-800 border border-amber-200 shrink-0">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                                    Maintenance
                                </span>
                                <div class="min-w-0">
                                    <div class="text-sm font-bold text-slate-900">{{ $window->title }}</div>
                                    <p class="text-xs text-slate-600 mt-0.5 leading-relaxed">
                                        Terjadwal
                                        {{ $window->starts_at->timezone('Asia/Jakarta')->format('d M Y, H:i') }}
                                        &ndash;
                                        {{ $window->ends_at->timezone('Asia/Jakarta')->format('H:i') }} WIB.
                                        @if($window->description) {{ $window->description }} @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif

                @yield('content')
            </main>

            <!-- Footer (No border-t) -->
            <footer class="py-4 px-6 sm:px-10 text-xs text-slate-400 shrink-0">
                &copy; 2026 VexaHost
            </footer>
        </div>
    </div>
    @include('partials.invoice-modal')
    @stack('scripts')
</body>
</html>
