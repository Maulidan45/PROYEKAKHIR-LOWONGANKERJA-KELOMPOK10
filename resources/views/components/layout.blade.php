@props(['judul' => 'Lokerku', 'deskripsi' => 'Katalog lowongan dan akun karier Lokerku.', 'beranda' => false, 'halamanAutentikasi' => false, 'panelAdmin' => false])
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#ffffff">
    <meta name="description" content="{{ $deskripsi }}">
    <title>{{ $judul }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body @class(['halaman-beranda' => $beranda, 'halaman-autentikasi' => $halamanAutentikasi, 'halaman-user' => auth()->user()?->role === 'user', 'halaman-admin' => $panelAdmin, 'halaman-perusahaan' => request()->routeIs('perusahaan.pelamar.*', 'lowongan.pelamar', 'wawancara.*') || (request()->routeIs('dashboard.role') && request()->route('role') === 'perusahaan') || (auth()->user()?->role === 'perusahaan' && request()->routeIs('akun.*', 'lowongan.*', 'lamaran.*'))])>
<a class="tautan-lewati" href="#main">Lewati ke konten</a>
@unless ($panelAdmin)
<header class="kepala-situs bg-white">
    <nav class="pembungkus navigasi relative" aria-label="Navigasi utama">
        <a class="merek" href="{{ auth()->check() ? route('dashboard') : route('home') }}">lokerku<span>.</span></a>
        @unless ($halamanAutentikasi)
        <ul class="tautan-nav hidden md:flex" role="list">
            @auth
                @if (auth()->user()->role === 'user')
                    <li><a href="{{ route('katalog') }}" @if(request()->routeIs('katalog')) aria-current="page" @endif>Cari Lowongan</a></li>
                    <li><a href="{{ route('lamaran.index') }}" @if(request()->routeIs('lamaran.*')) aria-current="page" @endif>Riwayat Lamaran</a></li>
                    <li><a href="{{ route('wawancara.index') }}" @if(request()->routeIs('wawancara.*')) aria-current="page" @endif>Wawancara</a></li>
                @elseif (auth()->user()->role === 'perusahaan')
                    <li><a href="{{ route('dashboard.role', ['role' => 'perusahaan']) }}" @if(request()->routeIs('dashboard*')) aria-current="page" @endif>Dashboard</a></li>
                    <li><a href="{{ route('lowongan.index') }}" @if(request()->routeIs('lowongan.index', 'lowongan.create', 'lowongan.edit')) aria-current="page" @endif>Kelola Lowongan</a></li>
                    <li><a href="{{ route('perusahaan.pelamar.index') }}" @if(request()->routeIs('perusahaan.pelamar.*', 'lowongan.pelamar')) aria-current="page" @endif>Pelamar</a></li>
                    <li><a href="{{ route('wawancara.index') }}" @if(request()->routeIs('wawancara.*')) aria-current="page" @endif>Wawancara</a></li>
                @endif
            @else
                <li><a href="{{ route('home') }}" @if(request()->routeIs('home')) aria-current="page" @endif>Cari Kerja</a></li>
                <li><a href="{{ route('home') }}#lowongan">Kategori</a></li>
            @endauth
        </ul>
        <div class="aksi-navigasi">
            @auth
                @php($jumlahNotifBelumDibaca = auth()->user()->unreadNotifications()->count())
                <a class="relative grid size-10 place-items-center rounded-md border border-slate-200 bg-white text-slate-700 transition hover:border-blue-400 hover:bg-blue-50 hover:text-blue-700" href="{{ route('notifikasi.index') }}" aria-label="Notifikasi ({{ $jumlahNotifBelumDibaca }} belum dibaca)">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                    @if ($jumlahNotifBelumDibaca > 0)
                        <span class="absolute -right-1.5 -top-1.5 grid min-w-5 h-5 place-items-center rounded-full bg-blue-600 px-1 text-[11px] font-bold text-white tabular-nums">{{ $jumlahNotifBelumDibaca > 99 ? '99+' : $jumlahNotifBelumDibaca }}</span>
                    @endif
                </a>
                <x-menu-peran :jenis-akun="auth()->user()->role" :navbar="true" />
            @else
                <a href="{{ route('login') }}" @if (request()->routeIs('login')) aria-current="page" @endif>Masuk</a>
                <a class="tombol" href="{{ route('register') }}">Daftar Gratis</a>
            @endauth
        </div>
        @endunless
    </nav>
</header>
@endunless
<main id="main" tabindex="-1" @class(['main-admin' => $panelAdmin])>
    @if (session('status'))
        <div @class(['mx-auto mt-4 w-[min(72rem,calc(100%-2rem))] rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-sm font-medium text-blue-900', 'max-w-none' => $panelAdmin]) role="status">{{ session('status') }}</div>
    @endif
    {{ $slot }}
</main>
@unless ($panelAdmin)
<footer class="kaki-situs">
    <div class="pembungkus isi-kaki">
        <span>© {{ date('Y') }} Lokerku — Platform Pencarian &amp; Rekrutmen Lowongan Kerja</span>
    </div>
</footer>
@endunless
</body>
</html>
