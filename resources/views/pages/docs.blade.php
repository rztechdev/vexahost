@extends('layouts.app', ['title' => 'Dokumentasi Teknis & Panduan Arsitektur Cloud — VexaHost'])

@section('content')
<div x-data="{
    docsSearchOpen: false,
    searchQuery: '',
    copied: {},
    activeTabSnippet: {},
    activeSection: 'intro',
    copyToClipboard(text, id) {
        navigator.clipboard.writeText(text).then(() => {
            this.copied[id] = true;
            setTimeout(() => { this.copied[id] = false; }, 2000);
        });
    },
    sections: [
        { id: 'intro', title: 'Pengenalan Arsitektur Cloud', category: 'Mulai Cepat' },
        { id: 'provisioning', title: 'Alur Aktivasi Server', category: 'Mulai Cepat' },
        { id: 'ssh-access', title: 'Akses SSH & Manajemen Kredensial', category: 'Mulai Cepat' },
        { id: 'dual-provider', title: 'Arsitektur Dual-Provider (Tencent & Cloudeka)', category: 'Infrastruktur' },
        { id: 'datacenters', title: 'Lokasi Datacenter & Peering Jaringan', category: 'Infrastruktur' },
        { id: 'storage', title: 'Penyimpanan NVMe SSD', category: 'Infrastruktur' },
        { id: 'stack-coolify', title: 'Coolify Self-Hosted Platform', category: 'Control Panel' },
        { id: 'stack-dokploy', title: 'Dokploy Modern PaaS Deployment', category: 'Control Panel' },
        { id: 'stack-aapanel', title: 'aaPanel & Traditional Stack (LEMP/LAMP)', category: 'Control Panel' },
        { id: 'stack-docker', title: 'Docker Engine Standalone', category: 'Control Panel' },
        { id: 'security-ufw', title: 'Konfigurasi Firewall UFW & Port', category: 'Keamanan' },
        { id: 'security-ssh', title: 'Hardening SSH & Port Relocation', category: 'Keamanan' },
        { id: 'security-fail2ban', title: 'Fail2ban & Mitigasi Brute Force', category: 'Keamanan' },
        { id: 'ops-power', title: 'Restart & Server Tidak Merespons', category: 'Operasional' },
        { id: 'ops-reinstall', title: 'Reinstall Sistem Operasi', category: 'Operasional' },
        { id: 'ops-monitoring', title: 'Memantau Pemakaian Resource', category: 'Operasional' },
        { id: 'ops-billing', title: 'Siklus Tagihan & Masa Tenggang', category: 'Operasional' },
        { id: 'api-auth', title: 'Autentikasi & Security Header API', category: 'API Reference' },
        { id: 'api-vps-status', title: 'Endpoint Status Instance (GET)', category: 'API Reference' },
        { id: 'api-shopee-process', title: 'Endpoint Otomasi Shopee (POST)', category: 'API Reference' },
        { id: 'api-errors', title: 'Format Respon & Standar Kode Error', category: 'API Reference' }
    ],
    get filteredSections() {
        if (!this.searchQuery.trim()) return this.sections;
        const q = this.searchQuery.toLowerCase();
        return this.sections.filter(s => s.title.toLowerCase().includes(q) || s.category.toLowerCase().includes(q));
    },
    scrollTo(id) {
        this.docsSearchOpen = false;
        const el = document.getElementById(id);
        if (el) {
            el.scrollIntoView({ behavior: 'smooth' });
            this.activeSection = id;
        }
    }
}"
@keydown.window.prevent.cmd.k="docsSearchOpen = true; $nextTick(() => $refs.searchInput.focus())"
@keydown.window.prevent.ctrl.k="docsSearchOpen = true; $nextTick(() => $refs.searchInput.focus())"
class="min-h-screen bg-slate-50 selection:bg-slate-900 selection:text-white">

    <!-- Top Sub-Header Bar (Enterprise Documentation Banner) -->
    <div class="bg-white border-b border-slate-200 sticky top-16 z-30 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-14 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="flex items-center space-x-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Dokumentasi Teknis</span>
                    <span class="text-slate-300">/</span>
                    <span class="text-xs font-semibold text-slate-900 font-mono-code">v1.4 Enterprise</span>
                </div>
                <a href="{{ route('status') }}" class="hidden sm:inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-50 text-slate-700 border border-slate-200 hover:bg-slate-100">
                    Cek Status Layanan &rarr;
                </a>
            </div>

            <!-- Quick Search Input Trigger -->
            <div class="flex items-center space-x-3">
                <button @click="docsSearchOpen = true; $nextTick(() => $refs.searchInput.focus())"
                        type="button"
                        class="flex items-center gap-2.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-500 hover:text-slate-800 text-xs transition-colors shadow-2xs group">
                    <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <span class="hidden sm:inline">Cari dokumentasi atau endpoint API...</span>
                    <span class="sm:hidden">Cari...</span>
                    <kbd class="hidden sm:inline-flex items-center gap-0.5 font-mono-code text-[10px] font-semibold bg-white px-1.5 py-0.5 rounded border border-slate-200 text-slate-600 shadow-2xs">Ctrl K</kbd>
                </button>

                <a href="{{ route('dashboard.index') }}" class="hidden md:inline-flex items-center gap-1 text-xs font-semibold text-slate-700 hover:text-black px-3 py-1.5 rounded-lg hover:bg-slate-100 transition-colors">
                    <span>Client Portal</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>
    </div>

    <!-- Search Modal (Cmd+K / Ctrl+K) -->
    <div x-show="docsSearchOpen"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 backdrop-blur-xs flex items-start justify-center pt-20 px-4"
         style="display: none;"
         @keydown.escape.window="docsSearchOpen = false">

        <div @click.away="docsSearchOpen = false"
             class="bg-white rounded-xl border border-slate-200 shadow-2xl max-w-2xl w-full overflow-hidden">
            <div class="p-4 border-b border-slate-200 flex items-center gap-3">
                <svg class="w-5 h-5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input x-ref="searchInput"
                       x-model="searchQuery"
                       type="text"
                       placeholder="Ketik topik, perintah SSH, spesifikasi, atau endpoint API..."
                       class="w-full text-sm text-slate-900 placeholder-slate-400 bg-transparent focus:outline-none">
                <button @click="docsSearchOpen = false" class="text-xs font-semibold text-slate-400 hover:text-slate-600 px-2 py-1 rounded bg-slate-100">ESC</button>
            </div>

            <div class="max-h-96 overflow-y-auto p-2 divide-y divide-slate-100">
                <template x-for="item in filteredSections" :key="item.id">
                    <button @click="scrollTo(item.id)"
                            class="w-full text-left p-3 hover:bg-slate-50 rounded-lg flex items-center justify-between group transition-colors">
                        <div>
                            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block font-mono-code" x-text="item.category"></span>
                            <span class="text-sm font-semibold text-slate-900 group-hover:text-black" x-text="item.title"></span>
                        </div>
                        <svg class="w-4 h-4 text-slate-300 group-hover:text-slate-700 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </template>
                <div x-show="filteredSections.length === 0" class="p-8 text-center text-xs text-slate-400">
                    Tidak ada topik dokumentasi yang cocok dengan kata kunci tersebut.
                </div>
            </div>

            <div class="px-4 py-2.5 bg-slate-50 border-t border-slate-200 text-[11px] text-slate-500 flex items-center justify-between">
                <span>Navigasi cepat dokumentasi VexaHost Cloud</span>
                <span class="font-mono-code text-[10px]">Gunakan panah & enter untuk membuka</span>
            </div>
        </div>
    </div>

    <!-- Main Container Layout (3 Columns: Left Sidebar, Main Docs Content, Right TOC) -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            <!-- LEFT SIDEBAR: Navigasi Kategori (Sticky) -->
            <aside class="lg:col-span-3 sticky top-32 max-h-[calc(100vh-9rem)] overflow-y-auto pr-2 space-y-6 text-xs">
                
                <!-- Category 1: Mulai Cepat -->
                <div>
                    <div class="flex items-center gap-2 font-bold uppercase tracking-wider text-slate-400 mb-2 px-3">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <span>Mulai Cepat & Orientasi</span>
                    </div>
                    <ul class="space-y-0.5">
                        <li>
                            <a href="#intro" @click="activeSection = 'intro'"
                               :class="activeSection === 'intro' ? 'bg-slate-200/70 text-slate-900 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 font-medium'"
                               class="block px-3 py-2 rounded-lg transition-colors">
                                Pengenalan Arsitektur Cloud
                            </a>
                        </li>
                        <li>
                            <a href="#provisioning" @click="activeSection = 'provisioning'"
                               :class="activeSection === 'provisioning' ? 'bg-slate-200/70 text-slate-900 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 font-medium'"
                               class="block px-3 py-2 rounded-lg transition-colors">
                                Alur Aktivasi Server
                            </a>
                        </li>
                        <li>
                            <a href="#ssh-access" @click="activeSection = 'ssh-access'"
                               :class="activeSection === 'ssh-access' ? 'bg-slate-200/70 text-slate-900 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 font-medium'"
                               class="block px-3 py-2 rounded-lg transition-colors">
                                Akses SSH & Kredensial
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Category 2: Infrastruktur Cloud & Jaringan -->
                <div>
                    <div class="flex items-center gap-2 font-bold uppercase tracking-wider text-slate-400 mb-2 px-3">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        <span>Infrastruktur & Jaringan</span>
                    </div>
                    <ul class="space-y-0.5">
                        <li>
                            <a href="#dual-provider" @click="activeSection = 'dual-provider'"
                               :class="activeSection === 'dual-provider' ? 'bg-slate-200/70 text-slate-900 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 font-medium'"
                               class="block px-3 py-2 rounded-lg transition-colors">
                                Dual-Provider (Tencent & Cloudeka)
                            </a>
                        </li>
                        <li>
                            <a href="#datacenters" @click="activeSection = 'datacenters'"
                               :class="activeSection === 'datacenters' ? 'bg-slate-200/70 text-slate-900 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 font-medium'"
                               class="block px-3 py-2 rounded-lg transition-colors">
                                Datacenter Jakarta & Singapore
                            </a>
                        </li>
                        <li>
                            <a href="#storage" @click="activeSection = 'storage'"
                               :class="activeSection === 'storage' ? 'bg-slate-200/70 text-slate-900 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 font-medium'"
                               class="block px-3 py-2 rounded-lg transition-colors">
                                Penyimpanan NVMe SSD
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Category 3: Control Panel & Stacks -->
                <div>
                    <div class="flex items-center gap-2 font-bold uppercase tracking-wider text-slate-400 mb-2 px-3">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                        <span>Control Panel & Stacks</span>
                    </div>
                    <ul class="space-y-0.5">
                        <li>
                            <a href="#stack-coolify" @click="activeSection = 'stack-coolify'"
                               :class="activeSection === 'stack-coolify' ? 'bg-slate-200/70 text-slate-900 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 font-medium'"
                               class="block px-3 py-2 rounded-lg transition-colors">
                                Coolify Platform Deployment
                            </a>
                        </li>
                        <li>
                            <a href="#stack-dokploy" @click="activeSection = 'stack-dokploy'"
                               :class="activeSection === 'stack-dokploy' ? 'bg-slate-200/70 text-slate-900 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 font-medium'"
                               class="block px-3 py-2 rounded-lg transition-colors">
                                Dokploy Modern PaaS
                            </a>
                        </li>
                        <li>
                            <a href="#stack-aapanel" @click="activeSection = 'stack-aapanel'"
                               :class="activeSection === 'stack-aapanel' ? 'bg-slate-200/70 text-slate-900 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 font-medium'"
                               class="block px-3 py-2 rounded-lg transition-colors">
                                aaPanel Tradisional (LEMP/LAMP)
                            </a>
                        </li>
                        <li>
                            <a href="#stack-docker" @click="activeSection = 'stack-docker'"
                               :class="activeSection === 'stack-docker' ? 'bg-slate-200/70 text-slate-900 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 font-medium'"
                               class="block px-3 py-2 rounded-lg transition-colors">
                                Docker Engine Standalone
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Category 4: Keamanan Server -->
                <div>
                    <div class="flex items-center gap-2 font-bold uppercase tracking-wider text-slate-400 mb-2 px-3">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        <span>Keamanan & Hardening</span>
                    </div>
                    <ul class="space-y-0.5">
                        <li>
                            <a href="#security-ufw" @click="activeSection = 'security-ufw'"
                               :class="activeSection === 'security-ufw' ? 'bg-slate-200/70 text-slate-900 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 font-medium'"
                               class="block px-3 py-2 rounded-lg transition-colors">
                                Firewall UFW & Port Policy
                            </a>
                        </li>
                        <li>
                            <a href="#security-ssh" @click="activeSection = 'security-ssh'"
                               :class="activeSection === 'security-ssh' ? 'bg-slate-200/70 text-slate-900 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 font-medium'"
                               class="block px-3 py-2 rounded-lg transition-colors">
                                Hardening SSH (Key-Only Auth)
                            </a>
                        </li>
                        <li>
                            <a href="#security-fail2ban" @click="activeSection = 'security-fail2ban'"
                               :class="activeSection === 'security-fail2ban' ? 'bg-slate-200/70 text-slate-900 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 font-medium'"
                               class="block px-3 py-2 rounded-lg transition-colors">
                                Fail2ban & Proteksi Brute Force
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Category 5: Operasional VPS -->
                <div>
                    <div class="flex items-center gap-2 font-bold uppercase tracking-wider text-slate-400 mb-2 px-3">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/></svg>
                        <span>Operasional Siklus Hidup</span>
                    </div>
                    <ul class="space-y-0.5">
                        <li>
                            <a href="#ops-power" @click="activeSection = 'ops-power'"
                               :class="activeSection === 'ops-power' ? 'bg-slate-200/70 text-slate-900 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 font-medium'"
                               class="block px-3 py-2 rounded-lg transition-colors">
                                Restart & Server Tidak Merespons
                            </a>
                        </li>
                        <li>
                            <a href="#ops-reinstall" @click="activeSection = 'ops-reinstall'"
                               :class="activeSection === 'ops-reinstall' ? 'bg-slate-200/70 text-slate-900 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 font-medium'"
                               class="block px-3 py-2 rounded-lg transition-colors">
                                Reinstall Sistem Operasi
                            </a>
                        </li>
                        <li>
                            <a href="#ops-monitoring" @click="activeSection = 'ops-monitoring'"
                               :class="activeSection === 'ops-monitoring' ? 'bg-slate-200/70 text-slate-900 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 font-medium'"
                               class="block px-3 py-2 rounded-lg transition-colors">
                                Memantau Pemakaian Resource
                            </a>
                        </li>
                        <li>
                            <a href="#ops-billing" @click="activeSection = 'ops-billing'"
                               :class="activeSection === 'ops-billing' ? 'bg-slate-200/70 text-slate-900 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 font-medium'"
                               class="block px-3 py-2 rounded-lg transition-colors">
                                Siklus Tagihan & Masa Tenggang
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Category 6: API Reference & Integrasi Developer -->
                <div>
                    <div class="flex items-center gap-2 font-bold uppercase tracking-wider text-slate-400 mb-2 px-3">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                        <span>Referensi API & Integrasi</span>
                    </div>
                    <ul class="space-y-0.5">
                        <li>
                            <a href="#api-auth" @click="activeSection = 'api-auth'"
                               :class="activeSection === 'api-auth' ? 'bg-slate-200/70 text-slate-900 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 font-medium'"
                               class="block px-3 py-2 rounded-lg transition-colors">
                                Autentikasi & Security Header
                            </a>
                        </li>
                        <li>
                            <a href="#api-vps-status" @click="activeSection = 'api-vps-status'"
                               :class="activeSection === 'api-vps-status' ? 'bg-slate-200/70 text-slate-900 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 font-medium'"
                               class="flex items-center justify-between px-3 py-2 rounded-lg transition-colors">
                                <span>GET Status Instance</span>
                                <span class="px-1.5 py-0.2 rounded text-[9px] font-bold font-mono-code bg-emerald-100 text-emerald-800">GET</span>
                            </a>
                        </li>
                        <li>
                            <a href="#api-shopee-process" @click="activeSection = 'api-shopee-process'"
                               :class="activeSection === 'api-shopee-process' ? 'bg-slate-200/70 text-slate-900 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 font-medium'"
                               class="flex items-center justify-between px-3 py-2 rounded-lg transition-colors">
                                <span>POST Otomasi Shopee</span>
                                <span class="px-1.5 py-0.2 rounded text-[9px] font-bold font-mono-code bg-indigo-100 text-indigo-800">POST</span>
                            </a>
                        </li>
                        <li>
                            <a href="#api-errors" @click="activeSection = 'api-errors'"
                               :class="activeSection === 'api-errors' ? 'bg-slate-200/70 text-slate-900 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 font-medium'"
                               class="block px-3 py-2 rounded-lg transition-colors">
                                Standar Error & HTTP Codes
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Support Ticket Widget in Sidebar -->
                <div class="p-4 rounded-xl border border-slate-200 bg-white">
                    <p class="font-bold text-slate-900 text-xs mb-1">Butuh Bantuan Teknis?</p>
                    <p class="text-slate-500 text-[11px] leading-relaxed mb-3">Tim VexaHost membantu kendala server Anda pada jam kerja.</p>
                    <a href="{{ route('dashboard.support') }}" class="block text-center py-2 px-3 rounded-lg bg-slate-900 text-white font-bold text-[11px] hover:bg-black transition-colors">
                        Buka Tiket Dukungan
                    </a>
                </div>

            </aside>

            <!-- CENTER COLUMN: Konten Dokumentasi Teknis Utama -->
            <main class="lg:col-span-6 space-y-16">

                <!-- SECTION 1: MULAI CEPAT & ARSITEKTUR -->
                <section id="intro" class="scroll-mt-36 space-y-4">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded text-[10px] font-bold font-mono-code bg-slate-900 text-white uppercase">Modul 01</span>
                        <span class="text-xs font-semibold text-slate-500">Mulai Cepat</span>
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                        Pengenalan Arsitektur Cloud VexaHost
                    </h2>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        VexaHost adalah platform komputasi awan berbasis KVM (Kernel-based Virtual Machine) yang didesain secara spesifik untuk beban kerja modern developer, startup SaaS, automasi bot, dan hosting mandiri berkecepatan tinggi. Setiap VPS mendapat alokasi vCPU, RAM, dan penyimpanan NVMe sesuai paket yang dipilih, lengkap dengan akses root penuh.
                    </p>

                    <!-- Alert Note Box -->
                    <div class="p-4 rounded-lg bg-blue-50/80 border-l-4 border-[#4A6FA5] text-xs text-slate-700 flex items-start gap-3">
                        <svg class="w-5 h-5 text-[#4A6FA5] shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div class="space-y-1">
                            <span class="font-bold text-slate-900 block">Isolasi Kernel Penuh (Hardware Virtualization)</span>
                            <span>Seluruh paket VPS VexaHost menggunakan KVM murni sehingga Anda memiliki akses kernel mandiri penuh, modul sistem kustom, kemampuan swap memory, hingga dukungan instalasi Docker dan containerd tanpa batasan containerisasi OS.</span>
                        </div>
                    </div>
                </section>

                <hr class="border-slate-200">

                <!-- SECTION: ALUR AKTIVASI SERVER -->
                <section id="provisioning" class="scroll-mt-36 space-y-4">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded text-[10px] font-bold font-mono-code bg-slate-900 text-white uppercase">Alur Kerja</span>
                        <span class="text-xs font-semibold text-slate-500">Aktivasi Server</span>
                    </div>
                    <h2 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                        Alur Aktivasi Server
                    </h2>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        Setiap server disiapkan langsung oleh tim VexaHost setelah pembayaran terkonfirmasi. Urutannya sebagai berikut:
                    </p>

                    <div class="space-y-3 font-mono-code text-xs">
                        <div class="p-3.5 rounded-lg border border-slate-200 bg-white flex items-start gap-3">
                            <span class="w-6 h-6 rounded bg-slate-100 text-slate-700 font-bold flex items-center justify-center shrink-0">1</span>
                            <div>
                                <span class="font-bold text-slate-900 block font-sans text-xs">Penerimaan Pesanan & Validasi Pembayaran</span>
                                <span class="text-slate-500 font-sans text-[11px]">Invoice diterbitkan. Setelah pembayaran terkonfirmasi (otomatis lewat gateway atau diverifikasi tim), status pesanan berubah menjadi <code class="text-emerald-700 bg-emerald-50 px-1 rounded">paid</code>.</span>
                            </div>
                        </div>

                        <div class="p-3.5 rounded-lg border border-slate-200 bg-white flex items-start gap-3">
                            <span class="w-6 h-6 rounded bg-slate-100 text-slate-700 font-bold flex items-center justify-center shrink-0">2</span>
                            <div>
                                <span class="font-bold text-slate-900 block font-sans text-xs">Server Disiapkan Tim</span>
                                <span class="text-slate-500 font-sans text-[11px]">Tim VexaHost menyiapkan server di infrastruktur penyedia (Tencent Cloud atau Lintasarta Cloudeka) sesuai paket dan lokasi yang Anda pilih. Pesanan tampil sebagai "sedang disiapkan" di dashboard.</span>
                            </div>
                        </div>

                        <div class="p-3.5 rounded-lg border border-slate-200 bg-white flex items-start gap-3">
                            <span class="w-6 h-6 rounded bg-slate-100 text-slate-700 font-bold flex items-center justify-center shrink-0">3</span>
                            <div>
                                <span class="font-bold text-slate-900 block font-sans text-xs">Pemasangan OS & Stack</span>
                                <span class="text-slate-500 font-sans text-[11px]">Sistem operasi pilihan Anda dipasang. Jika memilih panel seperti Coolify atau Dokploy, tim memasangnya lalu mencantumkan alamat panelnya di dashboard.</span>
                            </div>
                        </div>

                        <div class="p-3.5 rounded-lg border border-slate-200 bg-white flex items-start gap-3">
                            <span class="w-6 h-6 rounded bg-slate-100 text-slate-700 font-bold flex items-center justify-center shrink-0">4</span>
                            <div>
                                <span class="font-bold text-slate-900 block font-sans text-xs">Serah Terima di Dashboard</span>
                                <span class="text-slate-500 font-sans text-[11px]">IP publik, port SSH, dan password root (tersimpan terenkripsi) tampil di Client Portal, dan Anda menerima email pemberitahuan. Status server berubah menjadi <code class="text-emerald-700 bg-emerald-50 px-1 rounded">Aktif</code>.</span>
                            </div>
                        </div>
                    </div>
                </section>

                <hr class="border-slate-200">

                <!-- SECTION: AKSES SSH & KREDENSIAL -->
                <section id="ssh-access" class="scroll-mt-36 space-y-4">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded text-[10px] font-bold font-mono-code bg-slate-900 text-white uppercase">Akses Remote</span>
                        <span class="text-xs font-semibold text-slate-500">Terminal Shell</span>
                    </div>
                    <h2 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                        Akses SSH & Manajemen Kredensial
                    </h2>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        Anda mendapatkan hak akses root penuh (<code class="font-mono-code text-xs bg-slate-100 px-1.5 py-0.5 rounded">sudo / root</code>) melalui Secure Shell (SSH) protokol versi 2. Kredensial awal diberikan pada dashboard pelanggan.
                    </p>

                    <!-- Code Snippet Box with Copy Button -->
                    <div class="rounded-xl border border-slate-800 bg-slate-950 overflow-hidden text-xs">
                        <div class="bg-slate-900 px-4 py-2.5 border-b border-slate-800 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-rose-500/80"></span>
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-500/80"></span>
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500/80"></span>
                                <span class="text-[11px] font-mono-code text-slate-400 ml-2">bash &bull; terminal</span>
                            </div>
                            <button @click="copyToClipboard('ssh root@103.xxx.xxx.xxx', 'ssh-login')"
                                    class="text-[11px] font-semibold text-slate-400 hover:text-white flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                <span x-text="copied['ssh-login'] ? 'Tersalin!' : 'Salin'"></span>
                            </button>
                        </div>
                        <div class="p-4 font-mono-code text-slate-300 leading-relaxed overflow-x-auto">
                            <p class="text-slate-500"># Login standar menggunakan password root awal</p>
                            <p><span class="text-emerald-400">$</span> ssh root@103.xxx.xxx.xxx</p>
                            <br>
                            <p class="text-slate-500"># Jika Anda menggunakan private key (.pem / .id_ed25519)</p>
                            <p><span class="text-emerald-400">$</span> ssh -i ~/.ssh/id_ed25519 root@103.xxx.xxx.xxx</p>
                        </div>
                    </div>
                </section>

                <hr class="border-slate-200">

                <!-- SECTION 2: DUAL-PROVIDER ARCHITECTURE -->
                <section id="dual-provider" class="scroll-mt-36 space-y-4">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded text-[10px] font-bold font-mono-code bg-slate-900 text-white uppercase">Modul 02</span>
                        <span class="text-xs font-semibold text-slate-500">Infrastruktur Jaringan</span>
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                        Arsitektur Dual-Provider (Tencent & Cloudeka)
                    </h2>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        Server VexaHost berjalan di atas dua penyedia infrastruktur: <strong>Tencent Cloud</strong> dan <strong>Lintasarta Cloudeka</strong>. Setiap paket terhubung ke salah satu penyedia sesuai profil kebutuhan aplikasi Anda.
                    </p>

                    <!-- Provider Comparison Table -->
                    <div class="border border-slate-200 rounded-xl overflow-hidden bg-white text-xs">
                        <table class="w-full text-left">
                            <thead class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-200">
                                <tr>
                                    <th class="px-4 py-3">Parameter Evaluasi</th>
                                    <th class="px-4 py-3">Tencent Cloud Tier</th>
                                    <th class="px-4 py-3">Lintasarta Cloudeka Tier</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr>
                                    <td class="px-4 py-3 font-semibold text-slate-900">Katalog Paket Terkait</td>
                                    <td class="px-4 py-3 text-slate-700">Mahasiswa Basic, Standard, Premium</td>
                                    <td class="px-4 py-3 text-slate-700">Student Basic, Startup, Business</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 font-semibold text-slate-900">Lokasi Datacenter</td>
                                    <td class="px-4 py-3 text-slate-700">Singapore (ap-singapore) & Jakarta (ap-jakarta)</td>
                                    <td class="px-4 py-3 text-slate-700">Jakarta</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 font-semibold text-slate-900">Throughput Jaringan</td>
                                    <td class="px-4 py-3 text-slate-700">Hingga 30 Mbps burstable internasional</td>
                                    <td class="px-4 py-3 text-slate-700">Hingga 1 Gbps unmetered lokal exchange</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 font-semibold text-slate-900">Skenario Terbaik</td>
                                    <td class="px-4 py-3 text-slate-700">Bot Discord, integrasi API global, server staging</td>
                                    <td class="px-4 py-3 text-slate-700">E-Commerce Indonesia, API Payment, Web Sekolah</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <hr class="border-slate-200">

                <!-- SECTION: DATACENTER & STORAGE -->
                <section id="datacenters" class="scroll-mt-36 space-y-4">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded text-[10px] font-bold font-mono-code bg-slate-900 text-white uppercase">Fasilitas</span>
                        <span class="text-xs font-semibold text-slate-500">Datacenter & Peering</span>
                    </div>
                    <h2 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                        Lokasi Datacenter & Peering Jaringan
                    </h2>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        Infrastruktur VexaHost terhubung langsung dengan simpul interkoneksi utama di Indonesia dan Asia Tenggara, meliputi:
                    </p>
                    <ul class="space-y-2 text-xs text-slate-700">
                        <li class="flex items-start gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-900 mt-1.5 shrink-0"></span>
                            <span><strong>Indonesia Internet Exchange (IIX - APJII):</strong> Interkoneksi langsung ke seluruh ISP nasional (Telkom IndiHome, Biznet, First Media, MyRepublic, XL, Indosat).</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-900 mt-1.5 shrink-0"></span>
                            <span><strong>OpenIXP IDC 3D Kuningan:</strong> Rute pertukaran paket data independen berkapasitas ratusan gigabit per detik.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-900 mt-1.5 shrink-0"></span>
                            <span><strong>Equinix SG1 / Global Transit:</strong> Jalur kabel laut redundan menghubungkan node Singapore langsung ke backhaul global Tier-1.</span>
                        </li>
                    </ul>
                </section>

                <section id="storage" class="scroll-mt-36 space-y-4">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded text-[10px] font-bold font-mono-code bg-slate-900 text-white uppercase">Hardware</span>
                        <span class="text-xs font-semibold text-slate-500">Storage Subsystem</span>
                    </div>
                    <h2 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                        Penyimpanan NVMe SSD
                    </h2>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        Disk VPS berjalan di atas penyimpanan SSD dari penyedia infrastruktur (Tencent Cloud atau Lintasarta Cloudeka). Kapasitas disk mengikuti paket yang Anda pilih dan tercantum di dashboard.
                    </p>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        Pencadangan data merupakan tanggung jawab Anda sebagai pemilik server. Simpan salinan data penting di luar server, terutama sebelum mengajukan reinstall OS.
                    </p>
                </section>

                <hr class="border-slate-200">

                <!-- SECTION 3: CONTROL PANELS & APP STACKS -->
                <section id="stack-coolify" class="scroll-mt-36 space-y-4">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded text-[10px] font-bold font-mono-code bg-slate-900 text-white uppercase">Modul 03</span>
                        <span class="text-xs font-semibold text-slate-500">Control Panel</span>
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                        Coolify Platform Deployment
                    </h2>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        <strong>Coolify</strong> adalah alternatif self-hosted paling populer untuk Heroku, Netlify, dan Vercel. Ketika Anda memilih stack Coolify pada saat checkout, skrip inisialisasi VexaHost otomatis mengonfigurasi Docker Engine, Traefik reverse proxy, dan instans Coolify terbaru.
                    </p>

                    <div class="p-4 rounded-xl border border-slate-200 bg-white space-y-3 text-xs">
                        <span class="font-bold text-slate-900 block">Langkah Akses Coolify Pertama Kali:</span>
                        <ol class="list-decimal list-inside space-y-1.5 text-slate-600">
                            <li>Buka peramban web dan tuju URL: <code class="font-mono-code bg-slate-100 px-1 py-0.5 rounded text-slate-900">http://IP_VPS_ANDA:8000</code>.</li>
                            <li>Lengkapi formulir pendaftaran akun administrator master (Root Admin).</li>
                            <li>Tambahkan Git Repository (GitHub, GitLab, atau Git kustom) untuk mengaktifkan otomatisasi CI/CD zero-downtime.</li>
                            <li>Konfigurasikan Domain Publik di menu Server Settings dengan mengarahkan DNS A Record ke IP VPS VexaHost Anda.</li>
                        </ol>
                    </div>

                    <!-- Terminal Code Snippet for Manual Installation -->
                    <div class="rounded-xl border border-slate-800 bg-slate-950 overflow-hidden text-xs">
                        <div class="bg-slate-900 px-4 py-2 border-b border-slate-800 flex items-center justify-between">
                            <span class="text-slate-400 font-mono-code text-[11px]">Instalasi Manual Coolify (Jika Memilih Clean OS)</span>
                            <button @click="copyToClipboard('curl -fsSL https://cdn.coollabs.io/coolify/install.sh | bash', 'coolify-install')"
                                    class="text-[11px] font-semibold text-slate-400 hover:text-white flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                <span x-text="copied['coolify-install'] ? 'Tersalin!' : 'Salin'"></span>
                            </button>
                        </div>
                        <div class="p-4 font-mono-code text-slate-300">
                            <span class="text-emerald-400">$</span> curl -fsSL https://cdn.coollabs.io/coolify/install.sh | bash
                        </div>
                    </div>
                </section>

                <section id="stack-dokploy" class="scroll-mt-36 space-y-4">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded text-[10px] font-bold font-mono-code bg-slate-900 text-white uppercase">App Platform</span>
                        <span class="text-xs font-semibold text-slate-500">Modern PaaS</span>
                    </div>
                    <h2 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                        Dokploy Modern PaaS Deployment
                    </h2>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        <strong>Dokploy</strong> dirancang untuk kemudahan manajemen aplikasi microservices berbasis container, database cluster (PostgreSQL, MySQL, Redis, MongoDB), serta integrasi Docker Compose berlapis dengan dashboard visual yang minimalis dan sangat hemat sumber daya RAM.
                    </p>

                    <div class="rounded-xl border border-slate-800 bg-slate-950 overflow-hidden text-xs">
                        <div class="bg-slate-900 px-4 py-2 border-b border-slate-800 flex items-center justify-between">
                            <span class="text-slate-400 font-mono-code text-[11px]">Setup Dokploy (Port 3000)</span>
                            <button @click="copyToClipboard('curl -sSL https://dokploy.com/setup.sh | sh', 'dokploy-install')"
                                    class="text-[11px] font-semibold text-slate-400 hover:text-white flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                <span x-text="copied['dokploy-install'] ? 'Tersalin!' : 'Salin'"></span>
                            </button>
                        </div>
                        <div class="p-4 font-mono-code text-slate-300">
                            <span class="text-emerald-400">$</span> curl -sSL https://dokploy.com/setup.sh | sh
                        </div>
                    </div>
                </section>

                <section id="stack-aapanel" class="scroll-mt-36 space-y-4">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded text-[10px] font-bold font-mono-code bg-slate-900 text-white uppercase">Web Hosting</span>
                        <span class="text-xs font-semibold text-slate-500">LEMP & LAMP Stack</span>
                    </div>
                    <h2 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                        aaPanel & Stack Tradisional
                    </h2>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        Jika alur kerja Anda mengutamakan web server tradisional dengan banyak website PHP (WordPress, Laravel, CodeIgniter), <strong>aaPanel</strong> menyediakan manajemen grafis untuk Nginx, Apache, MySQL, PHP Multi-version (7.4, 8.0, 8.1, 8.2, 8.3), phpMyAdmin, dan SSL Let's Encrypt 1-klik.
                    </p>
                    <p class="text-xs text-slate-500">
                        Port default akses web aaPanel adalah <code class="font-mono-code text-slate-800 bg-slate-100 px-1 py-0.5 rounded">:8888</code> atau safety path acak yang dicetak saat provisioning selesai.
                    </p>
                </section>

                <section id="stack-docker" class="scroll-mt-36 space-y-4">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded text-[10px] font-bold font-mono-code bg-slate-900 text-white uppercase">Container Runtime</span>
                        <span class="text-xs font-semibold text-slate-500">Pure CLI</span>
                    </div>
                    <h2 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                        Docker Engine Standalone
                    </h2>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        Untuk beban kerja produksi yang tidak membutuhkan GUI web panel sama sekali demi menghemat 100% alokasi memory untuk aplikasi, opsi <em>Docker Standalone</em> mengonfigurasi official Docker Engine CE, containerd, dan Docker Compose v2 CLI siap pakai.
                    </p>
                </section>

                <hr class="border-slate-200">

                <!-- SECTION 4: KEAMANAN & HARDENING -->
                <section id="security-ufw" class="scroll-mt-36 space-y-4">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded text-[10px] font-bold font-mono-code bg-slate-900 text-white uppercase">Modul 04</span>
                        <span class="text-xs font-semibold text-slate-500">Keamanan Server</span>
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                        Firewall UFW & Port Security Policy
                    </h2>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        Secara default pada instalasi Ubuntu dan Debian, Uncomplicated Firewall (UFW) dalam kondisi tidak aktif. Sangat direkomendasikan untuk mengaktifkannya dengan aturan akses minimal (<em>Principle of Least Privilege</em>):
                    </p>

                    <div class="rounded-xl border border-slate-800 bg-slate-950 overflow-hidden text-xs">
                        <div class="bg-slate-900 px-4 py-2 border-b border-slate-800 flex items-center justify-between">
                            <span class="text-slate-400 font-mono-code text-[11px]">Konfigurasi Standar Firewall UFW</span>
                            <button @click="copyToClipboard('sudo ufw default deny incoming\nsudo ufw default allow outgoing\nsudo ufw allow 22/tcp comment \'SSH Access\'\nsudo ufw allow 80/tcp comment \'HTTP Traffic\'\nsudo ufw allow 443/tcp comment \'HTTPS Traffic\'\nsudo ufw enable', 'ufw-setup')"
                                    class="text-[11px] font-semibold text-slate-400 hover:text-white flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                <span x-text="copied['ufw-setup'] ? 'Tersalin!' : 'Salin'"></span>
                            </button>
                        </div>
                        <div class="p-4 font-mono-code text-slate-300 leading-relaxed overflow-x-auto space-y-1">
                            <p class="text-slate-500"># Set default policy: tolak seluruh akses masuk, izinkan keluar</p>
                            <p><span class="text-emerald-400">$</span> sudo ufw default deny incoming</p>
                            <p><span class="text-emerald-400">$</span> sudo ufw default allow outgoing</p>
                            <p class="text-slate-500 pt-2"># Izinkan SSH dan protokol Web sebelum mengaktifkan firewall</p>
                            <p><span class="text-emerald-400">$</span> sudo ufw allow 22/tcp comment 'SSH Access'</p>
                            <p><span class="text-emerald-400">$</span> sudo ufw allow 80/tcp comment 'HTTP Traffic'</p>
                            <p><span class="text-emerald-400">$</span> sudo ufw allow 443/tcp comment 'HTTPS Traffic'</p>
                            <p class="text-slate-500 pt-2"># Aktifkan firewall</p>
                            <p><span class="text-emerald-400">$</span> sudo ufw enable</p>
                        </div>
                    </div>
                </section>

                <section id="security-ssh" class="scroll-mt-36 space-y-4">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded text-[10px] font-bold font-mono-code bg-slate-900 text-white uppercase">Hardening</span>
                        <span class="text-xs font-semibold text-slate-500">SSH Cryptography</span>
                    </div>
                    <h2 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                        Hardening SSH (Key-Only Authentication)
                    </h2>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        Untuk mematikan 100% risiko peretasan via serangan <em>brute-force dictionary</em> pada akun root, Anda wajib menonaktifkan autentikasi password dan menggantinya dengan pasangan Public/Private Key berbasis Ed25519.
                    </p>

                    <div class="p-4 rounded-xl border border-slate-200 bg-white text-xs space-y-2 text-slate-700">
                        <p class="font-bold text-slate-900">Ubah konfigurasi pada file <code>/etc/ssh/sshd_config</code>:</p>
                        <pre class="bg-slate-900 text-slate-100 p-3 rounded-lg font-mono-code text-[11px] overflow-x-auto">
