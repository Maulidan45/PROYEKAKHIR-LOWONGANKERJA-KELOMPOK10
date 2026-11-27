@props(['judul', 'deskripsi' => null, 'label' => 'Administrator', 'aktif' => 'dashboard'])

@php
    $pengguna = auth()->user();
    $inisial = mb_strtoupper(mb_substr(trim($pengguna->name), 0, 1));
@endphp

<x-layout :judul="$judul . ' | Admin Lokerku'" :panel-admin="true">
    <div class="min-h-screen bg-slate-50 text-slate-900">
        <!-- Mobile Header -->
        <header class="sticky top-0 z-30 flex items-center justify-between border-b border-slate-200 bg-white px-4 py-3 lg:hidden">
            <a class="text-xl font-extrabold tracking-tight text-blue-700" href="{{ route('dashboard.role', ['role' => 'admin']) }}">
                lokerku<span class="text-orange-500">.</span>
                <span class="ml-1.5 rounded bg-blue-50 px-2 py-0.5 text-xs font-semibold text-blue-700">Admin</span>
            </a>
            <button type="button" class="inline-flex size-10 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-700 hover:bg-slate-50" onclick="document.getElementById('sidebar-admin-drawer').classList.toggle('hidden')" aria-label="Buka menu admin">
                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </header>

        <div class="mx-auto grid max-w-7xl lg:grid-cols-[260px_minmax(0,1fr)]">
            <!-- Sidebar -->
            <aside id="sidebar-admin-drawer" class="hidden border-b border-slate-200 bg-white p-4 lg:sticky lg:top-0 lg:flex lg:h-screen lg:flex-col lg:border-b-0 lg:border-r lg:p-5" aria-label="Panel navigasi admin">
                <div class="mb-6 hidden items-center justify-between lg:flex">
                    <a class="text-2xl font-extrabold tracking-tight text-blue-700" href="{{ route('dashboard.role', ['role' => 'admin']) }}">
                        lokerku<span class="text-orange-500">.</span>
                    </a>
                    <span class="rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-semibold text-blue-700">Admin</span>
                </div>

                <div class="mb-5 flex items-center gap-3 rounded-xl border border-slate-100 bg-slate-50 p-3">
                    <span class="grid size-10 shrink-0 place-items-center rounded-full bg-blue-600 text-sm font-bold text-white" aria-hidden="true">{{ $inisial }}</span>
                    <div class="min-w-0">
                        <strong class="block truncate text-sm font-semibold text-slate-900">{{ $pengguna->name }}</strong>
                        <span class="block truncate text-xs text-slate-500">{{ $pengguna->email }}</span>
                    </div>
                </div>

                <x-menu-admin :aktif="$aktif" />

                <div class="mt-auto space-y-1.5 border-t border-slate-100 pt-4">
                    <a class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-blue-700" href="{{ route('katalog') }}">
                        <svg class="size-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="6.2"/><path d="m20 20-4.4-4.4"/></svg>
                        <span>Lihat katalog</span>
                    </a>
                    <form action="{{ route('logout') }}" method="POST" data-kirim>
                        @csrf
                        <button class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-left text-sm font-medium text-red-700 transition hover:bg-red-50" type="submit" data-teks-proses="Keluar…">
                            <svg class="size-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 4h4v16h-4M10 8l-4 4 4 4M6 12h9"/></svg>
                            <span>Keluar</span>
                        </button>
                    </form>
                </div>
            </aside>

            <!-- Main Content -->
            <div class="min-w-0 p-4 sm:p-6 lg:p-8">
                <header class="mb-6 flex flex-col justify-between gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-xs sm:flex-row sm:items-center sm:p-6">
                    <div class="min-w-0">
                        <p class="text-xs font-bold tracking-wider text-blue-700 uppercase">{{ $label }}</p>
                        <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">{{ $judul }}</h1>
                        @if ($deskripsi)
                            <p class="mt-1 text-sm text-slate-600">{{ $deskripsi }}</p>
                        @endif
                    </div>
                    @isset($aksi)
                        <div class="flex shrink-0 flex-wrap items-center gap-2">{{ $aksi }}</div>
                    @endisset
                </header>

                {{ $slot }}
            </div>
        </div>
    </div>
</x-layout>
