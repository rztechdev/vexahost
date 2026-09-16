<div class="border border-slate-200 rounded-xl p-6 sm:p-7 bg-white shadow-xs">
    {{-- Header Slide 2 --}}
    <div class="flex items-center justify-between pb-4 mb-6 border-b border-slate-100">
        <div class="flex items-center gap-3">
            <span class="w-7 h-7 rounded-full bg-black text-white text-xs font-bold flex items-center justify-center">2</span>
            <div>
                <h2 class="font-bold text-slate-900 text-base sm:text-lg">Informasi Akun</h2>
                <p class="text-xs text-slate-500 mt-0.5">Identitas pemilik layanan &amp; akses portal dashboard VexaHost</p>
            </div>
        </div>
        <template x-if="isLoggedIn">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                Sudah Masuk
            </span>
        </template>
    </div>

    {{-- KONDISI 1: User Sudah Login (Baik dari awal maupun via Quick Login) --}}
    <div x-show="isLoggedIn" class="space-y-6" style="display: none;">
        <div class="bg-slate-50 border border-slate-200 rounded-xl p-5 sm:p-6 space-y-4">
            <div class="flex items-center justify-between flex-wrap gap-3">
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-full bg-black text-white flex items-center justify-center font-bold text-lg shadow-xs"
                         x-text="currentUser.full_name ? currentUser.full_name.charAt(0).toUpperCase() : 'U'">
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="font-bold text-slate-900 text-base" x-text="currentUser.full_name || 'Pengguna VexaHost'"></h3>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-black text-white">Akun Aktif</span>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5" x-text="currentUser.email"></p>
                    </div>
                </div>

                <a href="{{ route('logout') }}" 
                   onclick="event.preventDefault(); document.getElementById('checkout-logout-form').submit();"
                   class="text-xs text-rose-600 hover:text-rose-700 font-semibold px-3 py-1.5 rounded-lg border border-rose-200 hover:bg-rose-50 transition-colors">
                    Keluar / Ganti Akun
                </a>
            </div>

            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed pt-2 border-t border-slate-200">
                Pesanan VPS ini akan otomatis dihubungkan ke akun Anda. Invoice dan kredensial akses server dikirimkan ke email terdaftar di atas.
            </p>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Nomor WhatsApp / HP (Untuk Notifikasi)</label>
                    <input type="text" x-model="currentUser.phone" name="logged_in_phone" placeholder="081234567890"
                           class="w-full px-3 py-2.5 rounded-lg border border-slate-200 text-sm focus:border-black focus:ring-1 focus:ring-black">
                    <p class="text-[11px] text-slate-400 mt-1">Notifikasi aktivasi VPS akan dikirim juga ke nomor ini.</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Hostname / VPS Name</label>
                    <input type="text" :value="vpsName || '-'" readonly
                           class="w-full px-3 py-2.5 rounded-lg border border-slate-200 text-sm font-mono-code bg-slate-100 text-slate-600 focus:outline-none">
                    <p class="text-[11px] text-slate-400 mt-1">Ditetapkan pada konfigurasi Slide 1.</p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 p-3.5 bg-blue-50/80 rounded-lg border border-blue-200 text-blue-800 text-xs">
                <svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Akun Anda siap. Klik tombol <strong>"Lanjut ke Pilih Pembayaran"</strong> di bawah untuk memilih metode pembayaran.</span>
            </div>
        </div>
    </div>

    {{-- KONDISI 2: User Belum Login (Tamu / Guest) --}}
    <div x-show="!isLoggedIn" class="space-y-6">

        {{-- Segmented Tab Switcher (Buat Akun Baru vs Sudah Punya Akun) --}}
        <div class="flex items-center p-1 bg-slate-100 rounded-xl border border-slate-200 w-full">
            <button type="button" 
                    @click="authTab = 'register'" 
                    :class="authTab === 'register' ? 'bg-white text-slate-900 shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900 font-medium'"
                    class="flex-1 py-2.5 rounded-lg text-xs sm:text-sm transition-all flex items-center justify-center gap-2">
                <svg class="w-4 h-4 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
                <span>1. Buat Akun Baru</span>
            </button>
            <button type="button" 
                    @click="authTab = 'login'" 
                    :class="authTab === 'login' ? 'bg-white text-slate-900 shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900 font-medium'"
                    class="flex-1 py-2.5 rounded-lg text-xs sm:text-sm transition-all flex items-center justify-center gap-2">
                <svg class="w-4 h-4 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                </svg>
                <span>2. Sudah Punya Akun (Masuk)</span>
            </button>
        </div>

        {{-- TAB 1: FORM PENDAFTARAN AKUN BARU --}}
        <div x-show="authTab === 'register'" class="space-y-5">
            <div class="bg-slate-50 border border-slate-200 rounded-lg p-3.5 sm:p-4 text-xs sm:text-sm text-slate-600 leading-relaxed">
                Lengkapi formulir pendaftaran di bawah ini. Akun VexaHost Anda akan dibuat otomatis saat checkout selesai untuk mengelola server dan memantau invoice.
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- Nama Lengkap --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Nama Lengkap <span class="text-rose-600">*</span>
                    </label>
                    <input type="text" x-model="registerFullName" placeholder="Contoh: Andi Pratama"
                           class="w-full px-3 py-2.5 rounded-lg border border-slate-200 text-sm focus:border-black focus:ring-1 focus:ring-black">
                </div>

                {{-- Username (Opsional / Otomatis) --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Username <span class="text-slate-400 font-normal">(opsional)</span>
                    </label>
                    <input type="text" x-model="registerUsername" placeholder="contoh: andipratama"
                           class="w-full px-3 py-2.5 rounded-lg border border-slate-200 text-sm font-mono-code focus:border-black focus:ring-1 focus:ring-black">
                    <p class="text-[11px] text-slate-400 mt-1">Kosongkan jika ingin dibuatkan otomatis oleh sistem.</p>
                </div>

                {{-- Email Aktif --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Alamat Email Aktif <span class="text-rose-600">*</span>
                    </label>
                    <input type="email" x-model="registerEmail" placeholder="andi@email.com"
                           class="w-full px-3 py-2.5 rounded-lg border border-slate-200 text-sm focus:border-black focus:ring-1 focus:ring-black">
                    <p class="text-[11px] text-slate-400 mt-1">Kredensial dan tagihan akan dikirimkan ke email ini.</p>
                </div>

                {{-- Nomor WhatsApp / HP --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Nomor WhatsApp / HP
                    </label>
                    <input type="text" x-model="registerPhone" placeholder="081234567890"
                           class="w-full px-3 py-2.5 rounded-lg border border-slate-200 text-sm focus:border-black focus:ring-1 focus:ring-black">
                    <p class="text-[11px] text-slate-400 mt-1">Untuk notifikasi darurat &amp; status aktivasi VPS.</p>
                </div>

                {{-- Password Akun --}}
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="text-xs font-semibold text-slate-700 flex items-center gap-1">
                            <span>Password Akun</span>
                            <span class="text-rose-600">*</span>
                        </label>
                        <span class="text-[11px] text-slate-400">Min. 8 karakter</span>
                    </div>
                    <div class="relative">
                        <input :type="showRegisterPassword ? 'text' : 'password'" 
                               x-model="registerPassword" 
                               placeholder="Minimal 8 karakter"
                               class="w-full pl-3 pr-10 py-2.5 rounded-lg border border-slate-200 text-sm focus:border-black focus:ring-1 focus:ring-black">
                        <button type="button" 
                                @click="showRegisterPassword = !showRegisterPassword" 
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600"
                                tabindex="-1">
                            <svg x-show="!showRegisterPassword" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="showRegisterPassword" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Konfirmasi Password --}}
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="text-xs font-semibold text-slate-700 flex items-center gap-1">
                            <span>Konfirmasi Password</span>
                            <span class="text-rose-600">*</span>
                        </label>
                        <template x-if="registerPasswordConfirmation">
                            <span class="text-[11px] font-semibold" 
                                  :class="registerPassword === registerPasswordConfirmation ? 'text-emerald-600' : 'text-rose-600'" 
                                  x-text="registerPassword === registerPasswordConfirmation ? '✓ Cocok' : '✗ Beda'"></span>
                        </template>
                    </div>
                    <div class="relative">
                        <input :type="showRegisterConfirmPassword ? 'text' : 'password'" 
                               x-model="registerPasswordConfirmation" 
                               placeholder="Ketik ulang password"
                               class="w-full pl-3 pr-10 py-2.5 rounded-lg border border-slate-200 text-sm focus:border-black focus:ring-1 focus:ring-black">
                        <button type="button" 
                                @click="showRegisterConfirmPassword = !showRegisterConfirmPassword" 
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600"
                                tabindex="-1">
                            <svg x-show="!showRegisterConfirmPassword" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="showRegisterConfirmPassword" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Divider atau opsi Google --}}
            <div class="pt-2">
                <div class="relative my-3">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-slate-200"></div>
                    </div>
                    <div class="relative flex justify-center text-xs uppercase tracking-wider">
                        <span class="bg-white px-2.5 text-slate-400 font-medium text-[11px]">atau cara cepat</span>
                    </div>
                </div>

                <a href="{{ route('auth.google') }}?redirect={{ urlencode(request()->fullUrl()) }}" 
                   @click="saveDraft()"
                   class="w-full flex items-center justify-center gap-2.5 px-4 py-2.5 bg-white hover:bg-slate-50 text-slate-800 text-xs sm:text-sm font-semibold border border-slate-300 rounded-lg shadow-2xs transition-all">
                    <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.66-5.17 3.66-9.17z"/>
                        <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.24v3.15C3.26 21.4 7.34 24 12 24z"/>
                        <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.24C.45 8.15 0 9.92 0 12s.45 3.85 1.24 5.42l4.04-3.15z"/>
                        <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.34 0 3.26 2.6 1.24 6.58l4.04 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/>
                    </svg>
                    <span>Lanjutkan / Daftar Cepat dengan Google</span>
                </a>
            </div>
        </div>

        {{-- TAB 2: FORM MASUK (QUICK LOGIN INLINE) --}}
        <div x-show="authTab === 'login'" class="space-y-5" style="display: none;">
            <div class="bg-blue-50/70 border border-blue-200 rounded-lg p-3.5 sm:p-4 text-xs sm:text-sm text-blue-800 leading-relaxed flex items-start gap-2.5">
                <svg class="w-4 h-4 text-blue-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Silakan masuk ke akun VexaHost Anda. <strong>Konfigurasi Slide 1 tidak akan hilang</strong> dan Anda langsung diarahkan ke langkah pembayaran.</span>
            </div>

            {{-- Error Message Alert --}}
            <template x-if="loginError">
                <div class="p-3 rounded-lg bg-rose-50 border border-rose-200 text-xs text-rose-700 flex items-center gap-2">
                    <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span x-text="loginError"></span>
                </div>
            </template>

            <div class="space-y-4 max-w-lg mx-auto bg-slate-50 p-5 rounded-xl border border-slate-200">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Email atau Username Terdaftar <span class="text-rose-600">*</span>
                    </label>
                    <input type="text" x-model="loginIdentifier" @keydown.enter="performQuickLogin()"
                           placeholder="nama@email.com atau username"
                           class="w-full px-3 py-2.5 rounded-lg border border-slate-200 text-sm bg-white focus:border-black focus:ring-1 focus:ring-black">
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="text-xs font-semibold text-slate-700 flex items-center gap-1">
                            <span>Password Akun</span>
                            <span class="text-rose-600">*</span>
                        </label>
                        <a href="{{ route('password.request') }}" target="_blank" class="text-[11px] text-slate-500 hover:text-black hover:underline">
                            Lupa password?
                        </a>
                    </div>
                    <div class="relative">
                        <input :type="showLoginPassword ? 'text' : 'password'" 
                               x-model="loginPassword" 
                               @keydown.enter="performQuickLogin()"
                               placeholder="Masukkan password Anda"
                               class="w-full pl-3 pr-10 py-2.5 rounded-lg border border-slate-200 text-sm bg-white focus:border-black focus:ring-1 focus:ring-black">
                        <button type="button" 
                                @click="showLoginPassword = !showLoginPassword" 
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600"
                                tabindex="-1">
                            <svg x-show="!showLoginPassword" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="showLoginPassword" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Submit Button Quick Login --}}
                <div class="pt-2">
                    <button type="button" 
                            @click="performQuickLogin()" 
                            :disabled="isLoggingIn"
                            class="w-full py-2.5 px-4 rounded-lg bg-black hover:bg-neutral-800 text-white text-sm font-bold transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 shadow-xs">
                        <svg x-show="isLoggingIn" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <svg x-show="!isLoggingIn" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        <span x-text="isLoggingIn ? 'Memverifikasi...' : 'Masuk ke Akun Saya'"></span>
                    </button>
                    <p class="text-[11px] text-slate-400 text-center mt-2">Tips: Anda juga dapat langsung menekan tombol <strong>"Lanjut ke Pilih Pembayaran"</strong> di bawah setelah mengisi form.</p>
                </div>

                {{-- Google Auth Link --}}
                <div class="pt-1">
                    <div class="relative my-2.5">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-slate-200"></div>
                        </div>
                        <div class="relative flex justify-center text-xs uppercase tracking-wider">
                            <span class="bg-slate-50 px-2 text-slate-400 font-medium text-[10px]">atau masuk lewat</span>
                        </div>
                    </div>

                    <a href="{{ route('auth.google') }}?redirect={{ urlencode(request()->fullUrl()) }}" 
                       @click="saveDraft()"
                       class="w-full flex items-center justify-center gap-2 px-3 py-2 bg-white hover:bg-slate-100 text-slate-800 text-xs font-semibold border border-slate-300 rounded-lg shadow-2xs transition-all">
                        <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.66-5.17 3.66-9.17z"/>
                            <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.24v3.15C3.26 21.4 7.34 24 12 24z"/>
                            <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.24C.45 8.15 0 9.92 0 12s.45 3.85 1.24 5.42l4.04-3.15z"/>
                            <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.34 0 3.26 2.6 1.24 6.58l4.04 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/>
                        </svg>
                        <span>Masuk dengan Akun Google</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Hidden logout form if user wants to switch account on checkout --}}
<form id="checkout-logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
    @csrf
</form>