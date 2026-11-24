@props(['jenisAkun', 'navbar' => false])

@if (in_array($jenisAkun, ['perusahaan', 'user'], true) && $navbar)
    @php
        $tautanMenu = $jenisAkun === 'perusahaan' ? [
            ['url' => route('dashboard.role', ['role' => 'perusahaan']), 'aktif' => request()->routeIs('dashboard.role') && request()->route('role') === 'perusahaan', 'label' => 'Dashboard', 'ikon' => '<rect x="4" y="4" width="6" height="6" rx="1"/><rect x="14" y="4" width="6" height="6" rx="1"/><rect x="4" y="14" width="6" height="6" rx="1"/><rect x="14" y="14" width="6" height="6" rx="1"/>'],
            ['url' => route('lowongan.index'), 'aktif' => request()->routeIs('lowongan.index', 'lowongan.edit', 'lowongan.create'), 'label' => 'Lowongan saya', 'ikon' => '<path d="M4 7h16v12H4zM8 7V5h8v2M4 11h16M10 15h4"/>'],
            ['url' => route('perusahaan.pelamar.index'), 'aktif' => request()->routeIs('perusahaan.pelamar.*', 'lowongan.pelamar'), 'label' => 'Pelamar', 'ikon' => '<circle cx="9" cy="8" r="3"/><path d="M3 20c.5-3 2.5-5 6-5s5.5 2 6 5M16 5a3 3 0 0 1 0 6M17 15c2.4.5 3.7 2.1 4 5"/>'],
            ['url' => route('akun.edit'), 'aktif' => request()->routeIs('akun.*'), 'label' => 'Akun', 'ikon' => '<circle cx="12" cy="8" r="3.25"/><path d="M5 20c.7-3.2 3.1-5 7-5s6.3 1.8 7 5"/>'],
        ] : [
            ['url' => route('dashboard.role', ['role' => 'user']), 'aktif' => request()->routeIs('dashboard.role') && request()->route('role') === 'user', 'label' => 'Dashboard', 'ikon' => '<rect x="4" y="4" width="6" height="6" rx="1"/><rect x="14" y="4" width="6" height="6" rx="1"/><rect x="4" y="14" width="6" height="6" rx="1"/><rect x="14" y="14" width="6" height="6" rx="1"/>'],
            ['url' => route('lamaran.index'), 'aktif' => request()->routeIs('lamaran.*'), 'label' => 'Lamaran saya', 'ikon' => '<path d="M6 3h9l3 3v15H6zM9 12h6M9 16h6"/>'],
            ['url' => route('akun.edit'), 'aktif' => request()->routeIs('akun.*'), 'label' => 'Akun', 'ikon' => '<circle cx="12" cy="8" r="3.25"/><path d="M5 20c.7-3.2 3.1-5 7-5s6.3 1.8 7 5"/>'],
        ];
    @endphp
    <div class="panel-menu--perusahaan relative" data-sidebar data-menu-dropdown>
            <button type="button" class="grid size-10 place-items-center rounded-md border border-slate-300 bg-white text-slate-700 transition hover:border-blue-400 hover:bg-blue-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-700" data-toggle-menu aria-expanded="false" aria-controls="menu-peran" aria-label="Buka navigasi">
                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
                <span class="teks-tersembunyi" data-sidebar-label>Buka navigasi</span>
            </button>
            <nav class="absolute right-0 top-[calc(100%+0.5rem)] z-20 grid w-[min(16rem,calc(100vw-2rem))] gap-1 rounded-lg border border-slate-200 bg-white p-2 shadow-xl" id="menu-peran" data-menu-peran aria-label="Menu {{ $jenisAkun === 'perusahaan' ? 'perusahaan' : 'pencari kerja' }}" hidden>
                @foreach ($tautanMenu as $tautan)
                    <a class="flex min-h-11 items-center gap-3 rounded-md px-3 py-2 text-sm font-medium text-slate-700 no-underline transition hover:bg-slate-50 hover:text-blue-800 hover:no-underline aria-[current=page]:bg-blue-50 aria-[current=page]:text-blue-800" href="{{ $tautan['url'] }}" @if ($tautan['aktif']) aria-current="page" @endif>
                        <svg class="size-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $tautan['ikon'] !!}</svg>
                        <span>{{ $tautan['label'] }}</span>
                    </a>
                @endforeach
                <div class="my-1 border-t border-slate-100" aria-hidden="true"></div>
                <form action="{{ route('logout') }}" method="POST" data-kirim>
                    @csrf
                    <button class="flex min-h-11 w-full items-center gap-3 rounded-md px-3 py-2 text-left text-sm font-medium text-slate-700 transition hover:bg-slate-50 hover:text-blue-800" type="submit" data-teks-proses="Keluar…">
                        <svg class="size-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 4h4v16h-4M10 8l-4 4 4 4M6 12h9"/></svg>
                        <span>Keluar</span>
                    </button>
                </form>
            </nav>
    </div>
@elseif ($jenisAkun !== 'perusahaan')
    <aside class="panel-menu" data-sidebar>
        <button type="button" class="tombol-menu" data-toggle-menu aria-expanded="true" aria-controls="menu-peran" aria-label="Tutup navigasi">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 5h16M4 12h16M4 19h16M8 5v14"/></svg>
            <span class="teks-tersembunyi" data-sidebar-label>Tutup navigasi</span>
        </button>
        <nav id="menu-peran" data-menu-peran aria-label="Menu {{ $jenisAkun }}">
            <a href="{{ route('dashboard') }}" title="Dashboard" @if (request()->routeIs('dashboard*')) aria-current="page" @endif>
                <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="4" width="6" height="6" rx="1"/><rect x="14" y="4" width="6" height="6" rx="1"/><rect x="4" y="14" width="6" height="6" rx="1"/><rect x="14" y="14" width="6" height="6" rx="1"/></svg>
                <span>Dashboard</span>
            </a>
            @if ($jenisAkun === 'admin')
                <a href="{{ route('lowongan.index') }}" title="Kelola lowongan" @if (request()->routeIs('lowongan.*')) aria-current="page" @endif><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16v12H4zM8 7V5h8v2M4 11h16M10 15h4"/></svg><span>Kelola lowongan</span></a>
                <a href="{{ route('kategori.index') }}" title="Kategori" @if (request()->routeIs('kategori.*')) aria-current="page" @endif><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 5h14v14H5zM8 9h8M8 13h8M8 17h5"/></svg><span>Kategori</span></a>
                <a href="{{ route('dashboard.role', ['role' => $jenisAkun]) }}#akun-saya" title="Akun"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="8" r="3"/><path d="M5 20c.7-3.2 3.1-5 7-5s6.3 1.8 7 5"/></svg><span>Akun</span></a>
            @else
                <a href="{{ route('home') }}" title="Cari lowongan"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="10.5" cy="10.5" r="5.5"/><path d="m15 15 4 4"/></svg><span>Cari lowongan</span></a>
                <a href="{{ route('lamaran.index') }}" title="Lamaran saya" @if (request()->routeIs('lamaran.*')) aria-current="page" @endif><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 3h9l3 3v15H6zM9 12h6M9 16h6"/></svg><span>Lamaran saya</span></a>
            @endif
        </nav>
    </aside>
@endif