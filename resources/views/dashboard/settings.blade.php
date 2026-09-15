@extends('layouts.dashboard', ['title' => 'Pengaturan Akun', 'headerTitle' => 'Profil & Pengaturan Akun'])

@section('content')
<div class="space-y-6 max-w-3xl">
    <!-- Channel & Account Info (No line under title) -->
    <div class="bg-white rounded-lg border border-slate-200 p-5 flex items-center justify-between">
        <div>
            <span class="text-xs font-medium text-slate-500 block">Jalur Pendaftaran</span>
            <div class="flex items-center gap-2 mt-1">
                @if($user->channel === 'shopee')
                    <span class="px-2 py-0.5 rounded text-xs font-semibold uppercase border border-slate-300 bg-slate-50 text-slate-900">
                        Shopee (Order: {{ $user->shopee_order_id ?? 'N/A' }})
                    </span>
                @else
                    <span class="px-2 py-0.5 rounded text-xs font-semibold uppercase border border-slate-300 bg-slate-50 text-slate-900">
                        Website Direct
                    </span>
                @endif
                <span class="text-xs text-slate-500">Terdaftar {{ $user->created_at->format('d M Y') }}</span>
            </div>
        </div>
        <div class="text-right">
            <span class="text-xs text-slate-400 font-medium">Username</span>
            <p class="font-mono-code font-bold text-slate-900 text-sm">&#64;{{ $user->username }}</p>
        </div>
    </div>

    <!-- Edit Profile Form (No lines under headings) -->
    <div class="bg-white rounded-lg border border-slate-200 p-6">
        <h3 class="text-base font-bold text-slate-900 mb-4">Informasi Profil Akun</h3>

        <form action="{{ route('dashboard.settings.profile') }}" method="POST" class="space-y-4 text-xs">
            @csrf
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-medium text-slate-700 mb-1">Nama Lengkap *</label>
                    <input type="text" name="full_name" required value="{{ old('full_name', $user->full_name) }}"
                           class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:outline-none">
                </div>

                <div>
                    <label class="block font-medium text-slate-700 mb-1">Alamat Email</label>
                    <input type="email" value="{{ $user->email }}" disabled
                           class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-slate-50 text-slate-500 cursor-not-allowed">
                </div>

                <div>
                    <label class="block font-medium text-slate-700 mb-1">Nomor HP / WhatsApp</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="081234567890"
                           class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:outline-none">
                </div>

                <div>
                    <label class="block font-medium text-slate-700 mb-1">Instansi / Perusahaan</label>
                    <input type="text" name="company" value="{{ old('company', $user->company) }}" placeholder="Kampus / Perusahaan"
                           class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block font-medium text-slate-700 mb-1">Alamat Lengkap</label>
                <textarea name="address" rows="2" placeholder="Alamat domisili..."
                          class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:outline-none">{{ old('address', $user->address) }}</textarea>
            </div>

            <div class="pt-2 flex justify-end">
                <button type="submit" class="px-5 py-2.5 rounded-lg bg-black hover:bg-neutral-800 text-white font-bold transition-colors">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    <!-- Password Change Form (No lines under headings) -->
    <div class="bg-white rounded-lg border border-slate-200 p-6">
        <h3 class="text-base font-bold text-slate-900 mb-4">Ganti Password Akun</h3>

        <form action="{{ route('dashboard.settings.password') }}" method="POST" class="space-y-4 text-xs">
            @csrf
            <div class="grid sm:grid-cols-3 gap-4">
                <div>
                    <label class="block font-medium text-slate-700 mb-1">Password Saat Ini *</label>
                    <input type="password" name="current_password" required placeholder="••••••••"
                           class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:outline-none">
                </div>

                <div>
                    <label class="block font-medium text-slate-700 mb-1">Password Baru *</label>
                    <input type="password" name="new_password" required minlength="8" placeholder="Min 8 karakter"
                           class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:outline-none">
                </div>

                <div>
                    <label class="block font-medium text-slate-700 mb-1">Konfirmasi Password Baru *</label>
                    <input type="password" name="new_password_confirmation" required minlength="8" placeholder="Konfirmasi"
                           class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:outline-none">
                </div>
            </div>

            <div class="pt-2 flex justify-end">
                <button type="submit" class="px-5 py-2.5 rounded-lg bg-black hover:bg-neutral-800 text-white font-bold transition-colors">
                    Perbarui Password
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
