<div class="border border-slate-200 rounded-lg p-6">
    <div class="flex items-center justify-between mb-5">
        <div class="flex items-center gap-3">
            <span class="w-6 h-6 rounded-full bg-black text-white text-xs font-semibold flex items-center justify-center">2</span>
            <h2 class="font-semibold text-slate-900">Informasi Akun</h2>
        </div>
        @guest
            <a href="{{ route('login') }}" class="text-sm font-medium text-slate-900 underline">
                Sudah punya akun? Login
            </a>
        @endguest
    </div>

    @auth
        {{-- User is logged in --}}
        <div class="bg-slate-50 border border-slate-200 rounded-lg p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-black text-white flex items-center justify-center font-bold">
                    {{ strtoupper(substr(auth()->user()->full_name, 0, 1)) }}
                </div>
                <div>
                    <h3 class="font-semibold text-slate-900">{{ auth()->user()->full_name }}</h3>
                    <p class="text-xs text-slate-500">{{ auth()->user()->email }}</p>
                </div>
            </div>
            <p class="text-sm text-slate-600 mb-4">
                Anda akan menggunakan informasi akun ini untuk pesanan VPS. Detail login akan dikirim ke email Anda.
            </p>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Nomor WhatsApp / HP</label>
                    <input type="text" name="phone" value="{{ auth()->user()->phone ?? '' }}" placeholder="081234567890"
                           class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">VPS Name / Hostname</label>
                    <input type="text" x-model="vpsName" placeholder="misal: web-production-01"
                           class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm font-mono-code bg-slate-100 text-slate-700 focus:outline-none"
                           readonly>
                </div>
            </div>
            
            <div class="flex items-center gap-2 p-3 bg-blue-50 rounded-lg border border-blue-200">
                <svg class="w-5 h-5 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-xs text-blue-700">Pastikan email Anda aktif. Notifikasi dan kredensial server akan dikirim ke email ini.</p>
            </div>
        </div>
    @else
        {{-- Guest user registration --}}
        <div class="space-y-6">
            <div class="bg-slate-50 border border-slate-200 rounded-lg p-4 mb-4">
                <p class="text-sm text-slate-600">
                    Dengan membuat akun, Anda dapat memantau status VPS, melihat invoice, dan mengelola layanan melalui dashboard.
                </p>
            </div>
            
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Nama Lengkap *</label>
                    <input type="text" name="full_name" required value="" placeholder="Contoh: Andi Pratama"
                           class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Email Aktif *</label>
                    <input type="email" name="email" required value="" placeholder="andi@email.com"
                           class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Nomor WhatsApp / HP</label>
                    <input type="text" name="phone" value="" placeholder="081234567890"
                           class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Password *</label>
                    <input type="password" name="password" required minlength="8" placeholder="Minimal 8 karakter"
                           class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none">
                    <p class="text-xs text-slate-500 mt-1">Password untuk login ke dashboard</p>
                </div>
            </div>
            
            <div class="flex items-center gap-2 p-3 bg-blue-50 rounded-lg border border-blue-200">
                <svg class="w-5 h-5 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-xs text-blue-700">Akun akan dibuat otomatis. Anda bisa login menggunakan email dan password ini.</p>
            </div>
        </div>
    @endauth
</div>