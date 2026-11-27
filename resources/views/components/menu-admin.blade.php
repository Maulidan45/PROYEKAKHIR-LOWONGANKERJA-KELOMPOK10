@props(['aktif' => ''])

@php
    $jumlahMenungguVerifikasi = App\Models\User::where('role', 'perusahaan')->where('status_verifikasi', 'Menunggu Verifikasi')->count();
    $tautanAdmin = [
        [
            'kunci' => 'dashboard',
            'label' => 'Beranda',
            'url' => route('dashboard.role', ['role' => 'admin']),
            'warna' => 'bg-[#007AFF] text-white',
            'ikon' => '<rect x="4" y="4" width="6.5" height="6.5" rx="1.6"/><rect x="13.5" y="4" width="6.5" height="6.5" rx="1.6"/><rect x="4" y="13.5" width="6.5" height="6.5" rx="1.6"/><rect x="13.5" y="13.5" width="6.5" height="6.5" rx="1.6"/>',
        ],
        [
            'kunci' => 'verifikasi',
            'label' => 'Verifikasi',
            'url' => route('admin.verifikasi.index'),
            'badge' => $jumlahMenungguVerifikasi,
            'warna' => 'bg-emerald-500 text-white',
            'ikon' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9 12 2 2 4-4"/>',
        ],
        [
            'kunci' => 'lowongan',
            'label' => 'Lowongan',
            'url' => route('lowongan.index'),
            'warna' => 'bg-sky-500 text-white',
            'ikon' => '<path d="M4 8h16v11.5H4zM8.5 8V5h7v3M4 12.5h16M10 15.5h4"/>',
        ],
        [
            'kunci' => 'kategori',
            'label' => 'Kategori',
            'url' => route('kategori.index'),
            'warna' => 'bg-indigo-500 text-white',
            'ikon' => '<path d="M4.5 4.5h6v6h-6zM13.5 4.5h6v6h-6zM4.5 13.5h6v6h-6zM13.5 13.5h6v6h-6z"/>',
        ],
        [
            'kunci' => 'pengguna',
            'label' => 'Pengguna',
            'url' => route('admin.pengguna.index'),
            'warna' => 'bg-amber-500 text-white',
            'ikon' => '<circle cx="12" cy="8.5" r="3.4"/><path d="M5 20c.8-3.4 3.4-5.2 7-5.2s6.2 1.8 7 5.2"/>',
        ],
    ];
@endphp

<nav class="flex flex-col gap-1" aria-label="Menu admin" data-menu-peran>
    @foreach ($tautanAdmin as $tautan)
        @php($sedangAktif = $aktif === $tautan['kunci'])
        <a @class([
            'flex items-center justify-between gap-3 rounded-2xl px-3 py-2.5 text-sm font-semibold transition',
            'bg-[#007AFF] text-white shadow-md shadow-[#007AFF]/25' => $sedangAktif,
            'text-slate-700 hover:bg-white/80 hover:text-[#007AFF]' => ! $sedangAktif,
        ])
           href="{{ $tautan['url'] }}"
           @if ($sedangAktif) aria-current="page" @endif>
            <span class="flex items-center gap-3">
                <span @class([
                    'grid size-7 shrink-0 place-items-center rounded-xl transition',
                    'bg-white/20 text-white' => $sedangAktif,
                    $tautan['warna'] => ! $sedangAktif,
                ])>
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $tautan['ikon'] !!}</svg>
                </span>
                <span>{{ $tautan['label'] }}</span>
            </span>
            @if (! empty($tautan['badge']))
                <span @class([
                    'rounded-full px-2 py-0.5 text-xs font-bold tabular-nums',
                    'bg-white text-[#007AFF]' => $sedangAktif,
                    'bg-amber-100 text-amber-800' => ! $sedangAktif,
                ])>{{ $tautan['badge'] }}</span>
            @endif
        </a>
    @endforeach
</nav>
