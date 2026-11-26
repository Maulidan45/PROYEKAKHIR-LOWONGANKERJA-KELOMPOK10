@props(['aktif' => ''])

@php
    $tautanAdmin = [
        [
            'kunci' => 'dashboard',
            'label' => 'Ringkasan',
            'url' => route('dashboard.role', ['role' => 'admin']),
            'ikon' => '<rect x="4" y="4" width="6.5" height="6.5" rx="1.6"/><rect x="13.5" y="4" width="6.5" height="6.5" rx="1.6"/><rect x="4" y="13.5" width="6.5" height="6.5" rx="1.6"/><rect x="13.5" y="13.5" width="6.5" height="6.5" rx="1.6"/>',
        ],
        [
            'kunci' => 'lowongan',
            'label' => 'Kelola lowongan',
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
            'kunci' => 'akun',
            'label' => 'Akun',
            'url' => route('dashboard.role', ['role' => 'admin']).'#akun-saya',
            'ikon' => '<circle cx="12" cy="8.5" r="3.4"/><path d="M5 20c.8-3.4 3.4-5.2 7-5.2s6.2 1.8 7 5.2"/>',
        ],
    ];
@endphp

<nav class="flex flex-col gap-1 p-2" aria-label="Menu admin" data-menu-peran>
    @foreach ($tautanAdmin as $tautan)
        <a @class(['menu-admin-tautan', 'menu-admin-tautan-aktif' => $aktif === $tautan['kunci']])
           href="{{ $tautan['url'] }}"
           @if ($aktif === $tautan['kunci']) aria-current="page" @endif>
            <svg class="size-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $tautan['ikon'] !!}</svg>
            <span>{{ $tautan['label'] }}</span>
        </a>
    @endforeach
</nav>
