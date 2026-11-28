@props(['judul' => 'Lokerku', 'deskripsi' => 'Katalog lowongan dan akun karier Lokerku.', 'beranda' => false, 'halamanAutentikasi' => false, 'panelAdmin' => false])
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#007AFF">
    <meta name="description" content="{{ $deskripsi }}">
    <title>{{ $judul }}</title>
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('logo.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body @class(['relative min-h-screen bg-[#F8FAFC] text-slate-900 antialiased selection:bg-[#E0F2FE] selection:text-[#007AFF]', 'halaman-beranda' => $beranda, 'halaman-autentikasi' => $halamanAutentikasi, 'halaman-user' => auth()->user()?->role === 'user', 'halaman-admin' => $panelAdmin, 'halaman-perusahaan' => request()->routeIs('perusahaan.pelamar.*', 'lowongan.pelamar', 'wawancara.*') || (request()->routeIs('dashboard.role') && request()->route('role') === 'perusahaan') || (auth()->user()?->role === 'perusahaan' && request()->routeIs('akun.*', 'lowongan.*', 'lamaran.*'))])>
<!-- Soft Glowing Mesh Gradient Blobs -->
<div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden" aria-hidden="true">
    <div class="absolute -left-24 -top-24 h-[420px] w-[420px] rounded-full bg-sky-200/50 blur-3xl"></div>
    <div class="absolute right-[-6%] top-[12%] h-[380px] w-[380px] rounded-full bg-cyan-200/40 blur-3xl"></div>
    <div class="absolute bottom-[-8%] left-[28%] h-[360px] w-[360px] rounded-full bg-[#E0F2FE]/80 blur-3xl"></div>
</div>

<a class="tautan-lewati" href="#main">Lewati ke konten</a>

@unless ($panelAdmin)
<header class="sticky top-3 z-40 mx-auto w-[min(76rem,calc(100%-1.5rem))]">
    <nav class="flex min-h-[68px] items-center justify-between gap-4 rounded-full border border-white/80 bg-white/70 px-5 py-2.5 shadow-lg shadow-slate-900/5 backdrop-blur-xl sm:px-7" aria-label="Navigasi utama">
        <a class="inline-flex items-center gap-2 text-2xl font-extrabold tracking-tight text-slate-900 no-underline" href="{{ auth()->check() ? route('dashboard') : route('home') }}">
            <img src="{{ asset('logo.png') }}" alt="Lokerku" class="size-8 rounded-full object-cover shadow-sm sm:size-9">
            <span>lokerku<span class="text-[#007AFF]">.</span></span>
        </a>
        @unless ($halamanAutentikasi)
        <ul class="hidden items-center gap-1 md:flex" role="list">
            @auth
                @if (auth()->user()->role === 'user')
                    <li><a @class(['rounded-full px-4 py-2 text-sm font-semibold transition', 'bg-[#E0F2FE] text-[#007AFF]' => request()->routeIs('katalog'), 'text-slate-600 hover:bg-slate-100/80 hover:text-slate-900' => !request()->routeIs('katalog')]) href="{{ route('katalog') }}" @if(request()->routeIs('katalog')) aria-current="page" @endif>Cari Lowongan</a></li>
                    <li><a @class(['rounded-full px-4 py-2 text-sm font-semibold transition', 'bg-[#E0F2FE] text-[#007AFF]' => request()->routeIs('lamaran.*'), 'text-slate-600 hover:bg-slate-100/80 hover:text-slate-900' => !request()->routeIs('lamaran.*')]) href="{{ route('lamaran.index') }}" @if(request()->routeIs('lamaran.*')) aria-current="page" @endif>Riwayat Lamaran</a></li>
                    <li><a @class(['rounded-full px-4 py-2 text-sm font-semibold transition', 'bg-[#E0F2FE] text-[#007AFF]' => request()->routeIs('wawancara.*'), 'text-slate-600 hover:bg-slate-100/80 hover:text-slate-900' => !request()->routeIs('wawancara.*')]) href="{{ route('wawancara.index') }}" @if(request()->routeIs('wawancara.*')) aria-current="page" @endif>Wawancara</a></li>
                @elseif (auth()->user()->role === 'perusahaan')
                    <li><a @class(['rounded-full px-4 py-2 text-sm font-semibold transition', 'bg-[#E0F2FE] text-[#007AFF]' => request()->routeIs('dashboard*'), 'text-slate-600 hover:bg-slate-100/80 hover:text-slate-900' => !request()->routeIs('dashboard*')]) href="{{ route('dashboard.role', ['role' => 'perusahaan']) }}" @if(request()->routeIs('dashboard*')) aria-current="page" @endif>Dashboard</a></li>
                    <li><a @class(['rounded-full px-4 py-2 text-sm font-semibold transition', 'bg-[#E0F2FE] text-[#007AFF]' => request()->routeIs('lowongan.index', 'lowongan.create', 'lowongan.edit'), 'text-slate-600 hover:bg-slate-100/80 hover:text-slate-900' => !request()->routeIs('lowongan.index', 'lowongan.create', 'lowongan.edit')]) href="{{ route('lowongan.index') }}" @if(request()->routeIs('lowongan.index', 'lowongan.create', 'lowongan.edit')) aria-current="page" @endif>Kelola Lowongan</a></li>
                    <li><a @class(['rounded-full px-4 py-2 text-sm font-semibold transition', 'bg-[#E0F2FE] text-[#007AFF]' => request()->routeIs('perusahaan.pelamar.*', 'lowongan.pelamar'), 'text-slate-600 hover:bg-slate-100/80 hover:text-slate-900' => !request()->routeIs('perusahaan.pelamar.*', 'lowongan.pelamar')]) href="{{ route('perusahaan.pelamar.index') }}" @if(request()->routeIs('perusahaan.pelamar.*', 'lowongan.pelamar')) aria-current="page" @endif>Pelamar</a></li>
                    <li><a @class(['rounded-full px-4 py-2 text-sm font-semibold transition', 'bg-[#E0F2FE] text-[#007AFF]' => request()->routeIs('wawancara.*'), 'text-slate-600 hover:bg-slate-100/80 hover:text-slate-900' => !request()->routeIs('wawancara.*')]) href="{{ route('wawancara.index') }}" @if(request()->routeIs('wawancara.*')) aria-current="page" @endif>Wawancara</a></li>
                @elseif (auth()->user()->role === 'admin')
                    <li><a class="rounded-full bg-[#E0F2FE] px-4 py-2 text-sm font-semibold text-[#007AFF] transition hover:bg-sky-200/70" href="{{ route('dashboard.role', ['role' => 'admin']) }}">Panel Admin</a></li>
                @endif
            @endauth
        </ul>
        <div class="flex items-center gap-2.5 sm:gap-3">
            @auth
                @php($jumlahNotifBelumDibaca = auth()->user()->unreadNotifications()->count())
                <a class="relative grid size-10 place-items-center rounded-full border border-white/80 bg-white/80 text-slate-700 shadow-sm transition hover:border-[#007AFF]/40 hover:bg-[#E0F2FE] hover:text-[#007AFF]" href="{{ route('notifikasi.index') }}" aria-label="Notifikasi ({{ $jumlahNotifBelumDibaca }} belum dibaca)">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                    @if ($jumlahNotifBelumDibaca > 0)
                        <span class="absolute -right-1 -top-1 grid h-5 min-w-5 place-items-center rounded-full bg-[#007AFF] px-1 text-[11px] font-bold text-white tabular-nums">{{ $jumlahNotifBelumDibaca > 99 ? '99+' : $jumlahNotifBelumDibaca }}</span>
                    @endif
                </a>
                <x-menu-peran :jenis-akun="auth()->user()->role" :navbar="true" />
            @else
                <a class="hidden rounded-full px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-white/80 hover:text-[#007AFF] sm:inline-flex" href="{{ route('register') }}">Daftar</a>
                <a class="inline-flex items-center justify-center rounded-full bg-[#007AFF] px-5 py-2.5 text-sm font-semibold text-white shadow-md shadow-[#007AFF]/25 transition hover:bg-[#0062CC]" href="{{ route('login') }}" @if (request()->routeIs('login')) aria-current="page" @endif>Masuk</a>
            @endauth
        </div>
        @endunless
    </nav>
</header>
@endunless

<main id="main" tabindex="-1" @class(['main-admin' => $panelAdmin])>
    @if (session('status'))
        <div @class(['mx-auto mt-4 w-[min(72rem,calc(100%-2rem))] rounded-2xl border border-sky-200 bg-[#E0F2FE]/80 px-5 py-3.5 text-sm font-medium text-sky-950 backdrop-blur-md', 'max-w-none' => $panelAdmin]) role="status">{{ session('status') }}</div>
    @endif
    {{ $slot }}
</main>

@unless ($panelAdmin)
<footer class="mt-12 border-t border-slate-200/70 bg-white/60 py-7 text-sm text-slate-500 backdrop-blur-md">
    <div class="mx-auto flex w-[min(76rem,calc(100%-2rem))] flex-wrap items-center justify-between gap-4">
        <span class="font-medium text-slate-600">© {{ date('Y') }} Lokerku — Platform Pencarian &amp; Rekrutmen Lowongan Kerja</span>
        <span class="text-xs text-slate-400">Dirancang dengan pengalaman modern yang cepat &amp; bersih</span>
    </div>
</footer>
@endunless
</body>
</html>
