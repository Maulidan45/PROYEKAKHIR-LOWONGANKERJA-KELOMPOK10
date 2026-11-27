@php
    $totalAkun = $ringkasan['Akun terdaftar'] ?? 0;
    $lowonganAktif = $ringkasan['Lowongan aktif'] ?? 0;
    $totalKategori = $ringkasan['Kategori'] ?? 0;
    $totalLamaran = $ringkasan['Lamaran'] ?? 0;
    $akunUser = $jumlahAkun['user'] ?? 0;
    $akunPerusahaan = $jumlahAkun['perusahaan'] ?? 0;
    $akunAdmin = $jumlahAkun['admin'] ?? 0;

    $dataBatang = [
        ['label' => 'Pencari', 'nilai' => $akunUser],
        ['label' => 'PT', 'nilai' => $akunPerusahaan],
        ['label' => 'Kategori', 'nilai' => $totalKategori],
        ['label' => 'Loker', 'nilai' => $lowonganAktif],
        ['label' => 'Lamaran', 'nilai' => $totalLamaran],
        ['label' => 'Admin', 'nilai' => $akunAdmin],
    ];
    $maksBatang = max(1, collect($dataBatang)->max('nilai'));
@endphp

<x-panel-admin judul="Beranda" aktif="dashboard" :adalah-beranda="true">
    <x-slot:aksi>
        <form method="POST" action="{{ route('admin.lowongan.sinkron') }}" data-kirim>
            @csrf
            <button class="inline-flex min-h-9 items-center gap-2 rounded-full border-[1.5px] border-[#007AFF]/30 bg-white/90 px-4 text-xs font-bold text-slate-800 shadow-sm backdrop-blur-md transition hover:border-[#007AFF] hover:bg-[#E0F2FE] hover:text-[#007AFF] disabled:opacity-60 sm:min-h-10" type="submit" data-teks-proses="Menarik data…">
                <svg class="size-3.5 text-[#007AFF]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                Tarik API Remotive
            </button>
        </form>
    </x-slot:aksi>

    @if ($errors->any())
        <div class="rounded-3xl border-[1.5px] border-rose-300 bg-rose-50/90 px-5 py-3.5 text-sm font-medium text-rose-800 backdrop-blur-xl" role="alert">{{ $errors->first() }}</div>
    @endif

    <!-- Responsive Bento Grid: 1 col (iPhone) -> 2 cols (iPad/Tablet) -> 12 cols (Laptop/Desktop) -->
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-12 sm:gap-5">

        <!-- Left Column: Hero Blue Card + Compact Stat Card -->
        <div class="flex flex-col gap-4 sm:gap-5 lg:col-span-4">
            <!-- Hero Blue Card -->
            <section class="rounded-3xl border-[1.5px] border-slate-200/90 bg-white/80 p-5 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.1)] backdrop-blur-xl sm:rounded-[28px]">
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Lowongan Aktif</h2>
                        <p class="text-xs text-slate-500">Tayang di katalog publik</p>
                    </div>
                    <a class="grid size-8 place-items-center rounded-full border border-slate-200 bg-white text-slate-600 transition hover:border-[#007AFF]/40 hover:bg-[#E0F2FE] hover:text-[#007AFF]" href="{{ route('lowongan.index') }}" aria-label="Kelola Lowongan">
                        <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M7 17 17 7M8 7h9v9"/></svg>
                    </a>
                </div>

                <!-- Inner iOS Blue Gradient Card -->
                <div class="mt-4 rounded-2xl bg-gradient-to-br from-[#007AFF] via-[#0A84FF] to-sky-500 p-5 text-white shadow-lg shadow-[#007AFF]/25">
                    <div class="flex items-center justify-between text-xs font-bold tracking-wider uppercase opacity-95">
                        <span>LOKERKU</span>
                        <span class="rounded-full bg-white/20 px-2.5 py-0.5 text-[10px] font-semibold backdrop-blur-sm">API Aktif</span>
                    </div>
                    <p class="mt-1 text-xs text-sky-100">Total Posisi Tersedia</p>
                    <p class="mt-2 text-2xl font-extrabold tracking-tight tabular-nums sm:text-3xl">{{ number_format($lowonganAktif, 0, ',', '.') }} <span class="text-sm font-semibold text-sky-100 sm:text-base">Posisi</span></p>
                    <div class="mt-5 flex items-center justify-between text-[11px] font-medium text-sky-100">
                        <span>{{ number_format($totalKategori, 0, ',', '.') }} Kategori</span>
                        <span>Remotive Sync</span>
                    </div>
                </div>
            </section>

            <!-- Compact Stat Card -->
            <section class="flex items-center justify-between gap-4 rounded-3xl border-[1.5px] border-slate-200/90 bg-white/80 p-5 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.1)] backdrop-blur-xl sm:rounded-[28px]">
                <div>
                    <p class="text-xs font-semibold text-slate-500">Total Lamaran Masuk</p>
                    <p class="mt-1 text-2xl font-extrabold tracking-tight text-slate-900 tabular-nums">+{{ number_format($totalLamaran, 0, ',', '.') }} <span class="text-xs font-semibold text-slate-400">Lamaran</span></p>
                </div>
                <span class="rounded-full bg-[#007AFF] px-3.5 py-1.5 text-xs font-bold text-white shadow-sm shadow-[#007AFF]/25">Aktif</span>
            </section>
        </div>

        <!-- Center Column: Striped Bar Chart Widget -->
        <section class="flex flex-col justify-between rounded-3xl border-[1.5px] border-slate-200/90 bg-white/80 p-5 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.1)] backdrop-blur-xl sm:rounded-[28px] sm:p-6 lg:col-span-4">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <div class="flex items-center gap-2.5">
                    <span class="grid size-8 place-items-center rounded-xl border border-sky-200 bg-[#E0F2FE] text-[#007AFF]">
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 20V10M10 20V4M16 20v-7M22 20v-4"/></svg>
                    </span>
                    <h2 class="text-sm font-bold text-slate-900">Statistik Sistem</h2>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="rounded-full bg-[#007AFF] px-3 py-1 text-[11px] font-bold text-white shadow-xs">Ringkasan</span>
                    <a class="grid size-8 place-items-center rounded-full border border-slate-200 bg-white text-slate-600 transition hover:border-[#007AFF]/40 hover:bg-[#E0F2FE] hover:text-[#007AFF]" href="{{ route('admin.pengguna.index') }}" aria-label="Lihat Pengguna">
                        <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M7 17 17 7M8 7h9v9"/></svg>
                    </a>
                </div>
            </div>

            <!-- Striped Vertical Pill Bars -->
            <div class="mt-8 flex h-48 items-end justify-between gap-1.5 px-0.5 sm:h-52 sm:gap-2.5 sm:px-1">
                @foreach ($dataBatang as $batang)
                    @php
                        $persen = max(18, (int) round(($batang['nilai'] / $maksBatang) * 100));
                        $tertinggi = $batang['nilai'] === $maksBatang && $maksBatang > 0;
                    @endphp
                    <div class="flex min-w-0 flex-1 flex-col items-center gap-2">
                        <div class="relative flex h-36 w-full items-end justify-center sm:h-40">
                            @if ($tertinggi)
                                <span class="absolute -top-6 rounded-full bg-[#007AFF] px-2 py-0.5 text-[10px] font-bold text-white shadow-sm tabular-nums">{{ $batang['nilai'] }}</span>
                            @endif
                            <div @class([
                                'w-full max-w-[36px] rounded-full border transition-all duration-300',
                                'border-[#0062CC] bg-[#007AFF] shadow-md shadow-[#007AFF]/25' => $tertinggi,
                                'border-sky-300/70 bg-sky-200/80 hover:bg-sky-300/90' => ! $tertinggi,
                            ]) style="height: {{ $persen }}%; {{ ! $tertinggi ? 'background-image: repeating-linear-gradient(-45deg, transparent, transparent 4px, rgba(255,255,255,0.5) 4px, rgba(255,255,255,0.5) 8px);' : '' }}" title="{{ $batang['label'] }}: {{ $batang['nilai'] }}"></div>
                        </div>
                        <span class="truncate text-[10px] font-semibold text-slate-500">{{ $batang['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </section>

        <!-- Right Column: On Tablet spans 2 cols as 2-col grid; on Laptop/Desktop spans 4 cols stacked -->
        <div class="grid grid-cols-1 gap-4 md:col-span-2 md:grid-cols-2 lg:col-span-4 lg:grid-cols-1 sm:gap-5">
            <!-- Wave Chart Card -->
            <section class="rounded-3xl border-[1.5px] border-slate-200/90 bg-white/80 p-5 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.1)] backdrop-blur-xl sm:rounded-[28px]">
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Akun Terdaftar</h2>
                        <p class="text-xs text-slate-500">Seluruh peran pengguna</p>
                    </div>
                    <a class="grid size-8 place-items-center rounded-full border border-slate-200 bg-white text-slate-600 transition hover:border-[#007AFF]/40 hover:bg-[#E0F2FE] hover:text-[#007AFF]" href="{{ route('admin.pengguna.index') }}" aria-label="Kelola Pengguna">
                        <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M7 17 17 7M8 7h9v9"/></svg>
                    </a>
                </div>

                <div class="mt-3 text-center">
                    <p class="text-xs font-medium text-slate-400">Total Akun</p>
                    <p class="mt-0.5 text-3xl font-extrabold tracking-tight text-slate-900 tabular-nums">{{ number_format($totalAkun, 0, ',', '.') }} <span class="text-lg font-semibold text-slate-400">User</span></p>
                </div>

                <div class="my-3 overflow-hidden">
                    <svg class="h-14 w-full" viewBox="0 0 300 60" fill="none" preserveAspectRatio="none" aria-hidden="true">
                        <defs>
                            <linearGradient id="lokerkuWaveGrad" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#007AFF" stop-opacity="0.28"/>
                                <stop offset="100%" stop-color="#007AFF" stop-opacity="0"/>
                            </linearGradient>
                        </defs>
                        <path d="M0 40 C 35 15, 65 48, 95 28 C 125 8, 155 45, 190 22 C 225 5, 260 44, 300 18 L 300 60 L 0 60 Z" fill="url(#lokerkuWaveGrad)"/>
                        <path d="M0 40 C 35 15, 65 48, 95 28 C 125 8, 155 45, 190 22 C 225 5, 260 44, 300 18" stroke="#007AFF" stroke-width="2.2" stroke-linecap="round"/>
                    </svg>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <a class="inline-flex min-h-10 items-center justify-center gap-1.5 rounded-full bg-[#007AFF] px-4 text-xs font-bold text-white shadow-sm shadow-[#007AFF]/25 transition hover:bg-[#0062CC]" href="{{ route('admin.verifikasi.index') }}">
                        Verifikasi
                        <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 19V5m-5 5 5-5 5 5"/></svg>
                    </a>
                    <a class="inline-flex min-h-10 items-center justify-center gap-1.5 rounded-full border border-slate-200 bg-white px-4 text-xs font-bold text-slate-700 transition hover:border-[#007AFF]/40 hover:bg-[#E0F2FE] hover:text-[#007AFF]" href="{{ route('admin.pengguna.index') }}">
                        Pengguna
                        <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 5v14m-5-5 5 5 5-5"/></svg>
                    </a>
                </div>
            </section>

            <!-- Avatar Stack Card -->
            <section class="flex flex-col justify-between rounded-3xl border-[1.5px] border-slate-200/90 bg-white/80 p-5 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.1)] backdrop-blur-xl sm:rounded-[28px]">
                <div class="flex items-center justify-between gap-2">
                    <div>
                        <p class="text-xs font-bold text-slate-900">Antrean Verifikasi</p>
                        <p class="text-[11px] text-slate-500">Perusahaan menunggu persetujuan</p>
                    </div>
                    <span class="rounded-full bg-[#007AFF] px-3 py-1 text-xs font-bold text-white tabular-nums">{{ $menungguVerifikasi ?? 0 }}</span>
                </div>

                <div class="mt-4 flex items-center justify-between gap-3 rounded-2xl border border-slate-200/80 bg-white/90 p-3.5">
                    <div>
                        <p class="text-xs font-bold text-slate-800">Akun Terbaru</p>
                        <p class="text-[11px] text-slate-400">Terdaftar di sistem</p>
                    </div>
                    <div class="flex items-center -space-x-2">
                        @foreach ($daftarAkun->take(4) as $akunMini)
                            <span class="grid size-8 place-items-center rounded-full border-2 border-white bg-[#E0F2FE] text-xs font-extrabold text-[#007AFF] shadow-xs sm:size-9" title="{{ $akunMini->name }}">{{ mb_strtoupper(mb_substr($akunMini->name, 0, 1)) }}</span>
                        @endforeach
                        @if ($totalAkun > 4)
                            <a href="#daftar-akun" class="grid size-8 place-items-center rounded-full border-2 border-white bg-[#007AFF] text-[11px] font-bold text-white shadow-xs sm:size-9">+{{ $totalAkun - 4 }}</a>
                        @endif
                    </div>
                </div>
            </section>
        </div>

        <!-- Bottom Left-Center: Lowongan Aktif Terbaru Table -->
        <section class="rounded-3xl border-[1.5px] border-slate-200/90 bg-white/80 p-5 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.1)] backdrop-blur-xl sm:rounded-[28px] sm:p-6 md:col-span-2 lg:col-span-8" aria-labelledby="judul-lowongan-admin">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h2 class="text-base font-bold text-slate-900" id="judul-lowongan-admin">Lowongan Aktif Terbaru</h2>
                    <p class="text-xs text-slate-500">Daftar posisi terbaru dari perusahaan &amp; Remotive API</p>
                </div>
                <a class="grid size-9 place-items-center rounded-full border border-slate-200 bg-white text-slate-600 transition hover:border-[#007AFF]/40 hover:bg-[#E0F2FE] hover:text-[#007AFF]" href="{{ route('lowongan.index') }}" aria-label="Kelola semua lowongan">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M7 17 17 7M8 7h9v9"/></svg>
                </a>
            </div>

            <div class="mt-4 overflow-x-auto">
                <table class="w-full min-w-[540px] text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-200/80 text-xs font-semibold text-slate-400">
                            <th class="pb-3 font-medium">Posisi &amp; Perusahaan</th>
                            <th class="pb-3 font-medium">Kategori</th>
                            <th class="pb-3 font-medium">Lokasi</th>
                            <th class="pb-3 font-medium">Status</th>
                            <th class="pb-3 text-right font-medium">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200/60">
                        @forelse ($lowonganTerbaruAdmin as $itemLowongan)
                            <tr class="transition hover:bg-white/70">
                                <td class="py-3.5 pr-3">
                                    <div class="flex items-center gap-3">
                                        <span class="grid size-9 shrink-0 place-items-center rounded-full border border-sky-200 bg-[#E0F2FE] text-xs font-extrabold text-[#007AFF]">{{ mb_strtoupper(mb_substr(str_replace('PT ', '', $itemLowongan->perusahaan->name), 0, 1)) }}</span>
                                        <div class="min-w-0">
                                            <a class="block max-w-[220px] truncate font-bold text-slate-900 hover:text-[#007AFF]" href="{{ route('lowongan.show', $itemLowongan) }}">{{ $itemLowongan->judul }}</a>
                                            <span class="block max-w-[220px] truncate text-xs text-slate-400">{{ $itemLowongan->perusahaan->name }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3.5 pr-3 text-xs font-medium text-slate-600">{{ $itemLowongan->kategori->nama }}</td>
                                <td class="py-3.5 pr-3 text-xs text-slate-500">{{ Str::limit($itemLowongan->lokasi, 22) }}</td>
                                <td class="py-3.5 pr-3">
                                    <span class="inline-flex items-center gap-1.5 rounded-full border border-sky-200 bg-[#E0F2FE]/80 px-2.5 py-1 text-xs font-semibold text-[#007AFF]">
                                        <span class="size-1.5 rounded-full bg-[#007AFF]"></span>
                                        Aktif
                                    </span>
                                </td>
                                <td class="py-3.5 text-right">
                                    <a class="text-xs font-bold text-[#007AFF] hover:underline" href="{{ route('lowongan.show', $itemLowongan) }}">Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-sm text-slate-500">Belum ada lowongan aktif.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Bottom Right: Akun Terdaftar List -->
        <section class="flex flex-col justify-between rounded-3xl border-[1.5px] border-slate-200/90 bg-white/80 p-5 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.1)] backdrop-blur-xl sm:rounded-[28px] sm:p-6 md:col-span-2 lg:col-span-4" id="daftar-akun" aria-labelledby="judul-akun-terdaftar">
            <div>
                <div class="flex items-center justify-between gap-2">
                    <h2 class="text-base font-bold text-slate-900" id="judul-akun-terdaftar">Akun Terdaftar</h2>
                    <a class="text-xs font-bold text-[#007AFF] hover:underline" href="{{ route('admin.pengguna.index') }}">Semua</a>
                </div>
                <ul class="mt-4 divide-y divide-slate-200/60">
                    @forelse ($daftarAkun->take(5) as $akun)
                        <li class="flex items-center gap-3 py-2.5">
                            <span class="grid size-9 shrink-0 place-items-center rounded-full border border-sky-200 bg-[#E0F2FE] text-xs font-extrabold text-[#007AFF]">{{ mb_strtoupper(mb_substr($akun->name, 0, 1)) }}</span>
                            <div class="min-w-0 flex-1">
                                <strong class="block truncate text-xs font-bold text-slate-900">{{ $akun->name }}</strong>
                                <p class="truncate text-[11px] text-slate-400">{{ $akun->email }}</p>
                            </div>
                            <span class="rounded-full border border-slate-200 bg-slate-100 px-2.5 py-0.5 text-[10px] font-bold text-slate-600">{{ $jenisAkunTersedia[$akun->role] ?? $akun->role }}</span>
                        </li>
                    @empty
                        <li class="py-6 text-center text-xs text-slate-500">Belum ada akun terdaftar.</li>
                    @endforelse
                </ul>
            </div>
            <div class="mt-3 border-t border-slate-200/70 pt-3">{{ $daftarAkun->links() }}</div>
        </section>
    </div>
</x-panel-admin>
