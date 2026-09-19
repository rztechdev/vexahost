@extends('layouts.dashboard', ['title' => 'Session Aktif', 'headerTitle' => 'Session Aktif', 'backUrl' => route('security.settings'), 'backLabel' => 'Kembali ke Pengaturan Keamanan'])

@section('content')
<div class="max-w-5xl space-y-5">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold">Session aktif</h2>
            <p class="text-sm text-slate-500 mt-1">Cabut akses perangkat yang tidak Anda kenali.</p>
        </div>
        <form method="POST" action="{{ route('security.sessions.revoke-all') }}" class="flex gap-2">
            @csrf
            <input type="password" name="password" required placeholder="Password" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <button class="rounded-lg border border-rose-300 px-3 py-2 text-sm font-semibold text-rose-700">Cabut semua lainnya</button>
        </form>
    </div>
    <div class="bg-white border border-slate-200 rounded-xl divide-y">
        @forelse($sessions as $session)
            <div class="p-5 flex items-center justify-between gap-4">
                <div>
                    <p class="font-semibold text-sm">{{ $session->user_agent ?: 'Perangkat tidak diketahui' }} @if($session->is_current)<span class="ml-2 rounded-full bg-emerald-100 px-2 py-1 text-xs text-emerald-700">Session ini</span>@endif</p>
                    <p class="text-xs text-slate-500 mt-1">{{ $session->ip_address ?: '-' }} · {{ $session->last_activity?->timezone('Asia/Jakarta')->format('d M Y H:i') }}</p>
                </div>
                @unless($session->is_current)
                    <form method="POST" action="{{ route('security.sessions.revoke', $session->id) }}" data-confirm="Cabut session perangkat ini?">
                        <button class="text-sm font-semibold text-rose-700 underline">Cabut</button>
                    </form>
                @endunless
            </div>
        @empty
            <div class="p-8 text-center text-sm text-slate-500">Tidak ada session aktif.</div>
        @endforelse
    </div>
</div>
@endsection
