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
<body @class(['halaman-beranda' => $beranda, 'halaman-autentikasi' => $halamanAutentikasi, 'halaman-user' => auth()->user()?->role === 'user', 'halaman-admin' => $panelAdmin, 'halaman-perusahaan' => request()->routeIs('perusahaan.pelamar.*', 'lowongan.pelamar') || (request()->routeIs('dashboard.role') && request()->route('role') === 'perusahaan') || (auth()->user()?->role === 'perusahaan' && request()->routeIs('akun.*', 'lowongan.*'))])>
<a class="tautan-lewati" href="#main">Lewati ke konten</a>
@unless ($panelAdmin)
<header class="kepala-situs">
    <nav class="pembungkus navigasi relative" aria-label="Navigasi utama">
        <a class="merek" href="{{ route('home') }}">lokerku<span>.</span></a>
        @unless ($halamanAutentikasi)
        <ul class="tautan-nav" role="list">
            <li><a href="{{ route('home') }}" @if(request()->routeIs('home')) aria-current="page" @endif>Cari Kerja</a></li>
            <li><a href="{{ route('home', ['kategori' => '']) }}#lowongan">Kategori</a></li>
        </ul>
        <div class="aksi-navigasi">
            @auth
                @if (auth()->user()->role === 'perusahaan')
                    <x-menu-peran jenis-akun="perusahaan" :navbar="true" />
                @else
                    <x-menu-peran :jenis-akun="auth()->user()->role" :navbar="true" />
                @endif
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
        <div @class(['pemberitahuan', 'pemberitahuan-admin' => $panelAdmin]) role="status">{{ session('status') }}</div>
    @endif
    {{ $slot }}
</main>
@unless ($panelAdmin)
<footer class="kaki-situs">
    <div class="pembungkus isi-kaki">
        <span>© {{ date('Y') }} Lokerku</span>
    </div>
</footer>
@endunless
</body>
</html>

