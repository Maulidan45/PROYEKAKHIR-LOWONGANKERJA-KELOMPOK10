@props(['jenisAkun', 'navbar' => false])

@if (in_array($jenisAkun, ['perusahaan', 'user', 'admin'], true) && $navbar)
    @php
        $tautanMenu = match ($jenisAkun) {
            'perusahaan' => [
                ['url' => route('dashboard.role', ['role' => 'perusahaan']), 'aktif' => request()->routeIs('dashboard.role') && request()->route('role') === 'perusahaan', 'label' => 'Dashboard', 'ikon' => '<rect x="4" y="4" width="6" height="6" rx="1"/><rect x="14" y="4" width="6" height="6" rx="1"/><rect x="4" y="14" width="6" height="6" rx="1"/><rect x="14" y="14" width="6" height="6" rx="1"/>'],
                ['url' => route('akun.edit'), 'aktif' => request()->routeIs('akun.*'), 'label' => 'Profil Perusahaan', 'ikon' => '<path d="M3 21h18M5 21V7l7-4 7 4v14"/>'],
                ['url' => route('lowongan.create'), 'aktif' => request()->routeIs('lowongan.create'), 'label' => 'Pasang Lowongan', 'ikon' => '<path d="M12 5v14M5 12h14"/>'],
                ['url' => route('lowongan.index'), 'aktif' => request()->routeIs('lowongan.index', 'lowongan.edit'), 'label' => 'Kelola Lowongan', 'ikon' => '<path d="M4 7h16v12H4zM8 7V5h8v2M4 11h16M10 15h4"/>'],
                ['url' => route('perusahaan.pelamar.index'), 'aktif' => request()->routeIs('perusahaan.pelamar.*', 'lowongan.pelamar'), 'label' => 'Daftar Pelamar', 'ikon' => '<circle cx="9" cy="8" r="3"/><path d="M3 20c.5-3 2.5-5 6-5s5.5 2 6 5M16 5a3 3 0 0 1 0 6M17 15c2.4.5 3.7 2.1 4 5"/>'],
                ['url' => route('wawancara.index'), 'aktif' => request()->routeIs('wawancara.*'), 'label' => 'Jadwal Wawancara', 'ikon' => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 11h18"/>'],
                ['url' => route('notifikasi.index'), 'aktif' => request()->routeIs('notifikasi.*'), 'label' => 'Notifikasi', 'ikon' => '<path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/>'],
            ],
            'admin' => [
                ['url' => route('dashboard.role', ['role' => 'admin']), 'aktif' => request()->routeIs('dashboard*'), 'label' => 'Ringkasan', 'ikon' => '<rect x="4" y="4" width="6" height="6" rx="1"/><rect x="14" y="4" width="6" height="6" rx="1"/><rect x="4" y="14" width="6" height="6" rx="1"/><rect x="14" y="14" width="6" height="6" rx="1"/>'],
                ['url' => route('admin.verifikasi.index'), 'aktif' => request()->routeIs('admin.verifikasi.*'), 'label' => 'Verifikasi Perusahaan', 'ikon' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9 12 2 2 4-4"/>'],
                ['url' => route('lowongan.index'), 'aktif' => request()->routeIs('lowongan.*'), 'label' => 'Kelola Lowongan', 'ikon' => '<path d="M4 7h16v12H4zM8 7V5h8v2M4 11h16M10 15h4"/>'],
                ['url' => route('kategori.index'), 'aktif' => request()->routeIs('kategori.*'), 'label' => 'Kategori', 'ikon' => '<path d="M5 5h14v14H5zM8 9h8M8 13h8M8 17h5"/>'],
                ['url' => route('admin.pengguna.index'), 'aktif' => request()->routeIs('admin.pengguna.*'), 'label' => 'Pengguna', 'ikon' => '<circle cx="12" cy="8" r="3.25"/><path d="M5 20c.7-3.2 3.1-5 7-5s6.3 1.8 7 5"/>'],
            ],
            default => [
                ['url' => route('dashboard.role', ['role' => 'user']), 'aktif' => request()->routeIs('dashboard.role') && request()->route('role') === 'user', 'label' => 'Dashboard', 'ikon' => '<rect x="4" y="4" width="6" height="6" rx="1"/><rect x="14" y="4" width="6" height="6" rx="1"/><rect x="4" y="14" width="6" height="6" rx="1"/><rect x="14" y="14" width="6" height="6" rx="1"/>'],
                ['url' => route('katalog'), 'aktif' => request()->routeIs('katalog'), 'label' => 'Cari Lowongan', 'ikon' => '<circle cx="11" cy="11" r="6"/><path d="m20 20-4.35-4.35"/>'],
                ['url' => route('lamaran.index'), 'aktif' => request()->routeIs('lamaran.*'), 'label' => 'Riwayat Lamaran', 'ikon' => '<path d="M6 3h9l3 3v15H6zM9 12h6M9 16h6"/>'],
                ['url' => route('wawancara.index'), 'aktif' => request()->routeIs('wawancara.*'), 'label' => 'Jadwal Wawancara', 'ikon' => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 11h18"/>'],
                ['url' => route('notifikasi.index'), 'aktif' => request()->routeIs('notifikasi.*'), 'label' => 'Notifikasi', 'ikon' => '<path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/>'],
                ['url' => route('akun.edit'), 'aktif' => request()->routeIs('akun.*'), 'label' => 'Profil & Akun', 'ikon' => '<circle cx="12" cy="8" r="3.25"/><path d="M5 20c.7-3.2 3.1-5 7-5s6.3 1.8 7 5"/>'],
            ],
        };
    @endphp
    <div class="panel-menu--perusahaan relative" data-sidebar data-menu-dropdown>
        <button type="button" class="grid size-10 place-items-center rounded-md border border-slate-300 bg-white text-slate-700 transition hover:border-blue-400 hover:bg-blue-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-700" data-toggle-menu aria-expanded="false" aria-controls="menu-peran" aria-label="Buka navigasi">
            <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
            <span class="teks-tersembunyi" data-sidebar-label>Buka navigasi</span>
        </button>
        <nav class="absolute right-0 top-[calc(100%+0.5rem)] z-30 grid w-[min(17rem,calc(100vw-2rem))] gap-1 rounded-lg border border-slate-200 bg-white p-2 shadow-xl" id="menu-peran" data-menu-peran aria-label="Menu {{ match ($jenisAkun) { 'perusahaan' => 'perusahaan', 'admin' => 'admin', default => 'pencari kerja' } }}" hidden>
            @foreach ($tautanMenu as $tautan)
                <a class="flex min-h-10 items-center gap-3 rounded-md px-3 py-2 text-sm font-medium text-slate-700 no-underline transition hover:bg-slate-50 hover:text-blue-800 hover:no-underline aria-[current=page]:bg-blue-50 aria-[current=page]:text-blue-800" href="{{ $tautan['url'] }}" @if ($tautan['aktif']) aria-current="page" @endif>
                    <svg class="size-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $tautan['ikon'] !!}</svg>
                    <span>{{ $tautan['label'] }}</span>
                </a>
            @endforeach
            <div class="my-1 border-t border-slate-100" aria-hidden="true"></div>
            <form action="{{ route('logout') }}" method="POST" data-kirim>
                @csrf
                <button class="flex min-h-10 w-full items-center gap-3 rounded-md px-3 py-2 text-left text-sm font-medium text-red-700 transition hover:bg-red-50" type="submit" data-teks-proses="Keluar…">
                    <svg class="size-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 4h4v16h-4M10 8l-4 4 4 4M6 12h9"/></svg>
                    <span>Keluar</span>
                </button>
            </form>
        </nav>
    </div>
@endif
