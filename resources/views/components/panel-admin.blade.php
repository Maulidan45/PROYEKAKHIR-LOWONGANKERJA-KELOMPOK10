@props(['judul' => 'Beranda', 'aktif' => 'dashboard', 'adalahBeranda' => false])

@php
    $pengguna = auth()->user();
    $inisial = mb_strtoupper(mb_substr(trim($pengguna->name), 0, 1));
    $hitungMenunggu = static fn (): int => App\Models\User::where('role', 'perusahaan')
        ->where('status_verifikasi', 'Menunggu Verifikasi')
        ->count();
    $jumlahMenungguVerifikasi = app()->runningUnitTests()
        ? $hitungMenunggu()
        : Illuminate\Support\Facades\Cache::remember('admin:menunggu_verifikasi_count', 20, $hitungMenunggu);

    $tabNavigasi = [
        ['kunci' => 'dashboard', 'label' => 'Beranda', 'url' => route('dashboard.role', ['role' => 'admin'])],
        ['kunci' => 'verifikasi', 'label' => 'Verifikasi', 'url' => route('admin.verifikasi.index')],
        ['kunci' => 'lowongan', 'label' => 'Lowongan', 'url' => route('lowongan.index')],
        ['kunci' => 'kategori', 'label' => 'Kategori', 'url' => route('kategori.index')],
        ['kunci' => 'pengguna', 'label' => 'Pengguna', 'url' => route('admin.pengguna.index')],
        ['kunci' => 'katalog', 'label' => 'Katalog', 'url' => route('admin.katalog.index')],
    ];
@endphp

