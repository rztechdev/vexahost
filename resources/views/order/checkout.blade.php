@extends('layouts.app', [
    'title' => 'Checkout Layanan — VexaHost',
    'description' => 'Selesaikan pemesanan layanan VexaHost.',
    'robots' => 'noindex, follow',
])

@section('content')
<div class="py-10 bg-white min-h-screen" x-data="checkoutState()" x-init="init()">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Back link --}}
        <a :href="isDatabasePackage ? '{{ route('home') }}#database-packages' : (isAiPackage ? '{{ route('home') }}#ai-packages' : '{{ route('home') }}#pricing')" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-black transition-colors mb-6">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            <span x-text="isDatabasePackage ? 'Kembali ke Katalog Managed Database' : (isAiPackage ? 'Kembali ke Katalog AI Combo' : 'Kembali ke Pilihan Paket VPS')"></span>
        </a>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Left: Configuration Slides --}}
            <div class="lg:col-span-2">
                <div class="relative overflow-hidden">
                    {{-- Slide 1: Konfigurasi VPS (Khusus VPS biasa, dilewati untuk AI & DB) --}}
                    <div x-show="!isDirectCheckout && currentSlide === 0" x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-x-10" x-transition:enter-end="opacity-100 translate-x-0"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 -translate-x-10"
                         class="space-y-6">
                        @include('order.partials.slide-configuration')
                    </div>

                    {{-- Slide 2: Informasi Akun (Start langsung di sini jika paket AI atau Managed DB) --}}
                    <div x-show="currentSlide === 1" x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-x-10" x-transition:enter-end="opacity-100 translate-x-0"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 -translate-x-10"
                         class="space-y-6">

                        {{-- Alert khusus paket AI Combo: Konfigurasi Otomatis --}}
                        <template x-if="isAiPackage && currentSpec">
                            <div class="p-5 sm:p-6 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 space-y-3 shadow-xs">
                                <div class="flex items-center justify-between flex-wrap gap-2">
                                    <span class="text-xs font-bold uppercase tracking-wider text-slate-600">Paket AI Combo</span>
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-white text-slate-800 border border-slate-200" x-text="currentSpec.badge || 'AI Combo'"></span>
                                </div>
                                <div>
                                    <h2 class="text-xl font-bold text-slate-900" x-text="currentSpec.name"></h2>
                                    <p class="text-xs text-slate-500 mt-0.5" x-text="currentSpec.tagline"></p>
                                </div>
                                <div class="flex items-center gap-2 text-xs text-slate-600 bg-white px-3.5 py-2.5 rounded-lg border border-slate-200">
                                    <svg class="w-4 h-4 text-[#4A6FA5] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span>Konfigurasi server otomatis: OS, runtime AI, stack, dan swap memory telah disiapkan.</span>
                                </div>
                            </div>
                        </template>

                        {{-- Alert khusus paket Managed Database: Konfigurasi Otomatis & Pilihan Engine/Tools --}}
                        <template x-if="isDatabasePackage && currentSpec">
                            <div class="space-y-6 mb-6">
                                <div class="p-5 sm:p-6 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 space-y-3 shadow-xs">
                                    <div class="flex items-center justify-between flex-wrap gap-2">
                                        <span class="text-xs font-bold uppercase tracking-wider text-slate-600">Paket Managed Database</span>
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200" x-text="currentSpec.badge || 'Dedicated DB'"></span>
                                    </div>
                                    <div>
                                        <h2 class="text-xl font-bold text-slate-900" x-text="currentSpec.name"></h2>
                                        <p class="text-xs text-slate-500 mt-0.5" x-text="currentSpec.tagline"></p>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs text-slate-600">
                                        <div class="flex items-center gap-2 bg-white px-3 py-2 rounded-lg border border-slate-200">
                                            <svg class="w-4 h-4 text-[#4A6FA5] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            <span>Server database khusus, terpisah dari aplikasi</span>
                                        </div>
                                        <div class="flex items-center gap-2 bg-white px-3 py-2 rounded-lg border border-slate-200">
                                            <svg class="w-4 h-4 text-[#4A6FA5] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            <span>Datacenter Jakarta</span>
                                        </div>
                                        <div class="flex items-center gap-2 bg-white px-3 py-2 rounded-lg border border-slate-200">
                                            <svg class="w-4 h-4 text-[#4A6FA5] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            <span>Dedicated IPv4 (Remote client ready)</span>
                                        </div>
                                        <div class="flex items-center gap-2 bg-white px-3 py-2 rounded-lg border border-slate-200">
                                            <svg class="w-4 h-4 text-[#4A6FA5] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            <span>Kredensial koneksi siap pakai di dashboard</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Pilihan Database Engine (Visual Radio Selector) --}}
                                <div class="border border-slate-200 rounded-xl p-5 sm:p-6 bg-white space-y-4 shadow-xs">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h3 class="text-sm font-bold text-slate-900">Pilih Database Engine</h3>
                                            <p class="text-xs text-slate-500 mt-0.5">Engine database siap pakai yang akan langsung aktif di server Anda.</p>
                                        </div>
                                        <span class="text-xs font-semibold px-2 py-0.5 rounded bg-slate-100 text-slate-700">Wajib</span>
                                    </div>

                                    <div class="grid grid-cols-1 gap-2.5">
                                        {{-- PostgreSQL 16 --}}
                                        <label @click="dbEngine = 'postgres'" class="flex items-start gap-3.5 p-3.5 rounded-lg border cursor-pointer transition-all"
                                               :class="dbEngine === 'postgres' ? 'border-black bg-slate-50 ring-1 ring-black' : 'border-slate-200 hover:border-slate-300 bg-white'">
                                            <input type="radio" name="_radio_db_engine" value="postgres" :checked="dbEngine === 'postgres'" class="mt-1 text-black focus:ring-black">
                                            <img src="{{ asset('images/logos/databases/postgres.svg') }}" alt="PostgreSQL" class="w-6 h-6 object-contain shrink-0 mt-0.5">
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center justify-between gap-2">
                                                    <span class="text-xs font-bold text-slate-900">PostgreSQL 16</span>
                                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-black text-white">Default Recommended</span>
                                                </div>
                                                <p class="text-[11px] text-slate-500 mt-0.5">Default recommended untuk backend modern &amp; Supabase style (Prisma, Drizzle, Node.js, Go, Python).</p>
                                            </div>
                                        </label>

                                        {{-- MySQL 8.0 / MariaDB 11 --}}
                                        <label @click="dbEngine = 'mysql'" class="flex items-start gap-3.5 p-3.5 rounded-lg border cursor-pointer transition-all"
                                               :class="dbEngine === 'mysql' ? 'border-black bg-slate-50 ring-1 ring-black' : 'border-slate-200 hover:border-slate-300 bg-white'">
                                            <input type="radio" name="_radio_db_engine" value="mysql" :checked="dbEngine === 'mysql'" class="mt-1 text-black focus:ring-black">
                                            <img src="{{ asset('images/logos/databases/mysql.svg') }}" alt="MySQL" class="w-6 h-6 object-contain shrink-0 mt-0.5">
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center justify-between gap-2">
                                                    <span class="text-xs font-bold text-slate-900">MySQL 8.0 / MariaDB 11</span>
                                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-black text-white">Rekomendasi PHP &amp; CMS</span>
                                                </div>
                                                <p class="text-[11px] text-slate-500 mt-0.5">Rekomendasi Laravel, WordPress, WooCommerce, PHP native &amp; e-commerce.</p>
                                            </div>
                                        </label>

                                        {{-- Redis 7 --}}
                                        <label @click="dbEngine = 'redis'" class="flex items-start gap-3.5 p-3.5 rounded-lg border cursor-pointer transition-all"
                                               :class="dbEngine === 'redis' ? 'border-black bg-slate-50 ring-1 ring-black' : 'border-slate-200 hover:border-slate-300 bg-white'">
                                            <input type="radio" name="_radio_db_engine" value="redis" :checked="dbEngine === 'redis'" class="mt-1 text-black focus:ring-black">
                                            <img src="{{ asset('images/logos/databases/redis.svg') }}" alt="Redis" class="w-6 h-6 object-contain shrink-0 mt-0.5">
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center justify-between gap-2">
                                                    <span class="text-xs font-bold text-slate-900">Redis 7 In-Memory Cache</span>
                                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-black text-white">Session &amp; Queue Booster</span>
                                                </div>
                                                <p class="text-[11px] text-slate-500 mt-0.5">Rekomendasi session/queue booster, high-speed key-value cache, dan pub/sub messaging.</p>
                                            </div>
                                        </label>

                                        {{-- MongoDB 7 --}}
                                        <label @click="dbEngine = 'mongodb'" class="flex items-start gap-3.5 p-3.5 rounded-lg border cursor-pointer transition-all"
                                               :class="dbEngine === 'mongodb' ? 'border-black bg-slate-50 ring-1 ring-black' : 'border-slate-200 hover:border-slate-300 bg-white'">
                                            <input type="radio" name="_radio_db_engine" value="mongodb" :checked="dbEngine === 'mongodb'" class="mt-1 text-black focus:ring-black">
                                            <img src="{{ asset('images/logos/databases/mongodb.svg') }}" alt="MongoDB" class="w-6 h-6 object-contain shrink-0 mt-0.5">
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center justify-between gap-2">
                                                    <span class="text-xs font-bold text-slate-900">MongoDB 7 Community</span>
                                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-black text-white">NoSQL Document</span>
                                                </div>
                                                <p class="text-[11px] text-slate-500 mt-0.5">Khusus MERN / JSON Document dengan skema dinamis dan agregasi dokumen cepat.</p>
                                            </div>
                                        </label>

                                        {{-- Qdrant / pgvector --}}
                                        <label @click="dbEngine = 'vector'" class="flex items-start gap-3.5 p-3.5 rounded-lg border cursor-pointer transition-all"
                                               :class="dbEngine === 'vector' ? 'border-black bg-slate-50 ring-1 ring-black' : 'border-slate-200 hover:border-slate-300 bg-white'">
                                            <input type="radio" name="_radio_db_engine" value="vector" :checked="dbEngine === 'vector'" class="mt-1 text-black focus:ring-black">
                                            <img src="{{ asset('images/logos/databases/qdrant.svg') }}" alt="Qdrant" class="w-6 h-6 object-contain shrink-0 mt-0.5">
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center justify-between gap-2">
                                                    <span class="text-xs font-bold text-slate-900">Qdrant / pgvector</span>
                                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-black text-white">Khusus AI &amp; RAG</span>
                                                </div>
                                                <p class="text-[11px] text-slate-500 mt-0.5">Khusus tier Enterprise untuk AI &amp; RAG, indexing vector similarity, dan semantic search dokumen.</p>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                {{-- Pilihan Tools Pengelola Web (Opsional) --}}
                                <div class="border border-slate-200 rounded-xl p-5 sm:p-6 bg-white space-y-4 shadow-xs">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h3 class="text-sm font-bold text-slate-900">Pilih Tools Pengelola Web (Opsional)</h3>
                                            <p class="text-xs text-slate-500 mt-0.5">Antarmuka visual untuk mengelola tabel dan query data lewat browser.</p>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        {{-- CloudBeaver --}}
                                        <label @click="dbManager = 'cloudbeaver'" class="flex items-start gap-3 p-3.5 rounded-lg border cursor-pointer transition-all"
                                               :class="dbManager === 'cloudbeaver' ? 'border-black bg-slate-50 ring-1 ring-black' : 'border-slate-200 hover:border-slate-300 bg-white'">
                                            <input type="radio" name="_radio_db_manager" value="cloudbeaver" :checked="dbManager === 'cloudbeaver'" class="mt-1 text-black focus:ring-black">
                                            <img src="{{ asset('images/logos/databases/cloudbeaver.svg') }}" alt="CloudBeaver" class="w-6 h-6 object-contain shrink-0 mt-0.5">
                                            <div class="flex-1 min-w-0">
                                                <span class="text-xs font-bold text-slate-900 block">CloudBeaver / Adminer Web GUI</span>
                                                <p class="text-[11px] text-slate-500 mt-1 leading-relaxed">Akses browser aman via HTTPS &amp; port 8080. Siap kelola tabel, query data, dan pantau database secara visual.</p>
                                            </div>
                                        </label>

                                        {{-- CLI Only --}}
                                        <label @click="dbManager = 'cli_only'" class="flex items-start gap-3 p-3.5 rounded-lg border cursor-pointer transition-all"
                                               :class="dbManager === 'cli_only' ? 'border-black bg-slate-50 ring-1 ring-black' : 'border-slate-200 hover:border-slate-300 bg-white'">
                                            <input type="radio" name="_radio_db_manager" value="cli_only" :checked="dbManager === 'cli_only'" class="mt-1 text-black focus:ring-black">
                                            <div class="w-6 h-6 flex items-center justify-center shrink-0 mt-0.5 font-mono text-xs font-bold text-slate-800 bg-slate-100 rounded">&gt;_</div>
                                            <div class="flex-1 min-w-0">
                                                <span class="text-xs font-bold text-slate-900 block">CLI Only (Headless)</span>
                                                <p class="text-[11px] text-slate-500 mt-1 leading-relaxed">Hanya port database terbuka via password / whitelist IP. Tanpa beban GUI browser, murni efisiensi memori.</p>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </template>

                        {{-- Konfigurasi Hostname & Root Password (Khusus AI Combo & Managed Database) --}}
                        <template x-if="isDirectCheckout">
                            <div class="border border-slate-200 rounded-xl p-5 sm:p-6 bg-white space-y-4 shadow-xs mb-6">
                                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                                    <div>
                                        <h3 class="text-sm font-bold text-slate-900">Nama Server &amp; Kredensial Root</h3>
                                        <p class="text-xs text-slate-500 mt-0.5">Tentukan hostname server dan password akses root/administrator Anda.</p>
                                    </div>
                                    <span class="text-xs font-semibold px-2 py-0.5 rounded bg-rose-50 text-rose-600 border border-rose-200">Wajib</span>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                            Hostname / Server Name <span class="text-rose-600">*</span>
                                        </label>
                                        <input type="text" x-model="vpsName" required minlength="3" maxlength="63" pattern="[A-Za-z0-9][A-Za-z0-9-]*" placeholder="misal: ai-runner-01" class="w-full px-3 py-2.5 rounded-lg border border-slate-200 text-sm font-mono-code focus:border-black focus:ring-1 focus:ring-black">
                                        <p class="text-[11px] text-slate-400 mt-1">Gunakan huruf, angka, dan tanda hubung.</p>
                                    </div>

                                    <div>
                                        <div class="flex items-center justify-between mb-1.5">
                                            <label class="text-xs font-semibold text-slate-700 flex items-center gap-1">
                                                <span>Root Password Server</span>
                                                <span class="text-rose-600">*</span>
                                            </label>
                                            <span class="text-[11px] text-slate-400 font-mono-code">Min. 8 char</span>
                                        </div>
                                        <div class="relative">
                                            <input :type="showRootPassword ? 'text' : 'password'" 
                                                   x-model="rootPassword" 
                                                   required 
                                                   minlength="8" 
                                                   placeholder="Ketik password root/admin" 
                                                   class="w-full pl-3 pr-10 py-2.5 rounded-lg border border-slate-200 text-sm font-mono-code focus:border-black focus:ring-1 focus:ring-black" 
                                                   autocomplete="new-password">
                                            <button type="button" 
                                                    @click="showRootPassword = !showRootPassword" 
                                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600"
                                                    tabindex="-1">
                                                <svg x-show="!showRootPassword" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                                <svg x-show="showRootPassword" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
                                                </svg>
                                            </button>
                                        </div>
                                        <p class="text-[11px] text-slate-400 mt-1" x-text="isDatabasePackage ? 'Password ini juga menjadi password root user database Anda.' : 'Password untuk akses SSH user root ke node AI.'"></p>
                                    </div>
                                </div>
                            </div>
                        </template>

                        @include('order.partials.slide-account')
                    </div>

                    {{-- Slide 3: Metode Pembayaran --}}
                    <div x-show="currentSlide === 2" x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-x-10" x-transition:enter-end="opacity-100 translate-x-0"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 -translate-x-10"
                         class="space-y-6">
                        @include('order.partials.slide-payment')
                    </div>

                    {{-- Slide 4: Konfirmasi Pembayaran & Full Harga --}}
                    <div x-show="currentSlide === 3" x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-x-10" x-transition:enter-end="opacity-100 translate-x-0"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 -translate-x-10"
                         class="space-y-6">
                        @include('order.partials.slide-confirmation')
                    </div>
                </div>

                {{-- Navigation Buttons --}}
                <div class="flex justify-between items-center pt-8 mt-8 border-t border-slate-200">
                    <div>
                        {{-- Tombol Kembali jika Direct Checkout (AI Package / Managed DB) --}}
                        <template x-if="isDirectCheckout">
                            <div>
                                <button type="button" @click="previousSlide()" x-show="currentSlide > 1"
                                        class="px-5 py-2.5 rounded-lg border border-slate-300 hover:bg-slate-50 text-slate-700 text-sm font-medium transition-colors">
                                    ← <span x-text="currentSlide === 3 ? 'Kembali ke Pilih Pembayaran' : 'Kembali ke Informasi Akun'"></span>
                                </button>
                            </div>
                        </template>

                        {{-- Tombol Kembali jika Standard VPS --}}
                        <template x-if="!isDirectCheckout">
                            <button type="button" @click="previousSlide()" x-show="currentSlide > 0"
                                    class="px-5 py-2.5 rounded-lg border border-slate-300 hover:bg-slate-50 text-slate-700 text-sm font-medium transition-colors">
                                ← Kembali
                            </button>
                        </template>
                    </div>

                    {{-- Tombol Lanjut / Submit --}}
                    <div x-show="!isDirectCheckout && currentSlide === 0" class="ml-auto">
                        <button type="button" @click="nextSlide()"
                                class="px-6 py-2.5 rounded-lg bg-black hover:bg-neutral-800 text-white text-sm font-bold transition-colors">
                            Lanjut ke Informasi Akun →
                        </button>
                    </div>
                    <div x-show="currentSlide === 1" class="ml-auto">
                        <button type="button" @click="nextSlide()"
                                class="px-6 py-2.5 rounded-lg bg-black hover:bg-neutral-800 text-white text-sm font-bold transition-colors">
                            Lanjut ke Pilih Pembayaran →
                        </button>
                    </div>
                    <div x-show="currentSlide === 2" class="ml-auto">
                        <button type="button" @click="nextSlide()"
                                class="px-6 py-2.5 rounded-lg bg-black hover:bg-neutral-800 text-white text-sm font-bold transition-colors">
                            Lanjut ke Konfirmasi Pembayaran →
                        </button>
                    </div>
                    <div x-show="currentSlide === 3" class="ml-auto flex flex-col items-end gap-1.5">
                        <p x-show="!termsAccepted" x-cloak class="text-xs font-semibold text-slate-500">
                            Centang persetujuan ketentuan untuk melanjutkan.
                        </p>
                        <button type="button" @click="submitCheckout()" :disabled="isSubmitting || !termsAccepted"
                                class="px-7 py-3 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold transition-colors disabled:opacity-50 disabled:cursor-not-allowed inline-flex items-center gap-2 shadow-sm">
                            <svg x-show="!isSubmitting" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            <svg x-show="isSubmitting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-text="isSubmitting ? 'Memproses Pesanan...' : (paymentMethod === 'lynk' ? 'Bayar Sekarang via Lynk.id' : 'Bayar Sekarang')"></span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Right: Sticky Summary --}}
            <div class="lg:col-span-1">
                <div class="sticky top-24 border border-slate-200 rounded-xl p-6 space-y-5 bg-white shadow-sm">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <h3 class="font-bold text-slate-900 text-base">Ringkasan Pesanan</h3>
                        <span class="px-2 py-0.5 rounded text-[11px] font-bold uppercase"
                              :class="isDatabasePackage ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : (isAiPackage ? 'bg-blue-50 text-[#4A6FA5] border border-blue-200' : 'bg-slate-100 text-slate-700')">
                            <span x-text="isDatabasePackage ? 'Managed DB' : (isAiPackage ? 'AI Combo' : 'Cloud VPS')"></span>
                        </span>
                    </div>

                    {{-- Rincian jika Paket Direct Checkout (AI & Database) --}}
                    <template x-if="isDirectCheckout && currentSpec">
                        <div class="space-y-3.5 text-xs text-slate-700">
                            <div>
                                <span class="text-slate-400 block mb-0.5">Nama Paket:</span>
                                <span class="font-bold text-slate-900 text-sm block" x-text="currentSpec.name"></span>
                                <span class="text-slate-500 text-[11px] italic" x-text="currentSpec.tagline"></span>
                            </div>

                            <div class="bg-slate-50 p-3 rounded-lg border border-slate-100 space-y-1">
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Resource:</span>
                                    <span class="font-bold text-slate-900" x-text="currentSpec.cpu + ' Core vCPU · ' + currentSpec.ram + ' GB RAM'"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Storage:</span>
                                    <span class="font-bold text-slate-900" x-text="currentSpec.disk + ' GB NVMe SSD'"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Bandwidth:</span>
                                    <span class="font-bold text-slate-900" x-text="currentSpec.bandwidth + ' Mbps Unmetered'"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Setup:</span>
                                    <span class="font-bold text-slate-900">Pre-configured (Otomatis)</span>
                                </div>
                                <template x-if="isDatabasePackage">
                                    <div class="border-t border-slate-200 pt-2 mt-2 space-y-1">
                                        <div class="flex justify-between">
                                            <span class="text-slate-500">Database Engine:</span>
                                            <span class="font-bold text-[#4A6FA5]" x-text="dbEngineLabel"></span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-slate-500">Pengelola Web:</span>
                                            <span class="font-bold text-slate-800" x-text="dbManager === 'cloudbeaver' ? 'CloudBeaver Web GUI' : 'CLI Only'"></span>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <div x-show="parsedFeatures.length > 0">
                                <span class="text-slate-400 block mb-1 font-semibold" x-text="isDatabasePackage ? 'Fitur Server Database:' : 'Bundling Pre-configured:'"></span>
                                <ul class="space-y-1.5 pl-1">
                                    <template x-for="(feat, idx) in parsedFeatures" :key="idx">
                                        <li class="flex items-start gap-1.5 text-[11px] text-slate-600">
                                            <span class="text-emerald-500 font-bold">✓</span>
                                            <span class="leading-tight">
                                                <template x-if="feat.includes(':')">
                                                    <span>
                                                        <strong class="text-slate-900 font-semibold" x-text="feat.split(':')[0] + ':'"></strong>
                                                        <span x-text="feat.substring(feat.indexOf(':') + 1)"></span>
                                                    </span>
                                                </template>
                                                <template x-if="!feat.includes(':')">
                                                    <span x-text="feat"></span>
                                                </template>
                                            </span>
                                        </li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                    </template>

                    {{-- Rincian jika Standard VPS --}}
                    <template x-if="!isDirectCheckout">
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-slate-500">VPS Name</span>
                                <span class="font-medium text-slate-900 font-mono-code" x-text="vpsName || '-'"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Paket</span>
                                <span class="font-medium text-slate-900" x-text="currentSpec ? currentSpec.name : 'Pilih Paket'"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Provider</span>
                                <span class="font-medium text-slate-900" x-text="provider === 'tencent' ? 'Tencent Cloud' : 'Cloudeka by Lintasarta'"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Datacenter</span>
                                <span class="font-medium text-slate-900 capitalize" x-text="datacenter"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">OS</span>
                                <span class="font-medium text-slate-900" x-text="os === 'ubuntu2404' ? 'Ubuntu 24.04 LTS' : 'Ubuntu 22.04 LTS'"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Stack</span>
                                <span class="font-medium text-slate-900" x-text="controlPanelLabel"></span>
                            </div>
                        </div>
                    </template>

                    {{-- Price Calculation breakdown --}}
                    <div class="border-t border-slate-200 pt-4 space-y-2 text-xs">
                        <div class="flex justify-between text-slate-900 font-bold pt-2 border-t border-slate-200">
                            <span>Total Pembayaran</span>
                            <span class="font-mono-code text-lg" x-text="formatRupiah(totalPrice)"></span>
                        </div>
                        <p class="text-[11px] text-slate-400 text-right">Tarif flat perpanjangan bulanan</p>
                    </div>

                    {{-- Payment Logos --}}
                    <div class="border-t border-slate-200 pt-4">
                        <p class="text-xs text-slate-500 mb-3 text-center">Metode Pembayaran Tersedia</p>
                        <div class="flex flex-wrap justify-center gap-3">
                            @foreach([
                                'lynk' => 'Lynk.id',
                                'qris' => 'QRIS',
                                'va_bca' => 'BCA',
                                'va_mandiri' => 'Mandiri', 
                                'va_bri' => 'BRI',
                                'va_bni' => 'BNI',
                                'gopay' => 'GoPay',
                                'ewallet_ovo' => 'OVO',
                                'dana' => 'DANA',
                                'ewallet_shopeepay' => 'ShopeePay'
                            ] as $file => $name)
                                <div class="w-10 h-6 flex items-center justify-center bg-white border border-slate-200 rounded" title="{{ $name }}">
                                    <img src="{{ asset('images/payments/' . $file . '.svg') }}" alt="{{ $name }}" class="h-4">
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Progress Steps (Rata Tengah Halaman / Full Width) --}}
        <div class="mt-10">
            {{-- Progress Steps untuk Direct Checkout (AI & Database - 3 Langkah) --}}
            <template x-if="isDirectCheckout">
                <div class="flex items-center justify-center gap-3 sm:gap-6 md:gap-8 mx-auto">
                    <div class="flex items-center gap-2 shrink-0">
                        <span class="w-7 h-7 rounded-full flex items-center justify-center text-xs sm:text-sm font-bold shrink-0 transition-all"
                              :class="currentSlide >= 1 ? 'bg-black text-white' : 'bg-slate-200 text-slate-500 border border-slate-300'">1</span>
                        <span class="text-xs sm:text-sm font-semibold whitespace-nowrap"
                              :class="currentSlide >= 1 ? 'text-slate-900' : 'text-slate-400'">Informasi Akun</span>
                    </div>
                    <div class="w-8 sm:w-16 md:w-24 h-px shrink-0 transition-colors" :class="currentSlide > 1 ? 'bg-black' : 'bg-slate-200'"></div>
                    <div class="flex items-center gap-2 shrink-0">
                        <span class="w-7 h-7 rounded-full flex items-center justify-center text-xs sm:text-sm font-bold shrink-0 transition-all"
                              :class="currentSlide >= 2 ? 'bg-black text-white' : 'bg-slate-200 text-slate-500 border border-slate-300'">2</span>
                        <span class="text-xs sm:text-sm font-semibold whitespace-nowrap"
                              :class="currentSlide >= 2 ? 'text-slate-900' : 'text-slate-400'">Pilih Pembayaran</span>
                    </div>
                    <div class="w-8 sm:w-16 md:w-24 h-px shrink-0 transition-colors" :class="currentSlide > 2 ? 'bg-black' : 'bg-slate-200'"></div>
                    <div class="flex items-center gap-2 shrink-0">
                        <span class="w-7 h-7 rounded-full flex items-center justify-center text-xs sm:text-sm font-bold shrink-0 transition-all"
                              :class="currentSlide >= 3 ? 'bg-black text-white' : 'bg-slate-200 text-slate-500 border border-slate-300'">3</span>
                        <span class="text-xs sm:text-sm font-semibold whitespace-nowrap"
                              :class="currentSlide >= 3 ? 'text-slate-900' : 'text-slate-400'">Konfirmasi Pembayaran</span>
                    </div>
                </div>
            </template>

            {{-- Progress Steps untuk VPS Reguler (4 Langkah) --}}
            <template x-if="!isDirectCheckout">
                <div class="flex items-center justify-center gap-2 sm:gap-4 md:gap-6 mx-auto">
                    @foreach(['Konfigurasi VPS', 'Informasi Akun', 'Pilih Pembayaran', 'Konfirmasi Pembayaran'] as $index => $step)
                        <div class="flex items-center gap-2 shrink-0">
                            <span class="w-7 h-7 rounded-full flex items-center justify-center text-xs sm:text-sm font-bold shrink-0 transition-all"
                                  :class="currentSlide >= {{ $index }} ? 'bg-black text-white' : 'bg-slate-200 text-slate-500 border border-slate-300'">
                                {{ $index + 1 }}
                            </span>
                            <span class="text-xs sm:text-sm font-semibold whitespace-nowrap"
                                  :class="currentSlide >= {{ $index }} ? 'text-slate-900' : 'text-slate-400'">
                                {{ $step }}
                            </span>
                        </div>
                        @if($index < 3)
                            <div class="w-6 sm:w-12 md:w-16 lg:w-20 h-px shrink-0 transition-colors"
                                 :class="currentSlide > {{ $index }} ? 'bg-black' : 'bg-slate-200'"></div>
                        @endif
                    @endforeach
                </div>
            </template>
        </div>
    </div>