PasswordAuthentication no
PermitEmptyPasswords no
PubkeyAuthentication yes
ChallengeResponseAuthentication no</pre>
                        <p class="text-slate-500 text-[11px]">Setelah menyimpan perubahan, jalankan <code class="font-mono-code text-slate-800">sudo systemctl restart sshd</code>. Pastikan Anda telah menguji koneksi key pada terminal baru sebelum menutup sesi yang sedang aktif!</p>
                    </div>
                </section>

                <section id="security-fail2ban" class="scroll-mt-36 space-y-4">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded text-[10px] font-bold font-mono-code bg-slate-900 text-white uppercase">Intrusion Prevention</span>
                        <span class="text-xs font-semibold text-slate-500">Fail2ban Daemon</span>
                    </div>
                    <h2 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                        Fail2ban & Proteksi Brute Force
                    </h2>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        Fail2ban memantau log sistem secara berkelanjutan. Ketika alamat IP tertentu mengalami 3-5 kegagalan autentikasi berturut-turut, Fail2ban langsung menyuntikkan aturan DROP pada tabel iptables/nftables selama durasi ban yang ditentukan (misal: 24 jam).
                    </p>

                    <div class="rounded-xl border border-slate-800 bg-slate-950 overflow-hidden text-xs">
                        <div class="bg-slate-900 px-4 py-2 border-b border-slate-800 flex items-center justify-between">
                            <span class="text-slate-400 font-mono-code text-[11px]">Instalasi & Status Fail2ban</span>
                            <button @click="copyToClipboard('sudo apt update && sudo apt install fail2ban -y\nsudo systemctl enable --now fail2ban\nsudo fail2ban-client status sshd', 'fail2ban-setup')"
                                    class="text-[11px] font-semibold text-slate-400 hover:text-white flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                <span x-text="copied['fail2ban-setup'] ? 'Tersalin!' : 'Salin'"></span>
                            </button>
                        </div>
                        <div class="p-4 font-mono-code text-slate-300 leading-relaxed overflow-x-auto">
                            <p><span class="text-emerald-400">$</span> sudo apt update && sudo apt install fail2ban -y</p>
                            <p><span class="text-emerald-400">$</span> sudo systemctl enable --now fail2ban</p>
                            <p><span class="text-emerald-400">$</span> sudo fail2ban-client status sshd</p>
                        </div>
                    </div>
                </section>

                <hr class="border-slate-200">

                <!-- SECTION 5: OPERASIONAL SIKLUS HIDUP -->
                <section id="ops-power" class="scroll-mt-36 space-y-4">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded text-[10px] font-bold font-mono-code bg-slate-900 text-white uppercase">Modul 05</span>
                        <span class="text-xs font-semibold text-slate-500">Operasional Instance</span>
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                        Restart & Server Tidak Merespons
                    </h2>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        Anda memegang akses root penuh, jadi restart dilakukan langsung dari server. Tombol <strong>Cara Reboot</strong> di halaman Detail VPS berisi panduan yang sama.
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        <div class="p-4 rounded-xl border border-slate-200 bg-white">
                            <span class="font-bold text-slate-900 block mb-1">Restart Biasa (Mandiri)</span>
                            <p class="text-slate-500 leading-relaxed">Login lewat SSH lalu jalankan <code class="font-mono-code bg-slate-100 px-1 py-0.5 rounded text-slate-900">sudo reboot</code>. Server kembali dalam 1-2 menit dengan IP dan data yang sama.</p>
                        </div>
                        <div class="p-4 rounded-xl border border-slate-200 bg-white">
                            <span class="font-bold text-slate-900 block mb-1">Server Tidak Merespons</span>
                            <p class="text-slate-500 leading-relaxed">Jika SSH tidak bisa diakses sama sekali, klik <strong>Laporkan ke Tim</strong> di halaman Detail VPS. Tim me-restart server dari sisi infrastruktur, maksimal 6 jam kerja.</p>
                        </div>
                    </div>
                </section>

                <section id="ops-reinstall" class="scroll-mt-36 space-y-4">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded text-[10px] font-bold font-mono-code bg-slate-900 text-white uppercase">Reset Sistem</span>
                        <span class="text-xs font-semibold text-slate-500">Reinstall OS</span>
                    </div>
                    <h2 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                        Reinstall Sistem Operasi
                    </h2>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        Jika Anda ingin memulai dari awal atau beralih distribusi Linux (misal: dari Ubuntu ke Debian 12), klik <strong>Ajukan Reinstall OS</strong> di halaman Detail VPS. Tim mengerjakannya maksimal 6 jam kerja. Setelah selesai, password root baru tampil di tab Akses dan Anda menerima email pemberitahuan.
                    </p>

                    <!-- Alert Caution Box -->
                    <div class="p-4 rounded-lg bg-rose-50 border-l-4 border-rose-500 text-xs text-slate-700 flex items-start gap-3">
                        <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <div class="space-y-1">
                            <span class="font-bold text-rose-900 block">Peringatan: Seluruh Data Partisi Akan Terhapus</span>
                            <span>Reinstall memformat ulang seluruh disk server. Seluruh file, database lokal, dan konfigurasi akan terhapus permanen. Pastikan Anda sudah membuat backup di luar server sebelum mengajukan.</span>
                        </div>
                    </div>
                </section>

                <section id="ops-monitoring" class="scroll-mt-36 space-y-4">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded text-[10px] font-bold font-mono-code bg-slate-900 text-white uppercase">Metrik</span>
                        <span class="text-xs font-semibold text-slate-500">Resource & Quota</span>
                    </div>
                    <h2 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                        Memantau Pemakaian Resource
                    </h2>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        Dashboard menampilkan spesifikasi paket (vCPU, RAM, disk) dan status layanan. Untuk melihat pemakaian langsung, jalankan perintah berikut di server:
                    </p>
                    <div class="bg-[#0B0F19] text-slate-300 p-4 rounded-xl font-mono-code text-xs space-y-1">
                        <p><span class="text-emerald-400">$</span> htop      <span class="text-slate-500"># pemakaian CPU &amp; RAM per proses</span></p>
                        <p><span class="text-emerald-400">$</span> free -h   <span class="text-slate-500"># sisa memori</span></p>
                        <p><span class="text-emerald-400">$</span> df -h     <span class="text-slate-500"># sisa ruang disk</span></p>
                    </div>
                </section>

                <section id="ops-billing" class="scroll-mt-36 space-y-4">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded text-[10px] font-bold font-mono-code bg-slate-900 text-white uppercase">Siklus Hidup</span>
                        <span class="text-xs font-semibold text-slate-500">Billing & Suspension</span>
                    </div>
                    <h2 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                        Siklus Tagihan & Masa Tenggang
                    </h2>
                    <div class="space-y-2 text-xs text-slate-600">
                        <p><strong>Sebelum jatuh tempo:</strong> Pengingat perpanjangan dikirim lewat email. Tanggal jatuh tempo selalu tercantum di dashboard.</p>
                        <p><strong>Setelah jatuh tempo:</strong> Layanan masuk masa tenggang. Lamanya berbeda per jenis layanan, dan tanggal akhirnya tampil di tab Berlangganan pada halaman Detail VPS.</p>
                        <p><strong>Setelah masa tenggang:</strong> Jika belum diperpanjang, layanan ditangguhkan (<code class="font-mono-code bg-amber-50 text-amber-800 px-1 rounded">suspended</code>) dan data di server dapat terhapus permanen oleh penyedia infrastruktur. Perpanjang sebelum masa tenggang berakhir agar data tetap aman.</p>
                    </div>
                </section>

                <hr class="border-slate-200">

                <!-- SECTION 6: API REFERENCE & DEVELOPER INTEGRATION -->
                <section id="api-auth" class="scroll-mt-36 space-y-4">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded text-[10px] font-bold font-mono-code bg-slate-900 text-white uppercase">Modul 06</span>
                        <span class="text-xs font-semibold text-slate-500">API Reference</span>
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                        Autentikasi & Security Header API
                    </h2>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        REST API VexaHost memungkinkan otomasi deployment dan sinkronisasi status instance dengan sistem eksternal, e-commerce, atau bot internal Anda. Setiap permintaan wajib menyertakan token autentikasi rahasia melalui HTTP header:
                    </p>

                    <div class="border border-slate-200 rounded-xl overflow-hidden bg-white text-xs">
                        <table class="w-full text-left">
                            <thead class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-200">
                                <tr>
                                    <th class="px-4 py-3">Header Name</th>
                                    <th class="px-4 py-3">Format Nilai</th>
                                    <th class="px-4 py-3">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-mono-code">
                                <tr>
                                    <td class="px-4 py-3 font-bold text-slate-900 font-sans">X-Admin-Key</td>
                                    <td class="px-4 py-3 text-[#4A6FA5]">vx_sec_k9f83n2x9...</td>
                                    <td class="px-4 py-3 text-slate-600 font-sans">Kunci akses rahasia master untuk backend integrations.</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 font-bold text-slate-900 font-sans">Accept</td>
                                    <td class="px-4 py-3 text-slate-700">application/json</td>
                                    <td class="px-4 py-3 text-slate-600 font-sans">Wajib diset ke JSON payload response.</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 font-bold text-slate-900 font-sans">Content-Type</td>
                                    <td class="px-4 py-3 text-slate-700">application/json</td>
                                    <td class="px-4 py-3 text-slate-600 font-sans">Dibutuhkan untuk seluruh request bertipe POST / PUT.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- ENDPOINT 1: GET STATUS INSTANCE -->
                <section id="api-vps-status" class="scroll-mt-36 space-y-4">
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold font-mono-code bg-emerald-100 text-emerald-800 uppercase">GET</span>
                        <span class="font-mono-code text-xs font-bold text-slate-900">/api/admin/vps/{id}/status</span>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 tracking-tight">
                        Cek Status Realtime VPS Instance
                    </h3>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        Mengembalikan rincian teknis, spesifikasi hardware, status hypervisor running/stopped, IP publik, dan timestamp sinkronisasi terakhir.
                    </p>

                    <!-- Interactive Code Tabs (cURL, Python, Node.js, PHP) -->
                    <div class="rounded-xl border border-slate-800 bg-slate-950 overflow-hidden text-xs"
                         x-data="{ activeSnippet: 'curl' }">
                        <div class="bg-slate-900 px-4 py-2 border-b border-slate-800 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <button @click="activeSnippet = 'curl'"
                                        :class="activeSnippet === 'curl' ? 'text-white border-b-2 border-[#4A6FA5] font-bold' : 'text-slate-400 hover:text-slate-200'"
                                        class="px-2 py-1 transition-colors">cURL</button>
                                <button @click="activeSnippet = 'python'"
                                        :class="activeSnippet === 'python' ? 'text-white border-b-2 border-[#4A6FA5] font-bold' : 'text-slate-400 hover:text-slate-200'"
                                        class="px-2 py-1 transition-colors">Python</button>
                                <button @click="activeSnippet = 'node'"
                                        :class="activeSnippet === 'node' ? 'text-white border-b-2 border-[#4A6FA5] font-bold' : 'text-slate-400 hover:text-slate-200'"
                                        class="px-2 py-1 transition-colors">Node.js</button>
                                <button @click="activeSnippet = 'php'"
                                        :class="activeSnippet === 'php' ? 'text-white border-b-2 border-[#4A6FA5] font-bold' : 'text-slate-400 hover:text-slate-200'"
                                        class="px-2 py-1 transition-colors">PHP</button>
                            </div>
                            <button @click="copyToClipboard(
                                        activeSnippet === 'curl' ? 'curl -X GET https://vexahost.id/api/admin/vps/1/status \\\n  -H \'X-Admin-Key: YOUR_ADMIN_KEY\' \\\n  -H \'Accept: application/json\'' :
                                        activeSnippet === 'python' ? 'import requests\n\nurl = \'https://vexahost.id/api/admin/vps/1/status\'\nheaders = {\'X-Admin-Key\': \'YOUR_ADMIN_KEY\', \'Accept\': \'application/json\'}\nresponse = requests.get(url, headers=headers)\nprint(response.json())' :
                                        activeSnippet === 'node' ? 'const res = await fetch(\'https://vexahost.id/api/admin/vps/1/status\', {\n  headers: { \'X-Admin-Key\': \'YOUR_ADMIN_KEY\', \'Accept\': \'application/json\' }\n});\nconsole.log(await res.json());' :
                                        '<?php\n$ch = curl_init(\'https://vexahost.id/api/admin/vps/1/status\');\ncurl_setopt($ch, CURLOPT_HTTPHEADER, [\'X-Admin-Key: YOUR_ADMIN_KEY\', \'Accept: application/json\']);\ncurl_setopt($ch, CURLOPT_RETURNTRANSFER, true);\necho curl_exec($ch);',
                                        'api-vps-status-code')"
                                    class="text-[11px] font-semibold text-slate-400 hover:text-white flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                <span x-text="copied['api-vps-status-code'] ? 'Tersalin!' : 'Salin'"></span>
                            </button>
                        </div>
                        <div class="p-4 font-mono-code text-slate-300 overflow-x-auto leading-relaxed">
                            <div x-show="activeSnippet === 'curl'">
                                <p><span class="text-emerald-400">curl</span> -X GET https://vexahost.id/api/admin/vps/1/status \</p>
                                <p class="pl-4">-H <span class="text-amber-300">"X-Admin-Key: YOUR_ADMIN_KEY"</span> \</p>
                                <p class="pl-4">-H <span class="text-amber-300">"Accept: application/json"</span></p>
                            </div>
                            <div x-show="activeSnippet === 'python'" style="display:none;">
                                <p><span class="text-purple-400">import</span> requests</p>
                                <br>
                                <p>url = <span class="text-amber-300">'https://vexahost.id/api/admin/vps/1/status'</span></p>
                                <p>headers = {<span class="text-amber-300">'X-Admin-Key'</span>: <span class="text-amber-300">'YOUR_ADMIN_KEY'</span>, <span class="text-amber-300">'Accept'</span>: <span class="text-amber-300">'application/json'</span>}</p>
                                <p>response = requests.get(url, headers=headers)</p>
                                <p>print(response.json())</p>
                            </div>
                            <div x-show="activeSnippet === 'node'" style="display:none;">
                                <p><span class="text-purple-400">const</span> res = <span class="text-purple-400">await</span> fetch(<span class="text-amber-300">'https://vexahost.id/api/admin/vps/1/status'</span>, {</p>
                                <p class="pl-4">headers: {</p>
                                <p class="pl-8"><span class="text-amber-300">'X-Admin-Key'</span>: <span class="text-amber-300">'YOUR_ADMIN_KEY'</span>,</p>
                                <p class="pl-8"><span class="text-amber-300">'Accept'</span>: <span class="text-amber-300">'application/json'</span></p>
                                <p class="pl-4">}</p>
                                <p>});</p>
                                <p>console.log(<span class="text-purple-400">await</span> res.json());</p>
                            </div>
                            <div x-show="activeSnippet === 'php'" style="display:none;">
                                <p>&lt;?php</p>
                                <p>$ch = curl_init(<span class="text-amber-300">'https://vexahost.id/api/admin/vps/1/status'</span>);</p>
                                <p>curl_setopt($ch, CURLOPT_HTTPHEADER, [</p>
                                <p class="pl-4"><span class="text-amber-300">'X-Admin-Key: YOUR_ADMIN_KEY'</span>,</p>
                                <p class="pl-4"><span class="text-amber-300">'Accept: application/json'</span></p>
                                <p>]);</p>
                                <p>curl_setopt($ch, CURLOPT_RETURNTRANSFER, <span class="text-amber-300">true</span>);</p>
                                <p>$response = curl_exec($ch);</p>
                                <p>echo $response;</p>
                            </div>
                        </div>
                    </div>

                    <!-- JSON Response Sample -->
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 font-mono-code text-xs">
                        <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-2 font-sans">Contoh Payload Respon 200 OK:</span>
                        <pre class="text-slate-800 overflow-x-auto">{
  "id": 1,
  "hostname": "srv-production-01",
  "public_ip": "103.152.118.42",
  "status": "running",
  "cpu": 4,
  "ram": 8,
  "disk": 80,
  "os": "Ubuntu 24.04 LTS",
  "control_panel": "coolify",
  "billing_cycle": "monthly",
  "last_check": "2026-09-14T15:30:00.000000Z"
}</pre>
                    </div>
                </section>

                <!-- ENDPOINT 2: POST OTOMASI SHOPEE -->
                <section id="api-shopee-process" class="scroll-mt-36 space-y-4">
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold font-mono-code bg-indigo-100 text-indigo-800 uppercase">POST</span>
                        <span class="font-mono-code text-xs font-bold text-slate-900">/api/admin/shopee/process-order</span>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 tracking-tight">
                        Eksekusi Otomasi Order Shopee
                    </h3>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        Endpoint webhook yang dipanggil oleh bot otomasi atau integrasi marketplace Shopee ketika pembeli menyelesaikan checkout voucher paket VPS.
                    </p>

                    <!-- Parameter Specification Table -->
                    <div class="border border-slate-200 rounded-xl overflow-hidden bg-white text-xs">
                        <table class="w-full text-left">
                            <thead class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-200">
                                <tr>
                                    <th class="px-4 py-3">Field Body</th>
                                    <th class="px-4 py-3">Tipe</th>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-4 py-3">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-mono-code">
                                <tr>
                                    <td class="px-4 py-3 font-bold text-slate-900 font-sans">shopee_order_sn</td>
                                    <td class="px-4 py-3 text-slate-600">string</td>
                                    <td class="px-4 py-3 text-rose-600 font-sans font-semibold">Wajib</td>
                                    <td class="px-4 py-3 text-slate-600 font-sans">Nomor seri unik pesanan dari platform Shopee.</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 font-bold text-slate-900 font-sans">buyer_username</td>
                                    <td class="px-4 py-3 text-slate-600">string</td>
                                    <td class="px-4 py-3 text-rose-600 font-sans font-semibold">Wajib</td>
                                    <td class="px-4 py-3 text-slate-600 font-sans">Username akun Shopee pembeli.</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 font-bold text-slate-900 font-sans">vps_spec_id</td>
                                    <td class="px-4 py-3 text-slate-600">integer</td>
                                    <td class="px-4 py-3 text-rose-600 font-sans font-semibold">Wajib</td>
                                    <td class="px-4 py-3 text-slate-600 font-sans">ID paket yang dipilih (1 s/d 6).</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 font-bold text-slate-900 font-sans">os</td>
                                    <td class="px-4 py-3 text-slate-600">string</td>
                                    <td class="px-4 py-3 text-rose-600 font-sans font-semibold">Wajib</td>
                                    <td class="px-4 py-3 text-slate-600 font-sans">Distribusi OS target: <code>ubuntu2404</code>, <code>debian12</code>, dll.</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 font-bold text-slate-900 font-sans">control_panel</td>
                                    <td class="px-4 py-3 text-slate-600">string</td>
                                    <td class="px-4 py-3 text-slate-500 font-sans">Opsional</td>
                                    <td class="px-4 py-3 text-slate-600 font-sans">Opsi: <code>coolify</code>, <code>dokploy</code>, <code>aapanel</code>, atau <code>none</code>.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- SECTION: ERROR CODES -->
                <section id="api-errors" class="scroll-mt-36 space-y-4">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded text-[10px] font-bold font-mono-code bg-slate-900 text-white uppercase">Standar</span>
                        <span class="text-xs font-semibold text-slate-500">HTTP Status & Error Codes</span>
                    </div>
                    <h2 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                        Format Respon & Standar Kode Error
                    </h2>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        API VexaHost menggunakan kode status HTTP standar untuk mengindikasikan keberhasilan atau kegagalan request. Setiap kegagalan menyertakan objek JSON berisi pesan diagnostik yang jelas:
                    </p>

                    <div class="border border-slate-200 rounded-xl overflow-hidden bg-white text-xs">
                        <table class="w-full text-left">
                            <thead class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-200">
                                <tr>
                                    <th class="px-4 py-3">Kode HTTP</th>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-4 py-3">Penyebab / Deskripsi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-mono-code">
                                <tr>
                                    <td class="px-4 py-3 font-bold text-emerald-600">200 OK</td>
                                    <td class="px-4 py-3 text-slate-800">Success</td>
                                    <td class="px-4 py-3 text-slate-600 font-sans">Permintaan berhasil diproses dan mengembalikan data yang diminta.</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 font-bold text-emerald-600">201 Created</td>
                                    <td class="px-4 py-3 text-slate-800">Created</td>
                                    <td class="px-4 py-3 text-slate-600 font-sans">Instance baru atau order baru berhasil dialokasikan.</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 font-bold text-amber-600">400 Bad Request</td>
                                    <td class="px-4 py-3 text-slate-800">Malformed Payload</td>
                                    <td class="px-4 py-3 text-slate-600 font-sans">Sintaks body JSON tidak valid atau struktur tidak dikenali.</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 font-bold text-rose-600">401 Unauthorized</td>
                                    <td class="px-4 py-3 text-slate-800">Invalid Key</td>
                                    <td class="px-4 py-3 text-slate-600 font-sans">Header <code>X-Admin-Key</code> tidak disertakan atau nilai token salah.</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 font-bold text-amber-600">404 Not Found</td>
                                    <td class="px-4 py-3 text-slate-800">Resource Missing</td>
                                    <td class="px-4 py-3 text-slate-600 font-sans">ID VPS atau nomor pesanan tidak ditemukan di database.</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 font-bold text-amber-600">422 Unprocessable</td>
                                    <td class="px-4 py-3 text-slate-800">Validation Error</td>
                                    <td class="px-4 py-3 text-slate-600 font-sans">Gagal validasi parameter (misal: provider tidak cocok dengan paket).</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 font-bold text-rose-600">500 Server Error</td>
                                    <td class="px-4 py-3 text-slate-800">Internal Failure</td>
                                    <td class="px-4 py-3 text-slate-600 font-sans">Kendala teknis pada upstream datacenter API hypervisor.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <div class="pt-8 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div>
                        <span class="text-xs font-semibold text-slate-900 block">VexaHost Engineering Knowledge Base</span>
                        <span class="text-[11px] text-slate-400">Terakhir diperbarui: September 2026 &bull; Rilis Produksi v1.4</span>
                    </div>
                    <button @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-semibold text-slate-600 hover:text-black hover:bg-white transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                        <span>Kembali ke Atas</span>
                    </button>
                </div>

            </main>

            <!-- RIGHT COLUMN: ON THIS PAGE (TOC / DI HALAMAN INI) -->
            <aside class="lg:col-span-3 sticky top-32 hidden lg:block space-y-6 text-xs">
                <div class="p-5 rounded-xl border border-slate-200 bg-white">
                    <p class="font-bold text-slate-900 uppercase tracking-wider text-[11px] mb-3 pb-2 border-b border-slate-100 flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
                        <span>Di Halaman Ini</span>
                    </p>
                    <nav class="space-y-1.5 text-slate-600 font-medium">
                        <a href="#intro" class="block hover:text-slate-900 transition-colors py-0.5">Pengenalan Arsitektur Cloud</a>
                        <a href="#provisioning" class="block hover:text-slate-900 transition-colors py-0.5">Alur Aktivasi Server</a>
                        <a href="#ssh-access" class="block hover:text-slate-900 transition-colors py-0.5">Akses SSH & Kredensial</a>
                        <a href="#dual-provider" class="block hover:text-slate-900 transition-colors py-0.5">Dual-Provider (Tencent & Cloudeka)</a>
                        <a href="#datacenters" class="block hover:text-slate-900 transition-colors py-0.5">Datacenter & Peering Jaringan</a>
                        <a href="#storage" class="block hover:text-slate-900 transition-colors py-0.5">Penyimpanan NVMe SSD</a>
                        <a href="#stack-coolify" class="block hover:text-slate-900 transition-colors py-0.5">Coolify Platform Deployment</a>
                        <a href="#stack-dokploy" class="block hover:text-slate-900 transition-colors py-0.5">Dokploy Modern PaaS</a>
                        <a href="#stack-aapanel" class="block hover:text-slate-900 transition-colors py-0.5">aaPanel (LEMP/LAMP)</a>
                        <a href="#security-ufw" class="block hover:text-slate-900 transition-colors py-0.5">Firewall UFW & Port Policy</a>
                        <a href="#security-ssh" class="block hover:text-slate-900 transition-colors py-0.5">Hardening SSH Key-Only</a>
                        <a href="#security-fail2ban" class="block hover:text-slate-900 transition-colors py-0.5">Fail2ban & Brute Force</a>
                        <a href="#ops-power" class="block hover:text-slate-900 transition-colors py-0.5">Restart & Server Tidak Merespons</a>
                        <a href="#ops-reinstall" class="block hover:text-slate-900 transition-colors py-0.5">Reinstall Sistem Operasi</a>
                        <a href="#ops-billing" class="block hover:text-slate-900 transition-colors py-0.5">Siklus Tagihan & Masa Tenggang</a>
                        <a href="#api-auth" class="block hover:text-slate-900 transition-colors py-0.5">Autentikasi & Security Header</a>
                        <a href="#api-vps-status" class="block hover:text-slate-900 transition-colors py-0.5">GET Status Instance</a>
                        <a href="#api-shopee-process" class="block hover:text-slate-900 transition-colors py-0.5">POST Otomasi Shopee</a>
                        <a href="#api-errors" class="block hover:text-slate-900 transition-colors py-0.5">Standar Kode Error API</a>
                    </nav>
                </div>

                <div class="p-4 rounded-xl border border-slate-200 bg-white">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Shortcut Keyboard</span>
                    <div class="flex items-center justify-between text-slate-700 py-1">
                        <span>Pencarian Cepat</span>
                        <kbd class="font-mono-code text-[10px] bg-slate-100 border border-slate-200 px-1.5 py-0.5 rounded font-bold text-slate-800">Ctrl + K</kbd>
                    </div>
                    <div class="flex items-center justify-between text-slate-700 py-1">
                        <span>Tutup Modal</span>
                        <kbd class="font-mono-code text-[10px] bg-slate-100 border border-slate-200 px-1.5 py-0.5 rounded font-bold text-slate-800">ESC</kbd>
                    </div>
                </div>
            </aside>

        </div>
    </div>

</div>
@endsection
