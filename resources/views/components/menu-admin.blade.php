@props(['aktif' => ''])

@php
    $jumlahMenungguVerifikasi = App\Models\User::where('role', 'perusahaan')->where('status_verifikasi', 'Menunggu Verifikasi')->count();
    $tautanAdmin = [
        [
            'kunci' => 'dashboard',
            'label' => 'Ringkasan',
            'url' => route('dashboard.role', ['role' => 'admin']),
            'ikon' => '<rect x="4" y="4" width="6.5" height="6.5" rx="1.6"/><rect x="13.5" y="4" width="6.5" height="6.5" rx="1.6"/><rect x="4" y="13.5" width="6.5" height="6.5" rx="1.6"/><rect x="13.5" y="13.5" width="6.5" height="6.5" rx="1.6"/>',
        ],
        [
            'kunci' => 'verifikasi',
            'label' => 'Verifikasi Perusahaan',
            'url' => route('admin.verifikasi.index'),
            'badge' => $jumlahMenungguVerifikasi,
            'ikon' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9 12 2 2 4-4"/>',
        ],
        [
            'kunci' => 'lowongan',
            'label' => 'Kelola Lowongan',
            'url' => route('lowongan.index'),
            'ikon' => '<path d="M4 8h16v11.5H4zM8.5 8V5h7v3M4 12.5h16M10 15.5h4"/>',
        ],
        [
            'kunci' => 'kategori',
            'label' => 'Kategori',
            'url' => route('kategori.index'),
            'ikon' => '<path d="M4.5 4.5h6v6h-6zM13.5 4.5h6v6h-6zM4.5 13.5h6v6h-6zM13.5 13.5h6v6h-6z"/>',
        ],
        [
            'kunci' => 'pengguna',
            'label' => 'Pengguna',
            'url' => route('admin.pengguna.index'),
            'ikon' => '<circle cx="12" cy="8.5" r="3.4"/><path d="M5 20c.8-3.4 3.4-5.2 7-5.2s6.2 1.8 7 5.2"/>',
        ],
    ];
@endphp

<nav class="flex flex-col gap-1" aria-label="Menu admin" data-menu-peran>
    @foreach ($tautanAdmin as $tautan)
        <a @class([
            'flex items-center justify-between gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition',
            'bg-blue-600 text-white shadow-xs' => $aktif === $tautan['kunci'],
            'text-slate-600 hover:bg-slate-100 hover:text-blue-700' => $aktif !== $tautan['kunci'],
        ])
           href="{{ $tautan['url'] }}"
           @if ($aktif === $tautan['kunci']) aria-current="page" @endif>
            <span class="flex items-center gap-3">
                <svg class="size-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $tautan['ikon'] !!}</svg>
                <span>{{ $tautan['label'] }}</span>
            </span>
            @if (! empty($tautan['badge']))
                <span @class([
                    'rounded-full px-2 py-0.5 text-xs font-bold tabular-nums',
                    'bg-white text-blue-700' => $aktif === $tautan['kunci'],
                    'bg-amber-100 text-amber-800' => $aktif !== $tautan['kunci'],
                ])>{{ $tautan['badge'] }}</span>
            @endif
        </a>
    @endforeach
</nav>