</div>

<script>
function checkoutState() {
    return {
        currentSlide: 0,
        isSubmitting: false,
        // PHASE 2 - persetujuan ketentuan wajib dicentang sebelum pesanan dikirim.
        termsAccepted: false,
        selectedSpec: {{ $selectedSpec ? $selectedSpec->id : 'null' }},
        specs: {{ Js::from($specs->keyBy('id')) }},
        billingCycle: 'monthly',
        provider: 'tencent',
        vpsName: '',
        rootPassword: '',
        showRootPassword: false,
        stackType: 'none',
        controlPanel: 'none',
        datacenter: 'singapore',
        os: 'ubuntu2404',
        dbEngine: 'postgres',
        dbManager: 'cloudbeaver',
        operatingSystems: {
            tencent: {
                windows2012r2: 'Windows Server 2012 R2 DataCenter 64bit EN', windows2016: 'Windows Server 2016 DataCenter 64bit EN', windows2019: 'Windows Server 2019 DataCenter 64bit EN', windows2022: 'Windows Server 2022 DataCenter 64bit EN',
                ubuntu2404: 'Ubuntu Server 24.04 LTS 64bit', ubuntu2204: 'Ubuntu Server 22.04 LTS 64bit', opencloudos9: 'OpenCloudOS 9', opencloudos8: 'OpenCloudOS 8', centos76: 'CentOS 7.6 64bit', centos_stream9: 'CentOS Stream 9 64bit', debian12: 'Debian 12.0 64bit', rocky94: 'Rocky Linux 9.4 64bit', debian11: 'Debian 11.1 64bit', debian10: 'Debian 10.2 64bit'
            },
            cloudeka: { ubuntu2404: 'Ubuntu Server 24.04 LTS 64bit', ubuntu2204: 'Ubuntu Server 22.04 LTS 64bit' }
        },
        paymentMethod: 'qris',
        selectedPaymentImage: 'qris.svg',
        paymentMethods: {
            lynk: { name: 'Lynk.id Checkout', image: 'lynk.svg', description: 'QRIS, VA, E-Wallet, Kartu via Lynk.id' },
            qris: { name: 'QRIS VexaHost', image: 'qris.svg', description: 'Semua e-wallet & mobile banking instan' },
            bca_va: { name: 'BCA Virtual Account', image: 'va_bca.svg', description: 'Transfer otomatis 24 jam' },
            mandiri_va: { name: 'Mandiri Virtual Account', image: 'va_mandiri.svg', description: 'Transfer otomatis 24 jam' },
            bni_va: { name: 'BNI Virtual Account', image: 'va_bni.svg', description: 'Transfer otomatis 24 jam' },
            bri_va: { name: 'BRI Virtual Account', image: 'va_bri.svg', description: 'Transfer otomatis 24 jam' },
            gopay: { name: 'GoPay', image: 'gopay.svg', description: 'Aplikasi GoPay & Gojek' },
            ovo: { name: 'OVO', image: 'ewallet_ovo.svg', description: 'Aplikasi E-Wallet OVO' },
            dana: { name: 'DANA', image: 'dana.svg', description: 'Aplikasi E-Wallet DANA' },
            shopeepay: { name: 'ShopeePay', image: 'ewallet_shopeepay.svg', description: 'Scan & Saldo ShopeePay' }
        },
        qrisSvgDataUri: '{{ app(\App\Services\QrisService::class)->generateDataUri($selectedSpec ? $selectedSpec->sell_price : 80000) }}',

        // Auth & User State
        isLoggedIn: {{ auth()->check() ? 'true' : 'false' }},
        currentUser: {
            id: {{ auth()->id() ?? 'null' }},
            full_name: '{{ addslashes(auth()->user()?->full_name ?? '') }}',
            email: '{{ addslashes(auth()->user()?->email ?? '') }}',
            phone: '{{ addslashes(auth()->user()?->phone ?? '') }}'
        },
        authTab: 'register',
        registerFullName: '',
        registerUsername: '',
        registerEmail: '',
        registerPhone: '',
        registerPassword: '',
        registerPasswordConfirmation: '',
        showRegisterPassword: false,
        showRegisterConfirmPassword: false,
        loginIdentifier: '',
        loginPassword: '',
        showLoginPassword: false,
        isLoggingIn: false,
        loginError: '',
        csrfToken: '{{ csrf_token() }}',

        async refreshQris() {
            try {
                const res = await fetch('/qris/render?amount=' + this.totalPrice);
                const data = await res.json();
                if (data.svg_data_uri) {
                    this.qrisSvgDataUri = data.svg_data_uri;
                }
            } catch (e) {
                console.error('Error refreshing QRIS:', e);
            }
        },

        // Computed properties
        get selectedPaymentMethod() {
            return this.paymentMethods[this.paymentMethod] || null;
        },
        get currentSpec() {
            return this.specs[this.selectedSpec] || null;
        },
        get isAiPackage() {
            const s = this.currentSpec;
            if (!s) return false;
            return s.category === 'ai_combo' || Boolean(s.is_ai_package);
        },
        get isDatabasePackage() {
            const s = this.currentSpec;
            if (!s) return false;
            return s.category === 'managed_db' || Boolean(s.is_database_package);
        },
        get isDirectCheckout() {
            return this.isAiPackage || this.isDatabasePackage;
        },
        get dbEngineLabel() {
            const labels = {
                postgres: 'PostgreSQL 16',
                mysql: 'MySQL 8.0 / MariaDB 11',
                redis: 'Redis 7 In-Memory Cache',
                mongodb: 'MongoDB 7 Community',
                vector: 'Qdrant / pgvector (AI & RAG)'
            };
            return labels[this.dbEngine] || 'PostgreSQL 16';
        },
        get parsedFeatures() {
            const s = this.currentSpec;
            if (!s || !s.features) return [];
            if (Array.isArray(s.features)) return s.features;
            try {
                return JSON.parse(s.features);
            } catch (e) {
                return [];
            }
        },
        get currentBaseMonthlyPrice() {
            return this.currentSpec ? parseFloat(this.currentSpec.sell_price) : 80000;
        },
        get totalPrice() {
            return this.currentBaseMonthlyPrice;
        },
        get controlPanelLabel() {
            const labels = {
                none: 'Tanpa Control Panel', coolify: 'Coolify', dokploy: 'Dokploy', aapanel: 'aaPanel',
                cloudpanel: 'CloudPanel', docker: 'Docker', cyberpanel: 'CyberPanel', hestiacp: 'HestiaCP',
                hermes_agent: 'Hermes Agent', openclaw: 'OpenClaw', omniroute: 'OmniRoute', '9router': '9router',
                agent_zero: 'Agent Zero', n8n: 'n8n', ollama: 'Ollama', anythingllm: 'AnythingLLM',
                librechat: 'LibreChat', vscode_server: 'Visual Studio Code Server', gitea_forgejo: 'Gitea / Forgejo',
                uptime_kuma: 'Uptime Kuma', netdata_beszel: 'Netdata / Beszel', wordpress: 'WordPress',
                ghost: 'Ghost', strapi_directus: 'Strapi / Directus', prestashop_bagisto: 'PrestaShop / Bagisto',
                claude_opencode: 'Claude Code & OpenCode CLI', dify_ollama: 'Dify AI + Ollama RAG',
                managed_database: 'Dedicated Managed Database'
            };
            return labels[this.controlPanel] || 'Tanpa Control Panel';
        },
        
        canUseProvider(p) {
            const spec = this.currentSpec;
            if (!spec || !spec.allowed_providers) return true;
            return spec.allowed_providers.includes(p);
        },

        syncProviderWithSpec() {
            const spec = this.currentSpec;
            if (!spec) return;
            if (!this.canUseProvider(this.provider)) {
                this.provider = spec.default_provider || 'tencent';
            }
            if (this.provider === 'cloudeka') {
                this.datacenter = 'indonesia';
            }
            if (!this.operatingSystems[this.provider] || !this.operatingSystems[this.provider][this.os]) {
                this.os = Object.keys(this.operatingSystems[this.provider])[0];
            }
        },

        applyAiPackageDefaults() {
            const spec = this.currentSpec;
            if (!spec) return;
            const clean = (spec.name || 'ai-agent').toLowerCase().replace(/[^a-z0-9]/g, '-').replace(/-+/g, '-').replace(/^-|-$/g, '');
            const rnd = Math.random().toString(36).substring(2, 6);
            if (!this.vpsName || this.vpsName.startsWith('vx-ai-') || this.vpsName.startsWith('vx-db-') || this.vpsName === '') {
                this.vpsName = 'vx-ai-' + clean.substring(0, 14) + '-' + rnd;
            }
            this.controlPanel = spec.default_stack || 'vscode_server';
            this.datacenter = 'indonesia';
            this.os = 'ubuntu2404';
            if (spec.allowed_providers && spec.allowed_providers.length > 0) {
                this.provider = spec.default_provider || spec.allowed_providers[0];
            } else {
                this.provider = 'cloudeka';
            }
        },

        applyDatabaseDefaults() {
            const spec = this.currentSpec;
            if (!spec) return;
            const clean = (spec.name || 'db').toLowerCase().replace(/[^a-z0-9]/g, '-').replace(/-+/g, '-').replace(/^-|-$/g, '');
            const rnd = Math.random().toString(36).substring(2, 6);
            if (!this.vpsName || this.vpsName.startsWith('vx-db-') || this.vpsName.startsWith('vx-ai-') || this.vpsName === '') {
                this.vpsName = 'vx-db-' + clean.substring(0, 14) + '-' + rnd;
            }
            this.controlPanel = spec.default_stack || 'managed_database';
            this.datacenter = 'indonesia';
            this.os = 'ubuntu2404';
            if (!this.dbEngine) this.dbEngine = 'postgres';
            if (!this.dbManager) this.dbManager = 'cloudbeaver';
            if (spec.allowed_providers && spec.allowed_providers.length > 0) {
                this.provider = spec.default_provider || spec.allowed_providers[0];
            } else {
                this.provider = 'tencent';
            }
        },
        
        // Methods
        init() {
            // Restore draft dari localStorage jika ada
            this.restoreDraft();

            if (!this.selectedSpec && Object.keys(this.specs).length > 0) {
                this.selectedSpec = parseInt(Object.keys(this.specs)[0]);
            }
            if (this.isDirectCheckout) {
                if (this.currentSlide === 0) this.currentSlide = 1;
                if (this.isDatabasePackage) {
                    this.applyDatabaseDefaults();
                } else {
                    this.applyAiPackageDefaults();
                }
            } else {
                this.syncProviderWithSpec();
            }

            this.$watch('selectedSpec', () => {
                this.refreshQris();
                if (this.isDirectCheckout) {
                    if (this.currentSlide === 0) this.currentSlide = 1;
                    if (this.isDatabasePackage) {
                        this.applyDatabaseDefaults();
                    } else {
                        this.applyAiPackageDefaults();
                    }
                } else {
                    this.syncProviderWithSpec();
                }
                this.saveDraft();
            });

            // Watchers untuk auto-save draft ke localStorage
            this.$watch('vpsName', () => this.saveDraft());
            this.$watch('rootPassword', () => this.saveDraft());
            this.$watch('provider', () => this.saveDraft());
            this.$watch('datacenter', () => this.saveDraft());
            this.$watch('os', () => this.saveDraft());
            this.$watch('controlPanel', () => this.saveDraft());
            this.$watch('dbEngine', () => this.saveDraft());
            this.$watch('dbManager', () => this.saveDraft());
            this.$watch('billingCycle', () => this.saveDraft());
            this.$watch('paymentMethod', () => this.saveDraft());
            this.$watch('registerFullName', () => this.saveDraft());
            this.$watch('registerUsername', () => this.saveDraft());
            this.$watch('registerEmail', () => this.saveDraft());
            this.$watch('registerPhone', () => this.saveDraft());
        },

        saveDraft() {
            try {
                const draft = {
                    selectedSpec: this.selectedSpec,
                    billingCycle: this.billingCycle,
                    provider: this.provider,
                    vpsName: this.vpsName,
                    rootPassword: this.rootPassword,
                    controlPanel: this.controlPanel,
                    datacenter: this.datacenter,
                    os: this.os,
                    dbEngine: this.dbEngine,
                    dbManager: this.dbManager,
                    paymentMethod: this.paymentMethod,
                    registerFullName: this.registerFullName,
                    registerUsername: this.registerUsername,
                    registerEmail: this.registerEmail,
                    registerPhone: this.registerPhone,
                };
                localStorage.setItem('vx_checkout_draft', JSON.stringify(draft));
            } catch (e) {
                // ignore storage error
            }
        },

        restoreDraft() {
            try {
                const raw = localStorage.getItem('vx_checkout_draft');
                if (!raw) return;
                const draft = JSON.parse(raw);
                if (draft.selectedSpec && this.specs[draft.selectedSpec]) {
                    this.selectedSpec = draft.selectedSpec;
                }
                if (draft.billingCycle) this.billingCycle = draft.billingCycle;
                if (draft.provider && this.canUseProvider(draft.provider)) this.provider = draft.provider;
                if (draft.vpsName) this.vpsName = draft.vpsName;
                if (draft.rootPassword) this.rootPassword = draft.rootPassword;
                if (draft.controlPanel) this.controlPanel = draft.controlPanel;
                if (draft.datacenter) this.datacenter = draft.datacenter;
                if (draft.os) this.os = draft.os;
                if (draft.dbEngine) this.dbEngine = draft.dbEngine;
                if (draft.dbManager) this.dbManager = draft.dbManager;
                if (draft.paymentMethod) this.paymentMethod = draft.paymentMethod;
                if (!this.isLoggedIn) {
                    if (draft.registerFullName) this.registerFullName = draft.registerFullName;
                    if (draft.registerUsername) this.registerUsername = draft.registerUsername;
                    if (draft.registerEmail) this.registerEmail = draft.registerEmail;
                    if (draft.registerPhone) this.registerPhone = draft.registerPhone;
                }
            } catch (e) {
                console.warn('Could not restore draft:', e);
            }
        },

        clearDraft() {
            try {
                localStorage.removeItem('vx_checkout_draft');
            } catch (e) {}
        },

        async performQuickLogin() {
            this.loginError = '';
            if (!this.loginIdentifier || !this.loginPassword) {
                this.loginError = 'Harap isi email/username dan password akun Anda.';
                showAlert(this.loginError, { icon: 'warning', title: 'Data Login Belum Lengkap' });
                return false;
            }

            this.isLoggingIn = true;
            try {
                const res = await fetch('/checkout/quick-login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken
                    },
                    body: JSON.stringify({
                        login: this.loginIdentifier,
                        password: this.loginPassword
                    })
                });

                const data = await res.json().catch(() => null);

                if (!res.ok) {
                    this.loginError = data?.message || 'Login gagal. Silakan periksa kembali email/username dan password.';
                    showAlert(this.loginError, { icon: 'error', title: 'Login Gagal' });
                    this.isLoggingIn = false;
                    return false;
                }

                if (data?.requires_2fa) {
                    this.saveDraft();
                    window.location.href = data.redirect || '{{ route('two-factor.challenge') }}';
                    return false;
                }

                if (data?.success && data?.user) {
                    this.currentUser = data.user;
                    this.isLoggedIn = true;
                    if (data.csrf_token) {
                        this.csrfToken = data.csrf_token;
                        document.querySelector('meta[name="csrf-token"]')?.setAttribute('content', data.csrf_token);
                    }
                    this.loginPassword = '';
                    this.saveDraft();
                    showAlert('Berhasil masuk sebagai ' + (this.currentUser.full_name || this.currentUser.email), {
                        icon: 'success',
                        title: 'Login Berhasil'
                    });
                    return true;
                }
                return false;
            } catch (err) {
                console.error('Quick login error:', err);
                this.loginError = 'Terjadi kesalahan jaringan saat mencoba masuk. Silakan coba lagi.';
                showAlert(this.loginError, { icon: 'error', title: 'Login Gagal' });
                return false;
            } finally {
                this.isLoggingIn = false;
            }
        },

        selectStack(type) {
            this.stackType = type;
            this.controlPanel = type === 'none' ? 'none' : (type === 'control_panel' ? 'coolify' : 'hermes_agent');
        },

        selectProvider(provider) {
            if (!this.canUseProvider(provider)) {
                const spec = this.currentSpec;
                const specName = spec ? spec.name : 'Paket ini';
                if (provider === 'tencent') {
                    showAlert(specName + ' hanya tersedia di provider Cloudeka by Lintasarta (Datacenter Indonesia).');
                } else {
                    showAlert(specName + ' hanya tersedia di provider Tencent Cloud.');
                }
                return;
            }
            this.provider = provider;
            if (provider === 'cloudeka') this.datacenter = 'indonesia';
            if (!this.operatingSystems[provider][this.os]) this.os = Object.keys(this.operatingSystems[provider])[0];
        },
        
        formatRupiah(number) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(number);
        },
        
        previousSlide() {
            if (this.isDirectCheckout) {
                if (this.currentSlide === 3) {
                    this.currentSlide = 2;
                } else if (this.currentSlide === 2) {
                    this.currentSlide = 1;
                } else if (this.currentSlide === 1) {
                    window.location.href = this.isDatabasePackage ? "{{ route('home') }}#database-packages" : "{{ route('home') }}#ai-packages";
                }
                return;
            }
            if (this.currentSlide > 0) {
                this.currentSlide--;
            }
        },
        
        async nextSlide() {
            if (this.isDirectCheckout) {
                if (this.currentSlide === 1) {
                    if (!this.vpsName || !/^[A-Za-z0-9][A-Za-z0-9-]*$/.test(this.vpsName)) {
                        showAlert('Hostname / Nama Server wajib diisi (gunakan huruf, angka, dan tanda hubung).');
                        return;
                    }
                    if (!this.rootPassword || this.rootPassword.length < 8) {
                        showAlert('Root Password Server wajib diisi (minimal 8 karakter).');
                        return;
                    }
                    if (!await this.validateAccount()) {
                        return;
                    }
                }
                if (this.currentSlide === 2) {
                    if (!this.paymentMethod) {
                        showAlert('Silakan pilih salah satu metode pembayaran terlebih dahulu.');
                        return;
                    }
                    this.currentSlide = 3;
                    return;
                }
                this.currentSlide = 2;
                return;
            }

            if (this.currentSlide < 3) {
                if (this.currentSlide === 0 && !this.validateConfiguration()) {
                    return;
                }
                if (this.currentSlide === 1 && !await this.validateAccount()) {
                    return;
                }
                if (this.currentSlide === 2) {
                    if (!this.paymentMethod) {
                        showAlert('Silakan pilih salah satu metode pembayaran terlebih dahulu.');
                        return;
                    }
                }
                this.currentSlide++;
            }
        },
        
        validateConfiguration() {
            if (!this.vpsName || !/^[A-Za-z0-9][A-Za-z0-9-]*$/.test(this.vpsName)) {
                showAlert('VPS Name wajib diisi dengan huruf, angka, atau tanda hubung.');
                return false;
            }
            if (!this.rootPassword || this.rootPassword.length < 8) {
                showAlert('Root Password Server wajib diisi (minimal 8 karakter).');
                return false;
            }
            if (!this.selectedSpec) {
                showAlert('Silakan pilih paket VPS terlebih dahulu');
                return false;
            }
            if (!this.canUseProvider(this.provider)) {
                const spec = this.currentSpec;
                showAlert('Pilihan provider tidak tersedia untuk ' + (spec ? spec.name : 'paket ini') + '.');
                this.syncProviderWithSpec();
                return false;
            }
            return true;
        },
        
        async validateAccount() {
            if (this.isLoggedIn) {
                return true;
            }

            if (this.authTab === 'login') {
                if (!this.loginIdentifier || !this.loginPassword) {
                    showAlert('Silakan lengkapi Email/Username dan Password akun Anda untuk melanjutkan.');
                    return false;
                }
                // Eksekusi quick login secara otomatis di latar belakang
                const loginSuccess = await this.performQuickLogin();
                return Boolean(loginSuccess);
            }

            if (!this.registerFullName || !this.registerEmail || !this.registerPassword) {
                showAlert('Silakan lengkapi Nama Lengkap, Email, dan Password pendaftaran terlebih dahulu.');
                return false;
            }

            if (this.registerPassword.length < 8) {
                showAlert('Password pendaftaran minimal 8 karakter.');
                return false;
            }

            if (this.registerPasswordConfirmation && this.registerPassword !== this.registerPasswordConfirmation) {
                showAlert('Konfirmasi password tidak cocok dengan password yang Anda masukkan.');
                return false;
            }

            return true;
        },
        
        selectPayment(method, isActive = true) {
            if (!isActive || (method !== 'qris' && method !== 'lynk')) {
                const methodName = this.paymentMethods[method]?.name || method;
                showAlert('Metode pembayaran ' + methodName + ' sedang tidak aktif. Saat ini transaksi dapat menggunakan QRIS atau Lynk.id Checkout.', {
                    icon: 'info',
                    title: 'Metode Pembayaran Nonaktif'
                });
                return;
            }
            this.paymentMethod = method;
            this.selectedPaymentImage = this.paymentMethods[method]?.image || 'qris.svg';
        },
        
        async submitCheckout() {
            if (!this.paymentMethod) {
                showAlert('Silakan pilih metode pembayaran terlebih dahulu');
                return;
            }

            // PHASE 2 - persetujuan ketentuan wajib. Server memvalidasi ulang,
            // pemeriksaan di sini hanya agar pesannya muncul lebih cepat.
            if (!this.termsAccepted) {
                showAlert('Anda wajib menyetujui Ketentuan Layanan dan Ketentuan Penggunaan sebelum melanjutkan pemesanan');
                return;
            }

            if (this.isDatabasePackage) {
                this.applyDatabaseDefaults();
            } else if (this.isAiPackage) {
                this.applyAiPackageDefaults();
            }

            this.isSubmitting = true;
            
            try {
                const formData = {
                    _token: this.csrfToken,
                    vps_spec_id: this.selectedSpec,
                    billing_cycle: this.billingCycle,
                    provider: this.provider,
                    control_panel: this.controlPanel,
                    datacenter_location: this.datacenter,
                    os: this.os,
                    payment_method: this.paymentMethod,
                    db_engine: this.isDatabasePackage ? this.dbEngine : null,
                    db_manager: this.isDatabasePackage ? this.dbManager : null,
                    hostname: this.vpsName,
                    root_password: this.rootPassword,
                    terms_accepted: this.termsAccepted ? 1 : 0,
                    phone: this.isLoggedIn
                        ? (this.currentUser?.phone || '') 
                        : (this.registerPhone || ''),
                };

                if (!this.isLoggedIn) {
                    formData.full_name = this.registerFullName;
                    formData.username = this.registerUsername ? this.registerUsername.trim() : null;
                    formData.email = this.registerEmail;
                    formData.password = this.registerPassword;
                }

                const response = await fetch('/checkout', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken
                    },
                    body: JSON.stringify(formData)
                });

                const data = await response.json().catch(() => null);

                if (!response.ok) {
                    let errorMsg = 'Terjadi kesalahan saat memproses pesanan.';
                    if (data) {
                        if (data.message) {
                            errorMsg = data.message;
                        }
                        if (data.errors) {
                            errorMsg = Object.values(data.errors).flat().join('\n');
                        }
                    }
                    showAlert(errorMsg, {
                        icon: 'error',
                        title: 'Checkout Gagal',
                    });
                    this.isSubmitting = false;
                    return;
                }

                // Hapus draft saat checkout berhasil disubmit
                this.clearDraft();

                if (data && data.redirect_url) {
                    window.location.href = data.redirect_url;
                } else if (data && data.order_id) {
                    window.location.href = '/order/payment/' + data.order_id;
                } else {
                    window.location.href = '/dashboard';
                }
            } catch (error) {
                console.error('Checkout error:', error);
                showAlert('Terjadi kesalahan jaringan atau koneksi terputus. Silakan coba lagi.', {
                    icon: 'error',
                    title: 'Checkout gagal',
                });
                this.isSubmitting = false;
            }
        }
    };
}
</script>
@endsection
