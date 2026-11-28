@php
    $totalLowongan = max(1, $statistikLengkap['total_lowongan']);
    $persenAktif = (int) round(($statistikLengkap['lowongan_aktif'] / $totalLowongan) * 100);
    $totalPelamar = max(1, $statistikLengkap['total_pelamar']);
    $persenDiterima = (int) round(($statistikLengkap['pelamar_diterima'] / $totalPelamar) * 100);
    $hariIni = now()->locale('id');
    $daftarIklanAktif = ($lowonganPerusahaanAktif ?? collect())->filter(fn ($l) => $l->iklan_sampai && $l->iklan_sampai->isFuture());
@endphp

<x-panel-perusahaan judul="Dashboard" aktif="dashboard">
    <!-- Top Row: VISA-Style Iklan Loker Card + 4 Quick-Action Feature Cards -->
    <div class="grid grid-cols-1 gap-4 lg:grid-cols-12 sm:gap-5">
        <!-- VISA-Style Blue Gradient Card for Iklan Loker -->
        <section class="rounded-3xl bg-gradient-to-br from-[#007AFF] via-[#0A84FF] to-sky-500 p-5 text-white shadow-lg shadow-[#007AFF]/25 lg:col-span-4">
            <div class="flex items-center justify-between text-xs font-bold tracking-wider uppercase">
                <span>LOKERKU ADS</span>
                <span class="rounded-full bg-white/20 px-2.5 py-0.5 text-[10px] font-semibold backdrop-blur-sm">Prioritas #1</span>
            </div>
            <p class="mt-2 text-xs text-sky-100">Tampilkan loker paling atas di halaman utama</p>
            <p class="mt-1.5 text-2xl font-extrabold tracking-tight tabular-nums">
                Rp 50.000 <span class="text-xs font-semibold text-sky-100">/ 7 Hari</span>
            </p>
            <div class="mt-4 flex items-center justify-between gap-2">
                <span class="text-[11px] font-medium text-sky-100">{{ $daftarIklanAktif->count() }} loker sedang diiklankan</span>
                <a href="{{ route('perusahaan.ads.index') }}" class="inline-flex items-center gap-1.5 rounded-full bg-white px-4 py-2 text-xs font-bold !text-[#007AFF] shadow-sm transition hover:bg-sky-50">
                    <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><path d="m12 3 2.5 5 5.5.8-4 3.9.9 5.5-4.9-2.6L7.1 18.2l.9-5.5-4-3.9 5.5-.8Z"/></svg>
                    Iklankan Loker
                </a>
            </div>
        </section>

        <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 lg:col-span-8 sm:gap-4">
            <!-- + Pasang Lowongan Box -->
            @can('create', App\Models\Lowongan::class)
                <a href="{{ route('lowongan.create') }}" class="flex flex-col items-center justify-center gap-2.5 rounded-3xl border-[1.5px] border-sky-200 bg-[#E0F2FE]/60 p-4 text-center shadow-sm backdrop-blur-xl transition hover:-translate-y-0.5 hover:bg-[#E0F2FE]">
                    <span class="grid size-10 place-items-center rounded-2xl bg-[#007AFF] text-white shadow-md shadow-[#007AFF]/25">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                    </span>
                    <span class="text-xs font-bold text-[#007AFF]">Pasang Loker</span>
                </a>
            @else
                <a href="{{ route('akun.edit') }}" class="flex flex-col items-center justify-center gap-2.5 rounded-3xl border-[1.5px] border-sky-200 bg-[#E0F2FE]/60 p-4 text-center shadow-sm backdrop-blur-xl transition hover:-translate-y-0.5 hover:bg-[#E0F2FE]">
                    <span class="grid size-10 place-items-center rounded-2xl bg-[#007AFF] text-white shadow-md shadow-[#007AFF]/25">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/></svg>
                    </span>
                    <span class="text-xs font-bold text-[#007AFF]">Verifikasi Akun</span>
                </a>
            @endcan

            <a href="{{ route('lowongan.index') }}" class="flex flex-col justify-between rounded-3xl border-[1.5px] border-slate-200/90 bg-white/80 p-4 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.08)] backdrop-blur-xl transition hover:-translate-y-0.5 hover:border-[#007AFF]/40">
                <span class="grid size-10 place-items-center rounded-2xl bg-[#E0F2FE] text-[#007AFF]">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 8h16v11.5H4zM8.5 8V5h7v3M4 12.5h16"/></svg>
                </span>
                <div class="mt-3">
                    <strong class="block text-xs font-bold text-slate-900">Kelola Lowongan</strong>
                    <span class="text-[11px] font-medium text-slate-500">{{ $statistikLengkap['lowongan_aktif'] }} aktif</span>
                </div>
            </a>

            <a href="{{ route('perusahaan.pelamar.index') }}" class="flex flex-col justify-between rounded-3xl border-[1.5px] border-slate-200/90 bg-white/80 p-4 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.08)] backdrop-blur-xl transition hover:-translate-y-0.5 hover:border-[#007AFF]/40">
                <span class="grid size-10 place-items-center rounded-2xl bg-[#E0F2FE] text-[#007AFF]">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                </span>
                <div class="mt-3">
                    <strong class="block text-xs font-bold text-slate-900">Daftar Pelamar</strong>
                    <span class="text-[11px] font-medium text-slate-500">{{ $statistikLengkap['total_pelamar'] }} pelamar</span>
                </div>
            </a>

            <a href="{{ route('wawancara.index') }}" class="flex flex-col justify-between rounded-3xl border-[1.5px] border-slate-200/90 bg-white/80 p-4 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.08)] backdrop-blur-xl transition hover:-translate-y-0.5 hover:border-[#007AFF]/40">
                <span class="grid size-10 place-items-center rounded-2xl bg-[#E0F2FE] text-[#007AFF]">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                </span>
                <div class="mt-3">
                    <strong class="block text-xs font-bold text-slate-900">Jadwal Seleksi</strong>
                    <span class="text-[11px] font-medium text-slate-500">{{ $jadwalTerdekat->count() }} terdekat</span>
                </div>
            </a>
        </div>
    </div>

    <!-- Middle Row: 3 Bento Columns (Notifications, Lamaran Highlight, Calendar/Wawancara) -->
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-12 sm:gap-5">
        <!-- Col 1: Notifications -->
        <section class="flex flex-col justify-between rounded-3xl border-[1.5px] border-slate-200/90 bg-white/80 p-5 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.08)] backdrop-blur-xl lg:col-span-4">
            <div class="flex items-center justify-between gap-2">
                <h2 class="text-sm font-bold text-slate-900">Notifikasi</h2>
                <a class="text-xs font-semibold text-[#007AFF] hover:underline" href="{{ route('notifikasi.index') }}">Semua</a>
            </div>
            <div class="mt-4 space-y-2.5">
                @forelse ($notifikasiTerbaru->take(2) as $notif)
                    <a href="{{ route('notifikasi.index') }}" class="block rounded-2xl border border-slate-200/80 bg-white/95 p-3.5 shadow-xs transition hover:border-[#007AFF]/40">
                        <p class="truncate text-xs font-bold text-slate-900">{{ $notif->data['judul'] ?? 'Pembaruan Rekrutmen' }}</p>
                        <p class="mt-1 truncate text-[11px] text-slate-500">{{ $notif->data['pesan'] ?? 'Ada aktivitas baru pada lowongan Anda.' }}</p>
                    </a>
                @empty
                    <div class="rounded-2xl border border-slate-200/70 bg-slate-50/80 p-4 text-center">
                        <p class="text-xs font-semibold text-slate-700">Belum ada notifikasi baru</p>
                        <p class="mt-0.5 text-[11px] text-slate-400">Semua informasi lamaran akan muncul di sini.</p>
                    </div>
                @endforelse
            </div>
        </section>

        <!-- Col 2: Highlight Lamaran / Seleksi -->
        <section class="flex flex-col justify-between rounded-3xl border-[1.5px] border-slate-200/90 bg-white/80 p-5 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.08)] backdrop-blur-xl lg:col-span-4">
            <div class="flex items-center justify-between gap-2">
                <h2 class="text-sm font-bold text-slate-900">Seleksi Berjalan</h2>
                <a class="text-xs font-semibold text-[#007AFF] hover:underline" href="{{ route('perusahaan.pelamar.index') }}">Kelola</a>
            </div>
            @php($lamaranUtama = $lamaranTerbaru->first())
            @if ($lamaranUtama)
                <div class="mt-4 rounded-2xl border border-slate-200/80 bg-white/95 p-4 shadow-xs">
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[11px] font-semibold text-[#007AFF]">{{ $lamaranUtama->lowongan->judul }}</span>
                        <x-status-lamaran :status="$lamaranUtama->status" />
                    </div>
                    <p class="mt-2 text-sm font-bold text-slate-900">{{ $lamaranUtama->pelamar->name }}</p>
                    <p class="mt-1 text-xs text-slate-500">Melamar pada {{ $lamaranUtama->created_at->format('d M Y') }}</p>
                </div>
            @else
                <div class="mt-4 rounded-2xl border border-slate-200/70 bg-slate-50/80 p-4 text-center">
                    <p class="text-xs font-semibold text-slate-700">Belum ada pelamar masuk</p>
                </div>
            @endif
            <a href="{{ route('perusahaan.pelamar.index') }}" class="mt-3 inline-flex min-h-10 w-full items-center justify-center gap-2 rounded-2xl bg-[#E0F2FE] text-xs font-bold text-[#007AFF] transition hover:bg-sky-200/70">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 5v14M5 12h14"/></svg>
                Lihat Semua Pelamar
            </a>
        </section>

        <!-- Col 3: Calendar + Jadwal Wawancara -->
        <section class="rounded-3xl border-[1.5px] border-slate-200/90 bg-white/80 p-5 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.08)] backdrop-blur-xl md:col-span-2 lg:col-span-4">
            <div class="flex items-center justify-between gap-2">
                <h2 class="text-sm font-bold text-slate-900">{{ $hariIni->translatedFormat('F Y') }}</h2>
                <a class="text-xs font-semibold text-[#007AFF] hover:underline" href="{{ route('wawancara.index') }}">Jadwal</a>
            </div>
            <!-- Week Strip -->
            <div class="mt-3 grid grid-cols-7 gap-1 text-center">
                @for ($i = -3; $i <= 3; $i++)
                    @php($tgl = now()->copy()->addDays($i))
                    <div @class(['rounded-xl py-1.5 text-xs', 'bg-[#007AFF] font-bold text-white shadow-sm shadow-[#007AFF]/25' => $i === 0, 'text-slate-500' => $i !== 0])>
                        <span class="block text-[10px] opacity-80">{{ $tgl->locale('id')->translatedFormat('D') }}</span>
                        <span class="mt-0.5 block font-bold tabular-nums">{{ $tgl->format('d') }}</span>
                    </div>
                @endfor
            </div>
            <!-- Interview Timeline -->
            <div class="mt-4 space-y-2.5 border-t border-slate-200/70 pt-3">
                @forelse ($jadwalTerdekat->take(2) as $jadwal)
                    <div class="flex items-center justify-between gap-2 rounded-2xl bg-slate-50/90 px-3.5 py-2.5">
                        <div class="min-w-0">
                            <p class="truncate text-xs font-bold text-slate-900">{{ $jadwal->pelamar->name }}</p>
                            <p class="truncate text-[11px] text-slate-500">{{ $jadwal->lamaran->lowongan->judul }} · {{ $jadwal->tipe }}</p>
                        </div>
                        <span class="shrink-0 rounded-full bg-[#E0F2FE] px-2.5 py-0.5 text-[11px] font-bold text-[#007AFF] tabular-nums">{{ $jadwal->jam }}</span>
                    </div>
                @empty
                    <p class="py-2 text-center text-xs text-slate-400">Belum ada jadwal wawancara terdekat.</p>
                @endforelse
            </div>
        </section>
    </div>

    <!-- Bottom Row: Lamaran Terbaru List + Blue Highlight Card + Ring Progress & Pelamar Baru -->
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-12 sm:gap-5">
        <!-- Lamaran Terbaru (5 cols) -->
        <section class="rounded-3xl border-[1.5px] border-slate-200/90 bg-white/80 p-5 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.08)] backdrop-blur-xl lg:col-span-5">
            <div class="flex items-center justify-between gap-2">
                <h2 class="text-sm font-bold text-slate-900">Lamaran Masuk</h2>
                <a class="text-xs font-semibold text-[#007AFF] hover:underline" href="{{ route('perusahaan.pelamar.index') }}">Semua</a>
            </div>
            <ul class="mt-4 divide-y divide-slate-200/60">
                @forelse ($lamaranTerbaru->take(4) as $lamaran)
                    <li class="flex items-center justify-between gap-3 py-3">
                        <div class="flex min-w-0 items-center gap-3">
                            <span class="grid size-9 shrink-0 place-items-center rounded-full bg-[#E0F2FE] text-xs font-extrabold text-[#007AFF]">{{ mb_strtoupper(mb_substr($lamaran->pelamar->name, 0, 1)) }}</span>
                            <div class="min-w-0">
                                <strong class="block truncate text-xs font-bold text-slate-900">{{ $lamaran->pelamar->name }}</strong>
                                <p class="truncate text-[11px] text-slate-500">{{ $lamaran->lowongan->judul }}</p>
                            </div>
                        </div>
                        <div class="flex shrink-0 items-center gap-2">
                            <x-status-lamaran :status="$lamaran->status" />
                            <a class="text-xs font-bold text-[#007AFF] hover:underline" href="{{ route('lamaran.show', $lamaran) }}">Detail</a>
                        </div>
                    </li>
                @empty
                    <li class="py-8 text-center text-xs text-slate-500">Belum ada lamaran masuk.</li>
                @endforelse
            </ul>
        </section>

        <!-- Vertical Blue Highlight Card (2 cols, mirrors Go Premium card) -->
        <section class="flex flex-col justify-between rounded-3xl bg-gradient-to-br from-[#007AFF] via-[#0A84FF] to-sky-500 p-5 text-white shadow-lg shadow-[#007AFF]/25 lg:col-span-2">
            <div>
                <span class="inline-flex size-10 items-center justify-center rounded-2xl bg-white/20 backdrop-blur-sm">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9 12 2 2 4-4"/></svg>
                </span>
                <h2 class="mt-4 text-base font-extrabold leading-snug">{{ $pengguna->sudahTerverifikasi() ? 'Terverifikasi' : 'Lengkapi Profil' }}</h2>
                <p class="mt-1.5 text-xs leading-relaxed text-sky-100">{{ $statistikLengkap['total_lowongan'] }} total lowongan tercatat di akun perusahaan Anda.</p>
            </div>
            <a href="{{ route('akun.edit') }}" class="mt-5 inline-flex min-h-10 items-center justify-center rounded-2xl bg-white px-4 text-xs font-bold text-[#007AFF] shadow-sm transition hover:bg-sky-50">
                Profil Perusahaan
            </a>
        </section>

        <!-- Ring Progress + Pelamar Baru (5 cols) -->
        <div class="flex flex-col gap-4 lg:col-span-5">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <!-- Ring 1: Lowongan Aktif -->
                <div class="flex items-center gap-3.5 rounded-3xl border-[1.5px] border-slate-200/90 bg-white/80 p-4 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.08)] backdrop-blur-xl">
                    <div class="relative grid size-14 shrink-0 place-items-center">
                        <svg class="size-14 -rotate-90" viewBox="0 0 36 36">
                            <circle cx="18" cy="18" r="15" fill="none" stroke="#E0F2FE" stroke-width="3.5"/>
                            <circle cx="18" cy="18" r="15" fill="none" stroke="#007AFF" stroke-width="3.5" stroke-dasharray="{{ $persenAktif }}, 100" stroke-linecap="round"/>
                        </svg>
                        <span class="absolute text-xs font-extrabold text-slate-900 tabular-nums">{{ $persenAktif }}%</span>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-bold text-slate-900">Lowongan Aktif</p>
                        <p class="mt-0.5 text-[11px] text-slate-500">{{ $statistikLengkap['lowongan_aktif'] }} dari {{ $statistikLengkap['total_lowongan'] }}</p>
                    </div>
                </div>

                <!-- Ring 2: Pelamar Diterima -->
                <div class="flex items-center gap-3.5 rounded-3xl border-[1.5px] border-slate-200/90 bg-white/80 p-4 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.08)] backdrop-blur-xl">
                    <div class="relative grid size-14 shrink-0 place-items-center">
                        <svg class="size-14 -rotate-90" viewBox="0 0 36 36">
                            <circle cx="18" cy="18" r="15" fill="none" stroke="#E0F2FE" stroke-width="3.5"/>
                            <circle cx="18" cy="18" r="15" fill="none" stroke="#007AFF" stroke-width="3.5" stroke-dasharray="{{ $persenDiterima }}, 100" stroke-linecap="round"/>
                        </svg>
                        <span class="absolute text-xs font-extrabold text-slate-900 tabular-nums">{{ $persenDiterima }}%</span>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-bold text-slate-900">Diterima</p>
                        <p class="mt-0.5 text-[11px] text-slate-500">{{ $statistikLengkap['pelamar_diterima'] }} dari {{ $statistikLengkap['total_pelamar'] }}</p>
                    </div>
                </div>
            </div>

            <!-- Pelamar Baru Action Card -->
            <div class="flex flex-wrap items-center justify-between gap-3 rounded-3xl border-[1.5px] border-slate-200/90 bg-white/80 p-5 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.08)] backdrop-blur-xl">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Pelamar Baru</h3>
                    <p class="mt-0.5 text-xs text-slate-500">{{ $statistikLengkap['pelamar_baru'] }} pelamar baru menunggu ditinjau</p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('lowongan.index') }}" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-700 transition hover:bg-slate-50">Lowongan</a>
                    <a href="{{ route('perusahaan.pelamar.index') }}" class="rounded-full bg-[#007AFF] px-4 py-2 text-xs font-bold !text-white shadow-sm shadow-[#007AFF]/25 transition hover:bg-[#0062CC]">Tinjau</a>
                </div>
            </div>
        </div>
    </div>
</x-panel-perusahaan>
