@props(['judul', 'deskripsi' => null, 'label' => 'Administrator', 'aktif' => 'dashboard'])

@php
    $pengguna = auth()->user();
    $inisial = mb_strtoupper(mb_substr(trim($pengguna->name), 0, 1));
@endphp

<x-layout :judul="'$judul | Admin Lokerku'">
    <div class="kanvas-admin">
        <div class="pembungkus tata-admin">

            <aside class="sidebar-admin" data-sidebar-admin aria-label="Panel navigasi admin">
                <div class="brand-admin">
                    <a class="brand-admin-merek" href="{{ route('dashboard.role', ['role' => 'admin']) }}">
                        lokerku<span aria-hidden="true">.</span>
                    </a>
                    <span class="brand-admin-lencana">Admin</span>
                </div>

                <div class="brand-admin-identitas">
                    <span class="avatar-admin" aria-hidden="true">{{ $inisial }}</span>
                    <div class="min-w-0">
                        <strong>{{ $pengguna->name }}</strong>
                        <span>{{ $pengguna->email }}</span>
                    </div>
                </div>

                <x-menu-admin :aktif="$aktif" />

                <div class="mt-auto flex flex-col gap-2 p-2 pt-4">
                    <a class="menu-admin-tautan" href="{{ route('katalog') }}">
                        <svg class="size-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="6.2"/><path d="m20 20-4.4-4.4"/></svg>
                        <span>Lihat katalog</span>
                    </a>
                    <form action="{{ route('logout') }}" method="POST" data-kirim>
                        @csrf
                        <button class="menu-admin-tautan menu-admin-keluar w-full" type="submit" data-teks-proses="Keluar…">
                            <svg class="size-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 4h4v16h-4M10 8l-4 4 4 4M6 12h9"/></svg>
                            <span>Keluar</span>
                        </button>
                    </form>
                </div>
            </aside>

            <div class="min-w-0">
                <header class="kepala-admin">
                    <div class="min-w-0">
                        <p class="label-admin">{{ $label }}</p>
                        <h1 class="judul-admin">{{ $judul }}</h1>
                        @if ($deskripsi)
                            <p class="deskripsi-admin">{{ $deskripsi }}</p>
                        @endif
                    </div>
                    @if (! $slot->isEmpty())
                        <div class="aksi-admin">{{ $slot }}</div>
                    @endif
                </header>

                {{ $konten }}
            </div>
        </div>
    </div>
</x-layout>
