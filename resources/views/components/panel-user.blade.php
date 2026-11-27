@props(['judul' => 'Cari Lowongan', 'aktif' => 'katalog'])

@php
    $pengguna = auth()->user();
    $inisial = mb_strtoupper(mb_substr(trim($pengguna->name), 0, 1));
    $hitungNotif = static fn (): int => $pengguna->unreadNotifications()->count();
    $jumlahNotifBelumDibaca = app()->runningUnitTests()
        ? $hitungNotif()
        : Illuminate\Support\Facades\Cache::remember("user:{$pengguna->id}:unread_notif", 20, $hitungNotif);

    $tabNavigasi = [
        ['kunci' => 'katalog', 'label' => 'Beranda', 'url' => route('katalog')],
        ['kunci' => 'lamaran', 'label' => 'Lamaran', 'url' => route('lamaran.index')],
        ['kunci' => 'wawancara', 'label' => 'Wawancara', 'url' => route('wawancara.index')],
        ['kunci' => 'notifikasi', 'label' => 'Notifikasi', 'url' => route('notifikasi.index')],
        ['kunci' => 'akun', 'label' => 'Profil', 'url' => route('akun.edit')],
    ];
@endphp

<x-layout :judul="$judul . ' | Lokerku'" :panel-admin="true">
    <div class="min-h-screen px-3 py-3 text-slate-900 sm:px-6 sm:py-5 lg:px-8 lg:py-6">
        <div class="mx-auto max-w-[88rem]" data-admin-shell>

            <!-- Top Floating Glass Header Bar + Mobile Dropdown Wrapper (Sticky) -->
            <div class="sticky top-3 z-40">
                <header class="flex items-center justify-between gap-2 rounded-full border-[1.5px] border-slate-200/90 bg-white/90 px-3 py-2 shadow-sm backdrop-blur-xl sm:gap-4 sm:px-5 sm:py-2.5">
                    <!-- Brand -->
                    <a class="flex shrink-0 items-center gap-2 text-base font-extrabold tracking-tight text-slate-900 sm:text-lg" href="{{ route('katalog') }}">
                        <span class="grid size-8 place-items-center rounded-full bg-[#007AFF] text-xs font-black text-white shadow-md shadow-[#007AFF]/30 sm:size-9 sm:text-sm">L</span>
                        <span>lokerku<span class="text-[#007AFF]">.</span></span>
                    </a>

                    <!-- Right Utility Icons + Profile -->
                    <div class="flex shrink-0 items-center gap-1.5 sm:gap-2">
                        <a class="grid size-8 place-items-center rounded-full border border-slate-200/90 bg-white text-slate-600 shadow-xs transition hover:border-[#007AFF]/40 hover:bg-[#E0F2FE] hover:text-[#007AFF] sm:size-9" href="{{ route('lamaran.index') }}" title="Riwayat Lamaran" aria-label="Riwayat Lamaran">
                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3h9l3 3v15H6zM9 12h6M9 16h6"/></svg>
                        </a>
                        <a class="relative grid size-8 place-items-center rounded-full border border-slate-200/90 bg-white text-slate-600 shadow-xs transition hover:border-[#007AFF]/40 hover:bg-[#E0F2FE] hover:text-[#007AFF] sm:size-9" href="{{ route('notifikasi.index') }}" title="Notifikasi" aria-label="Notifikasi ({{ $jumlahNotifBelumDibaca }} belum dibaca)">
                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                            @if ($jumlahNotifBelumDibaca > 0)
                                <span class="absolute right-1.5 top-1.5 size-2 rounded-full bg-[#007AFF] ring-2 ring-white"></span>
                            @endif
                        </a>
                        <a class="flex items-center gap-2 rounded-full border border-slate-200/90 bg-white py-1 pl-1 pr-1 shadow-xs transition hover:border-[#007AFF]/40 sm:pr-3" href="{{ route('akun.edit') }}">
                            @if ($pengguna->foto_profil_path)
                                <img src="{{ asset('storage/' . $pengguna->foto_profil_path) }}" alt="{{ $pengguna->name }}" class="size-7 rounded-full object-cover">
                            @else
                                <span class="grid size-7 place-items-center rounded-full bg-gradient-to-br from-[#007AFF] to-sky-400 text-xs font-bold text-white" aria-hidden="true">{{ $inisial }}</span>
                            @endif
                            <span class="hidden text-xs font-semibold text-slate-700 md:inline">{{ Str::words($pengguna->name, 2, '') }}</span>
                        </a>
                        <button type="button" class="inline-flex size-8 items-center justify-center rounded-full border border-slate-200/90 bg-white text-slate-700 shadow-xs lg:hidden sm:size-9" onclick="document.getElementById('mobile-user-nav').classList.toggle('hidden')" aria-label="Buka menu navigasi">
                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
                        </button>
                    </div>
                </header>

                <!-- Mobile & Tablet Dropdown List (Right-Aligned Single Column) -->
                <nav id="mobile-user-nav" class="absolute right-0 top-full mt-2 hidden w-60 max-w-[calc(100vw-2rem)] rounded-2xl border-[1.5px] border-slate-200/90 bg-white/95 p-2 shadow-2xl backdrop-blur-2xl lg:hidden" aria-label="Menu seluler pencari kerja">
                    <div class="flex flex-col gap-1">
                        @foreach ($tabNavigasi as $tab)
                            <a @class([
                                'flex items-center rounded-xl px-3.5 py-2.5 text-xs font-bold transition',
                                'bg-[#007AFF] !text-white shadow-sm shadow-[#007AFF]/25' => $aktif === $tab['kunci'],
                                'text-slate-700 hover:bg-[#E0F2FE] hover:text-[#007AFF]' => $aktif !== $tab['kunci'],
                            ]) href="{{ $tab['url'] }}" onclick="document.getElementById('mobile-user-nav').classList.add('hidden')">{{ $tab['label'] }}</a>
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

            <!-- Body Layout: Sticky Collapsible Floating Icon Dock + Main Workspace -->
            <div class="mt-4 grid gap-4 transition-all duration-200 sm:mt-5 sm:gap-5 lg:grid-cols-[56px_minmax(0,1fr)]" data-dock-grid>
                <!-- Left Floating Icon Dock (Identical to Admin & Perusahaan) -->
                <aside class="hidden flex-col gap-3 lg:sticky lg:top-24 lg:flex lg:self-start" aria-label="Navigasi pencari kerja" data-dock-aside>
                    <div class="space-y-3">
                        <div class="flex flex-col gap-1.5 rounded-full border-[1.5px] border-slate-200/90 bg-white/85 p-1.5 shadow-md shadow-slate-900/5 backdrop-blur-xl" data-dock-capsule>
                            <button type="button" data-toggle-dock aria-expanded="false" title="Buka / Tutup Menu" aria-label="Buka atau tutup menu navigasi" class="flex h-10 w-10 items-center justify-center rounded-full text-slate-400 transition hover:bg-slate-100 hover:text-[#007AFF]" data-dock-item>
                                <svg class="size-4 shrink-0 transition-transform duration-200" data-dock-chevron viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                                <span class="hidden truncate text-xs font-bold text-slate-700" data-dock-label>Menu</span>
                            </button>
                            <a href="{{ route('katalog') }}" title="Cari Lowongan" aria-label="Cari Lowongan" @class(['flex h-10 w-10 items-center justify-center rounded-full transition', 'bg-[#007AFF] !text-white shadow-md shadow-[#007AFF]/30' => $aktif === 'katalog', 'text-slate-500 hover:bg-[#E0F2FE] hover:text-[#007AFF]' => $aktif !== 'katalog']) data-dock-item>
                                <svg class="size-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="6.2"/><path d="m20 20-4.4-4.4"/></svg>
                                <span class="hidden truncate text-xs font-semibold" data-dock-label>Beranda</span>
                            </a>
                            <a href="{{ route('lamaran.index') }}" title="Riwayat Lamaran" aria-label="Riwayat Lamaran" @class(['flex h-10 w-10 items-center justify-center rounded-full transition', 'bg-[#007AFF] !text-white shadow-md shadow-[#007AFF]/30' => $aktif === 'lamaran', 'text-slate-500 hover:bg-[#E0F2FE] hover:text-[#007AFF]' => $aktif !== 'lamaran']) data-dock-item>
                                <svg class="size-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 3h9l3 3v15H6zM9 12h6M9 16h6"/></svg>
                                <span class="hidden truncate text-xs font-semibold" data-dock-label>Lamaran</span>
                            </a>
                            <a href="{{ route('wawancara.index') }}" title="Jadwal Wawancara" aria-label="Jadwal Wawancara" @class(['flex h-10 w-10 items-center justify-center rounded-full transition', 'bg-[#007AFF] !text-white shadow-md shadow-[#007AFF]/30' => $aktif === 'wawancara', 'text-slate-500 hover:bg-[#E0F2FE] hover:text-[#007AFF]' => $aktif !== 'wawancara']) data-dock-item>
                                <svg class="size-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                                <span class="hidden truncate text-xs font-semibold" data-dock-label>Wawancara</span>
                            </a>
                        </div>

                        <div class="flex flex-col gap-1.5 rounded-full border-[1.5px] border-slate-200/90 bg-white/85 p-1.5 shadow-md shadow-slate-900/5 backdrop-blur-xl" data-dock-capsule>
                            <a href="{{ route('notifikasi.index') }}" title="Notifikasi" aria-label="Notifikasi" @class(['flex h-10 w-10 items-center justify-center rounded-full transition', 'bg-[#007AFF] !text-white shadow-md shadow-[#007AFF]/30' => $aktif === 'notifikasi', 'text-slate-500 hover:bg-[#E0F2FE] hover:text-[#007AFF]' => $aktif !== 'notifikasi']) data-dock-item>
                                <svg class="size-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                                <span class="hidden truncate text-xs font-semibold" data-dock-label>Notifikasi</span>
                            </a>
                            <a href="{{ route('akun.edit') }}" title="Profil" aria-label="Profil" @class(['flex h-10 w-10 items-center justify-center rounded-full transition', 'bg-[#007AFF] !text-white shadow-md shadow-[#007AFF]/30' => $aktif === 'akun', 'text-slate-500 hover:bg-[#E0F2FE] hover:text-[#007AFF]' => $aktif !== 'akun']) data-dock-item>
                                <svg class="size-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8.5" r="3.4"/><path d="M5 20c.8-3.4 3.4-5.2 7-5.2s6.2 1.8 7 5.2"/></svg>
                                <span class="hidden truncate text-xs font-semibold" data-dock-label>Profil</span>
                            </a>
                        </div>
                    </div>

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
