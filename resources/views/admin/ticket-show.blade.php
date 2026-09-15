@extends('layouts.admin', ['title' => 'Tanggapi Tiket #' . $ticket->id, 'headerTitle' => 'Respon Tiket Bantuan'])

@section('content')
<div class="space-y-6 max-w-3xl">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.tickets') }}" class="inline-flex items-center gap-1 text-xs text-slate-500 hover:text-black transition-colors">
            &larr; Kembali ke Daftar Tiket
        </a>
        <span class="px-2.5 py-0.5 rounded text-xs font-semibold uppercase border border-slate-300 bg-slate-50 text-slate-900">
            {{ str_replace('_', ' ', $ticket->status) }}
        </span>
    </div>

    <!-- Ticket Summary Card (No line under title) -->
    <div class="bg-white rounded-lg border border-slate-200 p-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-2">
            <div>
                <span class="font-mono-code text-xs text-slate-400 font-semibold">#TK-{{ str_pad($ticket->id, 4, '0', STR_PAD_LEFT) }}</span>
                <h1 class="text-base font-bold text-slate-900 mt-0.5">{{ $ticket->subject }}</h1>
            </div>
            <span class="text-xs text-slate-400">{{ $ticket->created_at->format('d M Y, H:i') }} WIB</span>
        </div>
        <div class="mt-4 pt-4 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
            <div class="text-slate-500">SLA: <span class="font-semibold {{ $ticket->sla_due_at && $ticket->sla_due_at->isPast() && !$ticket->first_response_at ? 'text-red-600' : 'text-slate-900' }}">{{ $ticket->first_response_at ? 'Sudah direspons ' . $ticket->first_response_at->diffForHumans() : ($ticket->sla_due_at?->diffForHumans() ?? '-') }}</span></div>
            <form method="POST" action="{{ route('admin.tickets.assign', $ticket->id) }}" class="flex items-center gap-2">@csrf<select name="assigned_to" onchange="this.form.submit()" class="px-2 py-1.5 rounded border border-slate-300 bg-white"><option value="">Belum ditugaskan</option>@foreach($admins as $admin)<option value="{{ $admin->id }}" {{ $ticket->assigned_to === $admin->id ? 'selected' : '' }}>{{ $admin->full_name }}</option>@endforeach</select></form>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-3 text-xs text-slate-600 border-t border-slate-100">
            <div>
                <span class="text-slate-400 block text-[11px]">Pelapor:</span>
                <span class="font-semibold text-slate-900">{{ $ticket->customer->full_name }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[11px]">Email:</span>
                <span class="font-mono-code text-slate-700">{{ $ticket->customer->email }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[11px]">Terkait VPS:</span>
                <span class="font-medium text-slate-900">{{ $ticket->vpsInstance ? ($ticket->vpsInstance->hostname ?? $ticket->vpsInstance->public_ip) : 'Umum' }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[11px]">Prioritas:</span>
                <span class="font-semibold uppercase text-slate-900">{{ $ticket->priority }}</span>
            </div>
        </div>
    </div>

    <!-- Messages Timeline -->
    <div class="space-y-3">
        @foreach($ticket->messages as $msg)
            <div class="rounded-lg p-4 border {{ $msg->is_admin_reply ? 'bg-slate-100 border-slate-300' : 'bg-white border-slate-200' }}">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-full flex items-center justify-center font-bold text-xs {{ $msg->is_admin_reply ? 'bg-black text-white' : 'bg-slate-300 text-slate-900' }}">
                            {{ strtoupper(substr($msg->user->full_name, 0, 1)) }}
                        </div>
                        <span class="font-semibold text-xs text-slate-900">{{ $msg->user->full_name }}</span>
                        @if($msg->is_admin_reply)
                            <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-black text-white">
                                Admin (Ryan)
                            </span>
                        @endif
                    </div>
                    <span class="text-xs text-slate-500">
                        {{ $msg->created_at->format('d M Y, H:i') }} WIB
                    </span>
                </div>
                <div class="text-xs text-slate-800 leading-relaxed whitespace-pre-line pl-8">
                    {{ $msg->message }}
                </div>
            </div>
        @endforeach
    </div>

    <!-- Reply Box (No lines under title, black button) -->
    <div class="bg-white rounded-lg border border-slate-200 p-5">
        <h3 class="font-semibold text-slate-900 text-sm mb-3">Balas Sebagai Admin VexaHost</h3>
        <form action="{{ route('admin.tickets.reply', $ticket->id) }}" method="POST" class="space-y-3 text-xs">
            @csrf
            <div>
                <textarea name="message" rows="4" required placeholder="Tuliskan petunjuk atau balasan teknis..."
                          class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:outline-none"></textarea>
            </div>

            <label class="inline-flex items-center gap-2 text-slate-600"><input type="checkbox" name="is_internal" value="1" class="rounded border-slate-300"> Catatan internal (tidak terlihat pelanggan)</label>

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-2">
                <div class="flex items-center gap-2">
                    <label class="font-medium text-slate-700">Status Tiket:</label>
                    <select name="status" class="px-2.5 py-1.5 rounded-lg border border-slate-300 font-medium focus:outline-none bg-white">
                        <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>Resolved (Selesai)</option>
                        <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>Closed (Tutup)</option>
                        <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>Open</option>
                    </select>
                </div>

                <button type="submit" class="px-5 py-2 rounded-lg bg-black hover:bg-neutral-800 text-white font-bold transition-colors">
                    Kirim Balasan Admin
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
