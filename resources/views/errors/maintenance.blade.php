{{--
    Halaman yang tampil saat maintenance menyala.
    Dibuat berdiri sendiri (tanpa @extends) agar tetap tampil benar
    meskipun layout utama ikut terdampak.
--}}
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Sedang Pemeliharaan — {{ $brand['name'] ?? 'VexaHost' }}</title>
    <link rel="icon" type="image/png" href="{{ $brand['favicon'] ?? asset('images/logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-mono-code { font-family: 'JetBrains Mono', monospace; }
    </style>
</head>
<body class="h-full antialiased bg-slate-50 text-slate-900">
<div class="min-h-screen flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-lg">

        <div class="flex justify-center mb-8">
            <img src="{{ $brand['logo'] ?? asset('images/logo.png') }}"
                 alt="{{ $brand['name'] ?? 'VexaHost' }}"
                 class="h-10 w-auto object-contain">
        </div>

        <div class="bg-white p-5 rounded-lg border border-slate-200">

            <div class="flex items-center gap-3 mb-5">
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded text-[11px] font-semibold bg-sky-50 text-sky-800 border border-sky-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-sky-600"></span>
                    Maintenance
                </span>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Status Layanan</span>
            </div>

            <h1 class="text-xl font-extrabold text-slate-900 mb-2">
                Sedang Dalam Pemeliharaan
            </h1>

            <p class="text-sm text-slate-600 leading-relaxed mb-5">
                {{ $message ?? 'Layanan ini sedang dalam pemeliharaan. Silakan coba beberapa saat lagi.' }}
            </p>

            @if(!empty($window))
                <div class="rounded-lg border border-slate-200 bg-slate-50 p-4 mb-5">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-2">
                        Jadwal Pemeliharaan
                    </span>
                    <div class="text-sm font-bold text-slate-900 mb-1">{{ $window->title }}</div>
                    <div class="font-mono-code text-xs text-slate-600">
                        {{ $window->starts_at->timezone('Asia/Jakarta')->format('d M Y H:i') }}
                        &ndash;
                        {{ $window->ends_at->timezone('Asia/Jakarta')->format('d M Y H:i') }} WIB
                    </div>
                </div>
            @endif

            <div class="flex flex-col sm:flex-row gap-2">
                <a href="{{ route('status') }}"
                   class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-black hover:bg-neutral-800 text-white text-xs font-bold transition-colors">
                    Lihat Halaman Status
                </a>
                <a href="{{ url('/') }}"
                   class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold border border-slate-200 transition-colors">
                    Kembali ke Beranda
                </a>
            </div>

        </div>

        @if(!empty($brand['support_email']))
            <p class="text-center text-xs text-slate-500 mt-6">
                Butuh bantuan? Hubungi
                <a href="mailto:{{ $brand['support_email'] }}"
                   class="font-semibold text-slate-700 hover:text-slate-900 underline underline-offset-2">
                    {{ $brand['support_email'] }}
                </a>
            </p>
        @endif

    </div>
</div>
</body>
</html>
