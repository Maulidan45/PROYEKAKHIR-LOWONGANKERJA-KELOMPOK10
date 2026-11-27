<x-panel-user judul="Cari Lowongan" aktif="katalog">
    <!-- Top Search Bar (Clean, No Bulky Outer Card on Mobile) -->
    <form class="flex flex-col gap-2 lg:flex-row lg:items-center lg:rounded-2xl lg:border lg:border-slate-200/90 lg:bg-white/95 lg:p-2 lg:shadow-[0_10px_30px_-10px_rgba(15,23,42,0.08)]" method="GET" action="{{ route('katalog') }}">
        <div class="flex flex-1 items-center gap-2 rounded-2xl border border-slate-200/90 bg-white/95 px-3 py-1.5 shadow-xs lg:rounded-none lg:border-0 lg:bg-transparent lg:py-1.5 lg:shadow-none">
            <svg class="size-4 shrink-0 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="6.2"/><path d="m20 20-4.4-4.4"/></svg>
            <label class="sr-only" for="cari-user-katalog">Posisi atau perusahaan</label>
            <input id="cari-user-katalog" type="search" name="kata_kunci" value="{{ $penyaring['kata_kunci'] ?? '' }}" class="w-full !min-h-0 !border-0 !bg-transparent !p-0 text-xs text-slate-800 placeholder:text-slate-400 focus:outline-none sm:text-sm" placeholder="Posisi, perusahaan, kata kunci…">
            <button type="submit" class="inline-flex h-8 shrink-0 items-center justify-center rounded-xl bg-[#007AFF] px-4 text-xs font-bold text-white shadow-sm shadow-[#007AFF]/25 transition hover:bg-[#0062CC] lg:hidden">Cari</button>
        </div>

        <div class="hidden h-7 w-px bg-slate-200 lg:block" aria-hidden="true"></div>

        <div class="grid grid-cols-2 gap-2 lg:flex lg:items-center lg:gap-0">
            <div class="flex items-center gap-1.5 rounded-xl border border-slate-200/90 bg-white/95 px-2.5 py-2 shadow-xs lg:w-48 lg:rounded-none lg:border-0 lg:bg-transparent lg:px-3 lg:py-1.5 lg:shadow-none">
                <svg class="size-3.5 shrink-0 text-slate-400 sm:size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M19 10c0 5.5-7 11-7 11S5 15.5 5 10a7 7 0 1 1 14 0Z"/><circle cx="12" cy="10" r="2.2"/></svg>
                <label class="sr-only" for="lokasi-user-katalog">Lokasi</label>
                <select id="lokasi-user-katalog" name="lokasi" class="w-full !min-h-0 !border-0 !bg-transparent !p-0 text-xs text-slate-700 focus:outline-none sm:text-sm">
                    <option value="">Semua lokasi</option>
                    @foreach ($daftarLokasi as $lokasi)
                        <option value="{{ $lokasi }}" @selected(($penyaring['lokasi'] ?? '') === $lokasi)>{{ $lokasi }}</option>
                    @endforeach
                </select>
            </div>

            <div class="hidden h-7 w-px bg-slate-200 lg:block" aria-hidden="true"></div>

            <div class="flex items-center gap-1.5 rounded-xl border border-slate-200/90 bg-white/95 px-2.5 py-2 shadow-xs lg:w-52 lg:rounded-none lg:border-0 lg:bg-transparent lg:px-3 lg:py-1.5 lg:shadow-none">
                <svg class="size-3.5 shrink-0 text-slate-400 sm:size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="4" y="7" width="16" height="13" rx="2"/><path d="M9 7V5h6v2"/></svg>
                <label class="sr-only" for="kategori-user-katalog">Kategori</label>
                <select id="kategori-user-katalog" name="kategori" class="w-full !min-h-0 !border-0 !bg-transparent !p-0 text-xs text-slate-700 focus:outline-none sm:text-sm">
                    <option value="">Semua kategori</option>
                    @foreach ($daftarKategori as $kat)
                        <option value="{{ $kat->id }}" @selected((string) ($penyaring['kategori'] ?? '') === (string) $kat->id)>{{ $kat->nama }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="hidden items-center justify-end gap-2 lg:flex">
            @if (filled($penyaring['kata_kunci'] ?? null) || filled($penyaring['lokasi'] ?? null) || filled($penyaring['kategori'] ?? null))
                <a href="{{ route('katalog') }}" class="inline-flex items-center gap-1 px-3 text-xs font-bold text-[#007AFF] hover:underline">Reset</a>
            @endif
            <button type="submit" class="inline-flex min-h-10 items-center justify-center rounded-xl bg-[#007AFF] px-6 text-xs font-bold text-white shadow-sm shadow-[#007AFF]/25 transition hover:bg-[#0062CC]">Cari</button>
        </div>
        @if (filled($penyaring['kata_kunci'] ?? null) || filled($penyaring['lokasi'] ?? null) || filled($penyaring['kategori'] ?? null))
            <div class="flex justify-end lg:hidden">
                <a href="{{ route('katalog') }}" class="text-[11px] font-bold text-[#007AFF] hover:underline">Reset pencarian</a>
            </div>
        @endif
    </form>

    <!-- Main 2-Column Split (Matches Reference: 2-col Job Cards Left + Filter/Profile Sidebar Right) -->
    <div class="grid grid-cols-1 items-start gap-5 lg:grid-cols-12">
        <!-- Left: 2-Column Job Cards Grid (2 Columns Even on Mobile) -->
        <div class="space-y-4 lg:col-span-8">
            <div class="grid grid-cols-2 gap-2.5 sm:gap-4">
                @forelse ($daftarLowongan as $lowongan)
                    @php
                        $skorKecocokan = $rekomendasiSkor->get($lowongan->id)['skor'] ?? 0;
                        $tersimpan = $lowonganTersimpan->contains('id', $lowongan->id);
                    @endphp
                    <article class="flex min-w-0 flex-col justify-between rounded-2xl border-[1.5px] border-slate-200/90 bg-white/90 p-3 shadow-[0_8px_24px_-10px_rgba(15,23,42,0.08)] backdrop-blur-xl transition hover:-translate-y-0.5 hover:border-[#007AFF]/40 sm:rounded-3xl sm:p-5">
                        <div class="min-w-0">
                            <div class="flex items-start justify-between gap-1.5 sm:gap-3">
                                <div class="flex min-w-0 items-center gap-2 sm:gap-3">
                                    <span class="grid size-8 shrink-0 place-items-center rounded-xl border border-sky-200 bg-[#E0F2FE] text-xs font-extrabold text-[#007AFF] sm:size-11 sm:rounded-2xl sm:text-sm">{{ mb_strtoupper(mb_substr(str_replace('PT ', '', $lowongan->perusahaan->name), 0, 1)) }}</span>
                                    <div class="min-w-0">
                                        <h2 class="line-clamp-2 text-xs font-bold leading-snug text-slate-900 sm:truncate sm:text-sm">
                                            <a class="hover:text-[#007AFF]" href="{{ route('lowongan.show', $lowongan) }}">{{ $lowongan->judul }}</a>
                                        </h2>
                                        <p class="mt-0.5 truncate text-[10px] text-slate-500 sm:text-xs">{{ $lowongan->perusahaan->name }}</p>
                                    </div>
                                </div>
                                <form method="POST" action="{{ route('lowongan.simpan', $lowongan) }}" class="shrink-0">
                                    @csrf
                                    <button type="submit" class="grid size-7 shrink-0 place-items-center rounded-lg border border-slate-200 bg-white text-slate-400 transition hover:border-[#007AFF]/40 hover:text-[#007AFF] sm:size-8 sm:rounded-xl {{ $tersimpan ? '!border-[#007AFF] !bg-[#E0F2FE] !text-[#007AFF]' : '' }}" title="{{ $tersimpan ? 'Tersimpan' : 'Simpan lowongan' }}" aria-label="{{ $tersimpan ? 'Hapus dari favorit' : 'Simpan lowongan' }}">
                                        <svg class="size-3.5 sm:size-4" viewBox="0 0 24 24" fill="{{ $tersimpan ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="1.8"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg>
                                    </button>
                                </form>
                            </div>

                            <p class="mt-2 truncate text-[10px] font-medium text-slate-400 sm:hidden">{{ $lowongan->lokasi }}</p>
                            <p class="mt-3.5 hidden line-clamp-2 text-xs leading-relaxed text-slate-500 sm:block">{{ strip_tags((string) $lowongan->deskripsi) }}</p>

                            <div class="mt-2 flex flex-wrap gap-1 sm:mt-3 sm:gap-1.5">
                                @if ($lowongan->sedangDiiklankan())
                                    <span class="rounded-full bg-[#007AFF] px-2 py-0.5 text-[10px] font-bold text-white sm:px-2.5 sm:text-[11px]">Dipromosikan</span>
                                @endif
                                @if ($skorKecocokan >= 60)
                                    <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-700 sm:px-2.5 sm:text-[11px]">Cocok {{ $skorKecocokan }}%</span>
                                @endif
                                <span class="rounded-full bg-[#E0F2FE] px-2 py-0.5 text-[10px] font-semibold text-[#007AFF] sm:px-2.5 sm:text-[11px]">{{ $lowongan->tipe_kerja }}</span>
                            </div>
                        </div>

                        <div class="mt-3 flex flex-col gap-2 border-t border-slate-200/60 pt-2.5 sm:mt-4 sm:flex-row sm:items-center sm:justify-between sm:pt-3.5">
                            <p class="truncate text-[11px] font-bold text-slate-800 sm:text-xs">
                                @if ($lowongan->gaji_min && $lowongan->gaji_max)
                                    Rp {{ number_format($lowongan->gaji_min, 0, ',', '.') }}–{{ number_format($lowongan->gaji_max, 0, ',', '.') }}
                                @else
                                    Gaji kompetitif
                                @endif
                            </p>
                            <a href="{{ route('lowongan.show', $lowongan) }}" class="inline-flex w-full shrink-0 items-center justify-center rounded-xl bg-[#007AFF] px-3 py-1.5 text-[11px] font-bold !text-white shadow-sm shadow-[#007AFF]/20 transition hover:bg-[#0062CC] sm:w-auto sm:px-4 sm:py-2 sm:text-xs">
                                Lihat Loker
                            </a>
                        </div>
                    </article>
                @empty
                    <div class="col-span-full rounded-3xl border-[1.5px] border-dashed border-slate-300 bg-white/80 p-10 text-center">
                        <p class="text-sm font-bold text-slate-800">Belum ada lowongan yang sesuai.</p>
                        <a href="{{ route('katalog') }}" class="mt-3 inline-flex rounded-full bg-[#007AFF] px-5 py-2 text-xs font-bold !text-white">Reset Filter</a>
                    </div>
                @endforelse
            </div>

            <div>{{ $daftarLowongan->links() }}</div>
        </div>

        <!-- Right: Filter & Profile Sidebar (Matches Reference Right Column) -->
        <aside class="space-y-4 lg:col-span-4">
            <!-- Quick Filter Card -->
            <section class="rounded-3xl border-[1.5px] border-slate-200/90 bg-white/85 p-5 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.08)] backdrop-blur-xl">
                <div class="flex items-center justify-between">
                    <h2 class="text-sm font-bold text-slate-900">Filter Kategori</h2>
                    <a href="{{ route('katalog') }}" class="text-xs font-bold text-[#007AFF] hover:underline">Reset</a>
                </div>
                <div class="mt-3 space-y-1.5">
                    @foreach ($daftarKategori->take(6) as $kat)
                        @php($aktifKat = (string) ($penyaring['kategori'] ?? '') === (string) $kat->id)
                        <a href="{{ route('katalog', array_filter(['kategori' => $kat->id, 'kata_kunci' => $penyaring['kata_kunci'] ?? null, 'lokasi' => $penyaring['lokasi'] ?? null])) }}" @class(['flex items-center justify-between rounded-2xl px-3 py-2 text-xs font-semibold transition', 'bg-[#E0F2FE] text-[#007AFF]' => $aktifKat, 'text-slate-600 hover:bg-slate-100/80' => ! $aktifKat])>
                            <span>{{ $kat->nama }}</span>
                            <span @class(['size-2 rounded-full', 'bg-[#007AFF]' => $aktifKat, 'bg-slate-200' => ! $aktifKat])></span>
                        </a>
                    @endforeach
                </div>
            </section>

            <!-- Kelengkapan Profil & Ringkasan Lamaran -->
            <section class="rounded-3xl border-[1.5px] border-slate-200/90 bg-white/85 p-5 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.08)] backdrop-blur-xl">
                <div class="flex items-center justify-between text-xs">
                    <h2 class="font-bold text-slate-900">Kelengkapan Profil</h2>
                    <span class="font-extrabold text-[#007AFF] tabular-nums">{{ $kelengkapanProfil }}%</span>
                </div>
                <div class="mt-2.5 h-2 w-full overflow-hidden rounded-full bg-[#E0F2FE]">
                    <div class="h-full rounded-full bg-[#007AFF]" style="width: {{ $kelengkapanProfil }}%"></div>
                </div>
                <div class="mt-3 flex items-center justify-between text-[11px] text-slate-500">
                    <span>Untuk rekomendasi akurat</span>
                    <a class="font-bold text-[#007AFF] hover:underline" href="{{ route('akun.edit') }}">Lengkapi</a>
                </div>

                <dl class="mt-4 grid grid-cols-2 gap-2 border-t border-slate-200/70 pt-4">
                    @foreach (collect($ringkasan)->take(4) as $label => $nilai)
                        <div class="rounded-2xl bg-slate-50/90 p-3">
                            <dt class="text-[11px] font-medium text-slate-500">{{ $label }}</dt>
                            <dd class="mt-1 text-lg font-extrabold text-slate-900 tabular-nums">{{ $nilai }}</dd>
                        </div>
                    @endforeach
                </dl>
            </section>

            @if ($jadwalWawancara->isNotEmpty())
                <section class="rounded-3xl border-[1.5px] border-slate-200/90 bg-white/85 p-5 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.08)] backdrop-blur-xl">
                    <div class="flex items-center justify-between">
                        <h2 class="text-sm font-bold text-slate-900">Jadwal Wawancara</h2>
                        <a class="text-xs font-bold text-[#007AFF] hover:underline" href="{{ route('wawancara.index') }}">Semua</a>
                    </div>
                    <ul class="mt-3 space-y-2">
                        @foreach ($jadwalWawancara as $jadwal)
                            <li class="rounded-2xl bg-[#E0F2FE]/60 p-3 text-xs">
                                <strong class="block font-bold text-slate-900">{{ $jadwal->lamaran->lowongan->judul }}</strong>
                                <span class="text-slate-600">{{ $jadwal->perusahaan->name }} &middot; {{ $jadwal->tanggal->format('d M') }}, {{ $jadwal->jam }}</span>
                            </li>
                        @endforeach
                    </ul>
                </section>
            @endif
        </aside>
    </div>
</x-panel-user>
