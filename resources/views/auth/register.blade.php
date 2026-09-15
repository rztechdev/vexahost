@extends('layouts.auth', ['title' => 'Daftar Akun Baru — VexaHost'])

@section('content')
@php
    $googlePrefill = session('google_prefill', []);
    $prefilledName = old('full_name', $googlePrefill['full_name'] ?? '');
    $prefilledEmail = old('email', $googlePrefill['email'] ?? '');
    $googleId = old('google_id', $googlePrefill['google_id'] ?? '');
    $avatar = old('avatar', $googlePrefill['avatar'] ?? '');
@endphp

<div class="w-full max-w-[640px] p-4 sm:p-6 mx-auto">
    <div class="bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 shadow-sm">

        <!-- Logo & Title -->
        <div class="text-center mb-6">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2.5 mb-2">
                <img src="{{ asset('images/logo.png') }}" alt="VexaHost" class="h-9 w-auto object-contain">
                <span class="text-xl font-bold tracking-tight text-slate-900">Vexa<span class="text-[#4A6FA5]">Host</span></span>
            </a>
            <h1 class="text-lg font-bold text-slate-900">Buat Akun Baru</h1>
            <p class="text-xs text-slate-500 mt-0.5">Mulai deploy server VPS KVM berkinerja tinggi dalam hitungan menit</p>
        </div>

        <!-- Flash Alert -->
        @if(session('info'))
            <div class="mb-4 p-3 rounded-lg bg-blue-50 border border-blue-200 text-xs text-blue-800 flex items-start gap-2.5">
                <svg class="w-4 h-4 text-blue-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ session('info') }}</span>
            </div>
        @endif
        @if(session('success'))
            <div class="mb-4 p-3 rounded-lg bg-emerald-50 border border-emerald-200 text-xs text-emerald-800">
                {{ session('success') }}
            </div>
        @endif
        @if($errors->any())
            <div class="mb-4 p-3 rounded-lg bg-rose-50 border border-rose-200 text-xs text-rose-800">
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Google OAuth Button -->
        <a href="{{ route('auth.google') }}"
           class="w-full flex items-center justify-center gap-3 px-4 py-2.5 bg-white hover:bg-slate-50 text-slate-800 text-sm font-semibold border border-slate-300 rounded-lg shadow-xs transition-all">
            <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24">
                <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.66-5.17 3.66-9.17z"/>
                <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.24v3.15C3.26 21.4 7.34 24 12 24z"/>
                <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.24C.45 8.15 0 9.92 0 12s.45 3.85 1.24 5.42l4.04-3.15z"/>
                <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.34 0 3.26 2.6 1.24 6.58l4.04 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/>
            </svg>
            <span>Daftar Cepat dengan Google</span>
        </a>

        <!-- Divider -->
        <div class="relative my-4">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-slate-200"></div>
            </div>
            <div class="relative flex justify-center text-xs uppercase tracking-wider">
                <span class="bg-white px-2.5 text-slate-400 font-medium text-[11px]">atau lengkapi formulir pendaftaran</span>
            </div>
        </div>

        <!-- Register Form with Alpine State -->
        <form action="{{ route('register') }}" method="POST" class="space-y-4 text-xs sm:text-sm"
              x-data="{
                  password: '',
                  confirmPassword: '',
                  showPassword: false,
                  showConfirm: false,
                  get hasLength() { return this.password.length >= 8; },
                  get hasCase() { return /[a-z]/.test(this.password) && /[A-Z]/.test(this.password); },
                  get hasNumber() { return /\d/.test(this.password); },
                  get hasSymbol() { return /[^A-Za-z0-9]/.test(this.password); },
                  get strength() {
                      if (!this.password) return 0;
                      let s = 0;
                      if (this.hasLength) s++;
                      if (this.hasCase) s++;
                      if (this.hasNumber) s++;
                      if (this.hasSymbol) s++;
                      return s;
                  },
                  get label() {
                      if (!this.password) return 'Belum diisi';
                      switch(this.strength) {
                          case 1: return 'Sangat Lemah';
                          case 2: return 'Cukup';
                          case 3: return 'Kuat';
                          case 4: return 'Sangat Kuat';
                          default: return 'Terlalu Pendek';
                      }
                  },
                  get labelColor() {
                      switch(this.strength) {
                          case 1: return 'text-rose-600';
                          case 2: return 'text-amber-600';
                          case 3: return 'text-blue-600';
                          case 4: return 'text-emerald-600';
                          default: return 'text-slate-400';
                      }
                  }
              }">
            @csrf

            @if($googleId)
                <input type="hidden" name="google_id" value="{{ $googleId }}">
                <input type="hidden" name="avatar" value="{{ $avatar }}">
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 sm:gap-4">
                <!-- Col 1: Nama Lengkap -->
                <div>
                    <label for="full_name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama Lengkap *</label>
                    <input type="text" id="full_name" name="full_name" required autofocus value="{{ $prefilledName }}"
                           placeholder="Nama lengkap Anda"
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#4A6FA5] focus:bg-white transition-all text-xs sm:text-sm">
                </div>

                <!-- Col 2: Username -->
                <div>
                    <label for="username" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Username *</label>
                    <input type="text" id="username" name="username" required value="{{ old('username') }}"
                           placeholder="contoh: daffa_dev"
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#4A6FA5] focus:bg-white transition-all text-xs sm:text-sm">
                </div>

                <!-- Col 3: Email -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="email" class="text-xs font-bold text-slate-700 uppercase tracking-wider">Alamat Email *</label>
                        @if($googleId)
                            <span class="inline-flex items-center gap-1 text-[10px] text-emerald-700 font-semibold bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                                Google Terverifikasi
                            </span>
                        @endif
                    </div>
                    <input type="email" id="email" name="email" required value="{{ $prefilledEmail }}"
                           placeholder="nama@email.com"
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#4A6FA5] focus:bg-white transition-all text-xs sm:text-sm">
                </div>

                <!-- Col 4: Phone / WA -->
                <div>
                    <label for="phone" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nomor WhatsApp <span class="text-slate-400 font-normal lowercase">(opsional)</span></label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone') }}"
                           placeholder="081234567890"
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#4A6FA5] focus:bg-white transition-all text-xs sm:text-sm">
                </div>

                <!-- Col 5: Password with Strongmeter & Eye Toggle -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="text-xs font-bold text-slate-700 uppercase tracking-wider">Password *</label>
                        <span class="text-[11px] font-semibold" :class="labelColor" x-text="label">Belum diisi</span>
                    </div>
                    <div class="relative">
                        <input :type="showPassword ? 'text' : 'password'" id="password" name="password" required minlength="8"
                               x-model="password"
                               placeholder="Minimal 8 karakter"
                               class="w-full px-3.5 py-2.5 pr-10 bg-slate-50 border border-slate-300 rounded-lg text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#4A6FA5] focus:bg-white transition-all text-xs sm:text-sm">
                        <button type="button" @click="showPassword = !showPassword"
                                class="absolute inset-y-0 right-0 flex items-center px-3 text-slate-400 hover:text-slate-600 focus:outline-none"
                                tabindex="-1"
                                title="Tampilkan/sembunyikan password">
                            <svg x-show="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Password Strongmeter 4-Segment Bar -->
                    <div class="grid grid-cols-4 gap-1.5 mt-2">
                        <div class="h-1.5 rounded-full transition-all duration-300"
                             :class="strength >= 1 ? (strength === 1 ? 'bg-rose-500' : (strength === 2 ? 'bg-amber-500' : (strength === 3 ? 'bg-blue-500' : 'bg-emerald-500'))) : 'bg-slate-200'"></div>
                        <div class="h-1.5 rounded-full transition-all duration-300"
                             :class="strength >= 2 ? (strength === 2 ? 'bg-amber-500' : (strength === 3 ? 'bg-blue-500' : 'bg-emerald-500')) : 'bg-slate-200'"></div>
                        <div class="h-1.5 rounded-full transition-all duration-300"
                             :class="strength >= 3 ? (strength === 3 ? 'bg-blue-500' : 'bg-emerald-500') : 'bg-slate-200'"></div>
                        <div class="h-1.5 rounded-full transition-all duration-300"
                             :class="strength >= 4 ? 'bg-emerald-500' : 'bg-slate-200'"></div>
                    </div>

                    <!-- Password Requirement Checklist (Compact) -->
                    <div class="grid grid-cols-2 gap-x-2 gap-y-1 mt-2 text-[10px] text-slate-500">
                        <span class="flex items-center gap-1" :class="hasLength ? 'text-emerald-600 font-semibold' : ''">
                            <svg class="w-3 h-3 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Min. 8 karakter
                        </span>
                        <span class="flex items-center gap-1" :class="hasCase ? 'text-emerald-600 font-semibold' : ''">
                            <svg class="w-3 h-3 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Huruf besar & kecil
                        </span>
                        <span class="flex items-center gap-1" :class="hasNumber ? 'text-emerald-600 font-semibold' : ''">
                            <svg class="w-3 h-3 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Mengandung angka
                        </span>
                        <span class="flex items-center gap-1" :class="hasSymbol ? 'text-emerald-600 font-semibold' : ''">
                            <svg class="w-3 h-3 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Simbol karakter (@$!%*)
                        </span>
                    </div>
                </div>

                <!-- Col 6: Confirm Password with Eye Toggle -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password_confirmation" class="text-xs font-bold text-slate-700 uppercase tracking-wider">Konfirmasi Password *</label>
                        <template x-if="confirmPassword">
                            <span class="text-[11px] font-semibold"
                                  :class="password === confirmPassword ? 'text-emerald-600' : 'text-rose-600'"
                                  x-text="password === confirmPassword ? 'Cocok' : 'Tidak cocok'"></span>
                        </template>
                    </div>
                    <div class="relative">
                        <input :type="showConfirm ? 'text' : 'password'" id="password_confirmation" name="password_confirmation" required minlength="8"
                               x-model="confirmPassword"
                               placeholder="Ulangi password"
                               class="w-full px-3.5 py-2.5 pr-10 bg-slate-50 border border-slate-300 rounded-lg text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#4A6FA5] focus:bg-white transition-all text-xs sm:text-sm">
                        <button type="button" @click="showConfirm = !showConfirm"
                                class="absolute inset-y-0 right-0 flex items-center px-3 text-slate-400 hover:text-slate-600 focus:outline-none"
                                tabindex="-1"
                                title="Tampilkan/sembunyikan konfirmasi password">
                            <svg x-show="!showConfirm" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="showConfirm" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Terms and Privacy Checkbox (Wajib / Required) -->
            <div class="pt-2">
                <input type="hidden" name="terms" value="0">
                <label for="agree_terms_register" class="flex items-start gap-2.5 cursor-pointer select-none group">
                    <input type="checkbox"
                           id="agree_terms_register"
                           name="terms"
                           value="1"
                           {{ old('terms') ? 'checked' : '' }}
                           required
                           oninvalid="this.setCustomValidity('Wajib menyetujui Ketentuan Layanan dan Kebijakan Privasi untuk mendaftar.')"
                           oninput="this.setCustomValidity('')"
                           class="mt-0.5 h-4 w-4 rounded border-slate-300 text-[#4A6FA5] focus:ring-[#4A6FA5] focus:ring-offset-0 cursor-pointer shrink-0">
                    <span class="text-xs leading-snug text-slate-600 group-hover:text-slate-900 transition-colors">
                        Saya menyetujui
                        <a href="{{ route('terms') }}" target="_blank" class="font-semibold text-slate-900 hover:text-[#4A6FA5] underline underline-offset-2">Ketentuan Layanan</a>
                        dan
                        <a href="{{ route('privacy') }}" target="_blank" class="font-semibold text-slate-900 hover:text-[#4A6FA5] underline underline-offset-2">Kebijakan Privasi</a>
                        VexaHost. <span class="text-rose-500 font-bold" title="Wajib disetujui">*</span>
                    </span>
                </label>
                @error('terms')
                    <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-1">
                <button type="submit"
                        class="w-full py-2.5 bg-black hover:bg-neutral-800 text-white text-sm font-bold rounded-lg transition-colors shadow-xs cursor-pointer">
                    Buat Akun Sekarang
                </button>
            </div>
        </form>

        <p class="mt-5 pt-4 border-t border-slate-100 text-center text-xs text-slate-600">
            Sudah memiliki akun?
            <a href="{{ route('login') }}" class="font-bold text-slate-900 hover:text-[#4A6FA5] transition-colors">Masuk ke Akun</a>
        </p>

        <!-- Back to Dashboard / Home Button at Bottom of Card -->
        <div class="mt-4 pt-4 border-t border-slate-100 text-center">
            <a href="{{ auth()->check() ? route('dashboard.index') : route('home') }}"
               class="inline-flex items-center justify-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-900 transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Kembali ke {{ auth()->check() ? 'Dashboard' : 'Beranda' }}</span>
            </a>
        </div>

    </div>
</div>
@endsection