<x-layout :judul="$judul . ' | Admin Lokerku'" :panel-admin="true">
    <div class="min-h-screen px-3 py-3 text-slate-900 sm:px-6 sm:py-5 lg:px-8 lg:py-6">
        <!-- Seamless Admin Container (blends directly with browser background) -->
        <div class="mx-auto max-w-[88rem]" data-admin-shell>

            <!-- Top Floating Glass Header Bar + Mobile Dropdown Wrapper (Sticky) -->
            <div class="sticky top-3 z-40">
                <header class="flex items-center justify-between gap-2 rounded-full border-[1.5px] border-slate-200/90 bg-white/90 px-3 py-2 shadow-sm backdrop-blur-xl sm:gap-4 sm:px-5 sm:py-2.5">
                    <!-- Brand -->
                    <a class="flex shrink-0 items-center gap-2 text-base font-extrabold tracking-tight text-slate-900 sm:text-lg" href="{{ route('dashboard.role', ['role' => 'admin']) }}">
                        <span class="grid size-8 place-items-center rounded-full bg-[#007AFF] text-xs font-black text-white shadow-md shadow-[#007AFF]/30 sm:size-9 sm:text-sm">L</span>
                        <span>lokerku<span class="text-[#007AFF]">.</span></span>
                    </a>

                    <!-- Right Utility Icons + Profile -->
                    <div class="flex shrink-0 items-center gap-1.5 sm:gap-2">
                        <a class="grid size-8 place-items-center rounded-full border border-slate-200/90 bg-white text-slate-600 shadow-xs transition hover:border-[#007AFF]/40 hover:bg-[#E0F2FE] hover:text-[#007AFF] sm:size-9" href="{{ route('admin.katalog.index') }}" title="Katalog" aria-label="Katalog">
                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="6.2"/><path d="m20 20-4.4-4.4"/></svg>
                        </a>
                        <a class="relative grid size-8 place-items-center rounded-full border border-slate-200/90 bg-white text-slate-600 shadow-xs transition hover:border-[#007AFF]/40 hover:bg-[#E0F2FE] hover:text-[#007AFF] sm:size-9" href="{{ route('admin.verifikasi.index') }}" title="Antrean Verifikasi" aria-label="Antrean Verifikasi">
                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                            @if ($jumlahMenungguVerifikasi > 0)
                                <span class="absolute right-1.5 top-1.5 size-2 rounded-full bg-[#007AFF] ring-2 ring-white"></span>
                            @endif
                        </a>
                        <div class="flex items-center gap-2 rounded-full border border-slate-200/90 bg-white py-1 pl-1 pr-1 sm:pr-3 shadow-xs">
                            <span class="grid size-7 place-items-center rounded-full bg-gradient-to-br from-[#007AFF] to-sky-400 text-xs font-bold text-white" aria-hidden="true">{{ $inisial }}</span>
                            <span class="hidden text-xs font-semibold text-slate-700 md:inline">{{ Str::words($pengguna->name, 2, '') }}</span>
                        </div>
                        <button type="button" class="inline-flex size-8 items-center justify-center rounded-full border border-slate-200/90 bg-white text-slate-700 shadow-xs lg:hidden sm:size-9" onclick="document.getElementById('mobile-admin-nav').classList.toggle('hidden')" aria-label="Buka menu admin">
                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
                        </button>
                    </div>
                </header>

                <!-- Mobile & Tablet Dropdown List (Right-Aligned Single Column) -->
                <nav id="mobile-admin-nav" class="absolute right-0 top-full mt-2 hidden w-60 max-w-[calc(100vw-2rem)] rounded-2xl border-[1.5px] border-slate-200/90 bg-white/95 p-2 shadow-2xl backdrop-blur-2xl lg:hidden" aria-label="Menu seluler admin">
                    <div class="flex flex-col gap-1">
                        @foreach ($tabNavigasi as $tab)
                            <a @class([
                                'flex items-center rounded-xl px-3.5 py-2.5 text-xs font-bold transition',
                                'bg-[#007AFF] !text-white shadow-sm shadow-[#007AFF]/25' => $aktif === $tab['kunci'],
                                'text-slate-700 hover:bg-[#E0F2FE] hover:text-[#007AFF]' => $aktif !== $tab['kunci'],
                            ]) href="{{ $tab['url'] }}" onclick="document.getElementById('mobile-admin-nav').classList.add('hidden')">{{ $tab['label'] }}</a>
                        @endforeach
                    </div>
                    <div class="mt-2 border-t border-slate-200/70 pt-2">
                        <form action="{{ route('logout') }}" method="POST" data-kirim>
                            @csrf
                            <button class="flex w-full items-center rounded-xl px-3.5 py-2 text-left text-xs font-bold text-rose-600 transition hover:bg-rose-50" type="submit">Keluar</button>
                        </form>
                    </div>
                </nav>
            </div>

            <!-- Compact Right-Aligned Action Row (No bulky page title headings) -->
            <div class="mt-4 flex flex-wrap items-center justify-end gap-2 px-1 sm:mt-5">
                <span class="inline-flex min-h-9 items-center gap-2 rounded-full border-[1.5px] border-slate-200/90 bg-white/85 px-3.5 text-xs font-semibold text-slate-700 shadow-xs backdrop-blur-md sm:min-h-10 sm:px-4">
                    <svg class="size-3.5 text-[#007AFF]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                    {{ now()->locale('id')->translatedFormat('d M Y') }}
                </span>
                @isset($aksi)
                    {{ $aksi }}
                @endisset
            </div>

            <!-- Body Layout: Collapsible Floating Icon Dock + Main Bento Workspace -->
            <div class="mt-4 grid gap-4 transition-all duration-200 sm:mt-5 sm:gap-5 lg:grid-cols-[56px_minmax(0,1fr)]" data-dock-grid>
                <!-- Left Floating Icon Dock (Desktop / Laptop, Sticky & Collapsible) -->
                <aside class="hidden flex-col gap-3 lg:sticky lg:top-24 lg:flex lg:self-start" aria-label="Pintasan ikon admin" data-dock-aside>
                    <div class="space-y-3">
                        <!-- Group 1: Core Admin + Toggle Button -->
                        <div class="flex flex-col gap-1.5 rounded-full border-[1.5px] border-slate-200/90 bg-white/85 p-1.5 shadow-md shadow-slate-900/5 backdrop-blur-xl" data-dock-capsule>
                            <button type="button" data-toggle-dock aria-expanded="false" title="Buka / Tutup Menu" aria-label="Buka atau tutup menu navigasi" class="flex h-10 w-10 items-center justify-center rounded-full text-slate-400 transition hover:bg-slate-100 hover:text-[#007AFF]" data-dock-item>
                                <svg class="size-4 shrink-0 transition-transform duration-200" data-dock-chevron viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                                <span class="hidden truncate text-xs font-bold text-slate-700" data-dock-label>Menu</span>
                            </button>
                            <a href="{{ route('dashboard.role', ['role' => 'admin']) }}" title="Beranda" aria-label="Beranda" @class(['flex h-10 w-10 items-center justify-center rounded-full transition', 'bg-[#007AFF] !text-white shadow-md shadow-[#007AFF]/30' => $aktif === 'dashboard', 'text-slate-500 hover:bg-[#E0F2FE] hover:text-[#007AFF]' => $aktif !== 'dashboard']) data-dock-item>
                                <svg class="size-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="4" width="6.5" height="6.5" rx="1.6"/><rect x="13.5" y="4" width="6.5" height="6.5" rx="1.6"/><rect x="4" y="13.5" width="6.5" height="6.5" rx="1.6"/><rect x="13.5" y="13.5" width="6.5" height="6.5" rx="1.6"/></svg>
                                <span class="hidden truncate text-xs font-semibold" data-dock-label>Beranda</span>
                            </a>
                            <a href="{{ route('admin.verifikasi.index') }}" title="Verifikasi" aria-label="Verifikasi" @class(['flex h-10 w-10 items-center justify-center rounded-full transition', 'bg-[#007AFF] !text-white shadow-md shadow-[#007AFF]/30' => $aktif === 'verifikasi', 'text-slate-500 hover:bg-[#E0F2FE] hover:text-[#007AFF]' => $aktif !== 'verifikasi']) data-dock-item>
                                <svg class="size-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9 12 2 2 4-4"/></svg>
                                <span class="hidden truncate text-xs font-semibold" data-dock-label>Verifikasi</span>
                            </a>
                            <a href="{{ route('lowongan.index') }}" title="Lowongan" aria-label="Lowongan" @class(['flex h-10 w-10 items-center justify-center rounded-full transition', 'bg-[#007AFF] !text-white shadow-md shadow-[#007AFF]/30' => $aktif === 'lowongan', 'text-slate-500 hover:bg-[#E0F2FE] hover:text-[#007AFF]' => $aktif !== 'lowongan']) data-dock-item>
                                <svg class="size-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 8h16v11.5H4zM8.5 8V5h7v3M4 12.5h16M10 15.5h4"/></svg>
                                <span class="hidden truncate text-xs font-semibold" data-dock-label>Lowongan</span>
                            </a>
                            <a href="{{ route('kategori.index') }}" title="Kategori" aria-label="Kategori" @class(['flex h-10 w-10 items-center justify-center rounded-full transition', 'bg-[#007AFF] !text-white shadow-md shadow-[#007AFF]/30' => $aktif === 'kategori', 'text-slate-500 hover:bg-[#E0F2FE] hover:text-[#007AFF]' => $aktif !== 'kategori']) data-dock-item>
                                <svg class="size-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4.5 4.5h6v6h-6zM13.5 4.5h6v6h-6zM4.5 13.5h6v6h-6zM13.5 13.5h6v6h-6z"/></svg>
                                <span class="hidden truncate text-xs font-semibold" data-dock-label>Kategori</span>
                            </a>
                        </div>

                        <!-- Group 2: Users & In-Admin Katalog -->
                        <div class="flex flex-col gap-1.5 rounded-full border-[1.5px] border-slate-200/90 bg-white/85 p-1.5 shadow-md shadow-slate-900/5 backdrop-blur-xl" data-dock-capsule>
                            <a href="{{ route('admin.pengguna.index') }}" title="Pengguna" aria-label="Pengguna" @class(['flex h-10 w-10 items-center justify-center rounded-full transition', 'bg-[#007AFF] !text-white shadow-md shadow-[#007AFF]/30' => $aktif === 'pengguna', 'text-slate-500 hover:bg-[#E0F2FE] hover:text-[#007AFF]' => $aktif !== 'pengguna']) data-dock-item>
                                <svg class="size-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8.5" r="3.4"/><path d="M5 20c.8-3.4 3.4-5.2 7-5.2s6.2 1.8 7 5.2"/></svg>
                                <span class="hidden truncate text-xs font-semibold" data-dock-label>Pengguna</span>
                            </a>
                            <a href="{{ route('admin.katalog.index') }}" title="Katalog" aria-label="Katalog" @class(['flex h-10 w-10 items-center justify-center rounded-full transition', 'bg-[#007AFF] !text-white shadow-md shadow-[#007AFF]/30' => $aktif === 'katalog', 'text-slate-500 hover:bg-[#E0F2FE] hover:text-[#007AFF]' => $aktif !== 'katalog']) data-dock-item>
                                <svg class="size-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="6.2"/><path d="m20 20-4.4-4.4"/></svg>
                                <span class="hidden truncate text-xs font-semibold" data-dock-label>Katalog</span>
                            </a>
                        </div>
                    </div>

                    <!-- Group 3: Logout -->
                    <div class="flex flex-col gap-1.5 rounded-full border-[1.5px] border-slate-200/90 bg-white/85 p-1.5 shadow-md shadow-slate-900/5 backdrop-blur-xl" data-dock-capsule>
                        <form action="{{ route('logout') }}" method="POST" data-kirim>
                            @csrf
                            <button class="flex h-10 w-10 items-center justify-center rounded-full text-rose-500 transition hover:bg-rose-50" type="submit" title="Keluar" aria-label="Keluar" data-dock-item>
                                <svg class="size-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 4h4v16h-4M10 8l-4 4 4 4M6 12h9"/></svg>
                                <span class="hidden truncate text-xs font-semibold" data-dock-label>Keluar</span>
                            </button>
                        </form>
                    </div>
                </aside>

                <!-- Main Content Slot -->
                <div class="min-w-0 space-y-4 sm:space-y-5">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</x-layout>
