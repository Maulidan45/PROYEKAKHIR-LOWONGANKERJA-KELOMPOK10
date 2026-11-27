<x-layout :judul="'Dashboard | Lokerku'" :panel-admin="$jenisAkun === 'admin'">
    @php($perusahaan = $jenisAkun === 'perusahaan')
    @if ($jenisAkun === 'admin')
        @include('dashboard-admin')
    @elseif ($perusahaan)
        <div class="mx-auto w-[min(72rem,calc(100%-2rem))] py-8 sm:w-[min(72rem,calc(100%-3rem))] sm:py-12">
            <section class="mx-auto min-w-0 max-w-5xl space-y-6">
                @if (! $pengguna->sudahTerverifikasi())
                    <div class="flex flex-wrap items-center justify-between gap-4 rounded-2xl border border-amber-200 bg-amber-50 p-5">
                        <div>
                            <p class="text-sm font-bold text-amber-900">Status Verifikasi: {{ $pengguna->status_verifikasi }}</p>
                            <p class="mt-1 text-xs text-amber-800">
                                @if ($pengguna->status_verifikasi === 'Ditolak')
                                    {{ $pengguna->catatan_verifikasi ?? 'Dokumen legalitas ditolak. Silakan unggah ulang dokumen yang valid.' }}
                                @else
                                    Unggah dokumen legalitas di Profil Perusahaan agar admin dapat menyetujui publikasi lowongan Anda.
                                @endif
                            </p>
                        </div>
                        <a class="tombol-utama shrink-0" href="{{ route('akun.edit') }}">Lengkapi Legalitas</a>
                    </div>
                @endif

                <!-- Statistik Lengkap Perusahaan -->
                <dl class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-xs">
                        <dt class="text-xs font-medium text-slate-500">Total Lowongan</dt>
                        <dd class="mt-2 text-2xl font-bold text-slate-900 tabular-nums">{{ $statistikLengkap['total_lowongan'] }}</dd>
                    </div>
                    <div class="rounded-2xl border border-emerald-200 bg-emerald-50/40 p-4 shadow-xs">
                        <dt class="text-xs font-medium text-emerald-800">Lowongan Aktif</dt>
                        <dd class="mt-2 text-2xl font-bold text-emerald-700 tabular-nums">{{ $statistikLengkap['lowongan_aktif'] }}</dd>
                    </div>
                    <div class="rounded-2xl border border-amber-200 bg-amber-50/40 p-4 shadow-xs">
                        <dt class="text-xs font-medium text-amber-800">Kedaluwarsa</dt>
                        <dd class="mt-2 text-2xl font-bold text-amber-700 tabular-nums">{{ $statistikLengkap['lowongan_kedaluwarsa'] }}</dd>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 shadow-xs">
                        <dt class="text-xs font-medium text-slate-600">Ditutup</dt>
                        <dd class="mt-2 text-2xl font-bold text-slate-700 tabular-nums">{{ $statistikLengkap['lowongan_ditutup'] }}</dd>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-xs">
                        <dt class="text-xs font-medium text-slate-500">Total Pelamar</dt>
                        <dd class="mt-2 text-2xl font-bold text-slate-900 tabular-nums">{{ $statistikLengkap['total_pelamar'] }}</dd>
                    </div>
                    <div class="rounded-2xl border border-blue-200 bg-blue-50/50 p-4 shadow-xs">
                        <dt class="text-xs font-medium text-blue-800">Pelamar Baru</dt>
                        <dd class="mt-2 text-2xl font-bold text-blue-700 tabular-nums">{{ $statistikLengkap['pelamar_baru'] }}</dd>
                    </div>
                    <div class="rounded-2xl border border-indigo-200 bg-indigo-50/40 p-4 shadow-xs">
                        <dt class="text-xs font-medium text-indigo-800">Sedang Ditinjau</dt>
                        <dd class="mt-2 text-2xl font-bold text-indigo-700 tabular-nums">{{ $statistikLengkap['pelamar_ditinjau'] }}</dd>
                    </div>
                    <div class="rounded-2xl border border-emerald-200 bg-white p-4 shadow-xs">
                        <dt class="text-xs font-medium text-slate-600">Pelamar Diterima</dt>
                        <dd class="mt-2 text-2xl font-bold text-emerald-700 tabular-nums">{{ $statistikLengkap['pelamar_diterima'] }}</dd>
                    </div>
                </dl>

                <div class="grid items-start gap-6 lg:grid-cols-[minmax(0,1fr)_300px]">
                    <div class="space-y-6">
                        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xs" aria-labelledby="judul-aktivitas">
                            <header class="flex flex-col justify-between gap-3 border-b border-slate-200 px-5 py-5 sm:flex-row sm:items-center sm:px-6">
                                <div>
                                    <p class="mb-1 text-xs font-bold tracking-[0.14em] text-blue-800 uppercase">Aktivitas terbaru</p>
                                    <h2 class="text-xl font-semibold tracking-tight text-slate-900" id="judul-aktivitas">Lamaran masuk</h2>
                                    <p class="mt-1 text-sm text-slate-600">Periksa kandidat dan perbarui tahapan seleksinya.</p>
                                </div>
                                <span class="inline-flex min-h-8 w-fit items-center rounded-full bg-blue-50 px-3 text-xs font-semibold text-blue-800">{{ $statistikLengkap['pelamar_baru'] }} pelamar baru</span>
                            </header>
                            @forelse ($lamaranTerbaru as $lamaran)
                                <article class="flex flex-wrap items-center gap-3 border-b border-slate-100 px-5 py-4 last:border-b-0 sm:flex-nowrap sm:gap-4 sm:px-6">
                                    <span class="grid size-9 shrink-0 place-items-center rounded-full bg-blue-50 text-sm font-bold text-blue-800" aria-hidden="true">{{ mb_strtoupper(mb_substr($lamaran->pelamar->name, 0, 1)) }}</span>
                                    <div class="min-w-0 flex-1">
                                        <strong class="block truncate text-sm font-semibold text-slate-900">{{ $lamaran->pelamar->name }}</strong>
                                        <p class="mt-0.5 truncate text-sm text-slate-600">{{ $lamaran->lowongan->judul }} · {{ $lamaran->created_at->format('d M Y') }}</p>
                                    </div>
                                    <x-status-lamaran :status="$lamaran->status" />
                                    <a class="inline-flex min-h-10 items-center text-sm font-semibold text-blue-800 hover:underline" href="{{ route('lamaran.show', $lamaran) }}">Detail</a>
                                </article>
                            @empty
                                <div class="mx-5 my-5 rounded-xl bg-slate-50 px-5 py-6 sm:mx-6">
                                    <p class="text-sm font-semibold text-slate-900">Belum ada lamaran yang masuk.</p>
                                    <a class="mt-1 inline-flex min-h-10 items-center text-sm font-medium text-blue-800" href="{{ route('lowongan.index') }}">Kelola lowongan Anda &rarr;</a>
                                </div>
                            @endforelse
                            @if ($lamaranTerbaru->isNotEmpty())
                                <div class="border-t border-slate-200 px-5 py-3 sm:px-6">
                                    <a class="inline-flex items-center text-sm font-semibold text-blue-800 hover:underline" href="{{ route('perusahaan.pelamar.index') }}">Lihat semua pelamar &rarr;</a>
                                </div>
                            @endif
                        </section>

                        @if ($jadwalTerdekat->isNotEmpty())
                            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xs">
                                <header class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                                    <h2 class="text-base font-bold text-slate-900">Jadwal Wawancara Terdekat</h2>
                                    <a class="text-xs font-semibold text-blue-700 hover:underline" href="{{ route('wawancara.index') }}">Kelola jadwal</a>
                                </header>
                                <ul class="divide-y divide-slate-100">
                                    @foreach ($jadwalTerdekat as $jadwal)
                                        <li class="flex flex-wrap items-center justify-between gap-3 px-5 py-3.5 text-sm">
                                            <div>
                                                <strong class="block font-semibold text-slate-900">{{ $jadwal->pelamar->name }}</strong>
                                                <span class="text-xs text-slate-500">{{ $jadwal->lamaran->lowongan->judul }} · {{ $jadwal->tipe }}</span>
                                            </div>
                                            <span class="rounded-lg bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-800">{{ $jadwal->tanggal->format('d M Y') }}, {{ $jadwal->jam }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </section>
                        @endif
                    </div>

                    <aside class="space-y-5">
                        <div class="rounded-2xl border border-blue-100 bg-blue-50/50 p-5">
                            <h2 class="text-base font-bold tracking-tight text-slate-900">Menu Perusahaan</h2>
                            <div class="mt-3 grid gap-1">
                                @can('create', App\Models\Lowongan::class)
                                    <a class="flex min-h-10 items-center gap-3 rounded-lg px-2.5 text-sm font-medium text-slate-700 transition hover:bg-white hover:text-blue-800" href="{{ route('lowongan.create') }}">Pasang Lowongan Baru</a>
                                @endcan
                                <a class="flex min-h-10 items-center gap-3 rounded-lg px-2.5 text-sm font-medium text-slate-700 transition hover:bg-white hover:text-blue-800" href="{{ route('lowongan.index') }}">Kelola Lowongan</a>
                                <a class="flex min-h-10 items-center gap-3 rounded-lg px-2.5 text-sm font-medium text-slate-700 transition hover:bg-white hover:text-blue-800" href="{{ route('perusahaan.pelamar.index') }}">Daftar Pelamar</a>
                                <a class="flex min-h-10 items-center gap-3 rounded-lg px-2.5 text-sm font-medium text-slate-700 transition hover:bg-white hover:text-blue-800" href="{{ route('wawancara.index') }}">Jadwal Wawancara</a>
                                <a class="flex min-h-10 items-center gap-3 rounded-lg px-2.5 text-sm font-medium text-slate-700 transition hover:bg-white hover:text-blue-800" href="{{ route('notifikasi.index') }}">Notifikasi ({{ $notifikasiTerbaru->count() }})</a>
                                <a class="flex min-h-10 items-center gap-3 rounded-lg px-2.5 text-sm font-medium text-slate-700 transition hover:bg-white hover:text-blue-800" href="{{ route('akun.edit') }}">Profil Perusahaan</a>
                            </div>
                        </div>
                    </aside>
                </div>
            </section>
        </div>
    @else
        <!-- DASHBOARD PENCARI KERJA -->
        <div class="pembungkus halaman-rekomendasi space-y-6">
            <!-- Kelengkapan Profil & Ringkasan -->
            <div class="grid gap-4 lg:grid-cols-[minmax(0,1fr)_320px]">
                <dl class="grid grid-cols-2 gap-3 sm:grid-cols-5">
                    @foreach ($ringkasan as $label => $nilai)
                        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-xs">
                            <dt class="text-xs font-medium text-slate-500">{{ $label }}</dt>
                            <dd class="mt-2 text-2xl font-bold text-slate-900 tabular-nums">{{ $nilai }}</dd>
                        </div>
                    @endforeach
                </dl>

                <div class="rounded-2xl border border-blue-200 bg-blue-50/60 p-4">
                    <div class="flex items-center justify-between text-sm">
                        <strong class="font-bold text-slate-900">Kelengkapan Profil</strong>
                        <span class="font-bold text-blue-700 tabular-nums">{{ $kelengkapanProfil }}%</span>
                    </div>
                    <div class="mt-2 h-2.5 w-full overflow-hidden rounded-full bg-blue-100">
                        <div class="h-full rounded-full bg-blue-600 transition-all" style="width: {{ $kelengkapanProfil }}%"></div>
                    </div>
                    <div class="mt-2 flex items-center justify-between text-xs">
                        <span class="text-slate-600">Lengkapi profil untuk rekomendasi akurat</span>
                        <a class="font-semibold text-blue-700 hover:underline" href="{{ route('akun.edit') }}">Lengkapi</a>
                    </div>
                </div>
            </div>

            <!-- Notifikasi, Wawancara & Riwayat Aktivitas -->
            @if ($notifikasiTerbaru->isNotEmpty() || $jadwalWawancara->isNotEmpty() || $riwayatAktivitas->isNotEmpty())
                <div class="grid gap-5 lg:grid-cols-3">
                    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs">
                        <div class="flex items-center justify-between">
                            <h2 class="text-base font-bold text-slate-900">Notifikasi Terbaru</h2>
                            <a class="text-xs font-semibold text-blue-700 hover:underline" href="{{ route('notifikasi.index') }}">Semua</a>
                        </div>
                        <ul class="mt-3 space-y-2.5 text-sm">
                            @forelse ($notifikasiTerbaru as $notif)
                                <li class="rounded-xl bg-blue-50/50 p-3">
                                    <strong class="block text-xs font-semibold text-slate-900">{{ $notif->data['judul_lowongan'] ?? 'Pembaruan' }}</strong>
                                    <p class="mt-0.5 text-xs text-slate-600">{{ $notif->data['pesan'] ?? ($notif->data['status'] ?? '-') }}</p>
                                </li>
                            @empty
                                <li class="text-xs text-slate-500">Tidak ada notifikasi belum dibaca.</li>
                            @endforelse
                        </ul>
                    </section>

                    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs">
                        <div class="flex items-center justify-between">
                            <h2 class="text-base font-bold text-slate-900">Jadwal Wawancara</h2>
                            <a class="text-xs font-semibold text-blue-700 hover:underline" href="{{ route('wawancara.index') }}">Lihat semua</a>
                        </div>
                        <ul class="mt-3 space-y-2.5 text-sm">
                            @forelse ($jadwalWawancara as $jadwal)
                                <li class="rounded-xl border border-amber-200 bg-amber-50/60 p-3">
                                    <strong class="block text-xs font-bold text-slate-900">{{ $jadwal->lamaran->lowongan->judul }}</strong>
                                    <p class="text-xs text-slate-600">{{ $jadwal->perusahaan->name }}</p>
                                    <p class="mt-1 text-xs font-semibold text-amber-800">{{ $jadwal->tanggal->format('d M Y') }} · {{ $jadwal->jam }} ({{ $jadwal->tipe }})</p>
                                </li>
                            @empty
                                <li class="text-xs text-slate-500">Belum ada jadwal wawancara mendatang.</li>
                            @endforelse
                        </ul>
                    </section>

                    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs">
                        <div class="flex items-center justify-between">
                            <h2 class="text-base font-bold text-slate-900">Riwayat Aktivitas</h2>
                            <a class="text-xs font-semibold text-blue-700 hover:underline" href="{{ route('lamaran.index') }}">Riwayat Lamaran</a>
                        </div>
                        <ul class="mt-3 space-y-2.5 text-sm">
                            @forelse ($riwayatAktivitas as $aktivitas)
                                <li class="flex items-center justify-between gap-2 rounded-xl bg-slate-50 p-3">
                                    <div class="min-w-0">
                                        <a class="block truncate text-xs font-bold text-slate-900 hover:text-blue-700" href="{{ route('lamaran.show', $aktivitas) }}">{{ $aktivitas->lowongan->judul }}</a>
                                        <span class="block truncate text-xs text-slate-500">{{ $aktivitas->lowongan->perusahaan->name }}</span>
                                    </div>
                                    <x-status-lamaran :status="$aktivitas->status" />
                                </li>
                            @empty
                                <li class="text-xs text-slate-500">Belum ada aktivitas lamaran.</li>
                            @endforelse
                        </ul>
                    </section>
                </div>
            @endif

            @if ($lowonganTersimpan->isNotEmpty())
                <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs" aria-label="Lowongan favorit">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-bold text-slate-900">Lowongan Favorit</h2>
                        <a class="text-xs font-semibold text-blue-700 hover:underline" href="{{ route('katalog') }}">Cari lebih banyak</a>
                    </div>
                    <div class="mt-3 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($lowonganTersimpan as $tersimpan)
                            <a class="rounded-xl border border-slate-200 p-3.5 transition hover:border-blue-400 hover:bg-blue-50/30" href="{{ route('lowongan.show', $tersimpan) }}">
                                <strong class="block truncate text-sm font-bold text-slate-900">{{ $tersimpan->judul }}</strong>
                                <span class="block truncate text-xs text-slate-600">{{ $tersimpan->perusahaan->name }} · {{ $tersimpan->lokasi }}</span>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            <!-- Pencarian & Rekomendasi Job Matching -->
            <form class="pencarian-dashboard" action="{{ route('dashboard.role', ['role' => 'user']) }}" method="GET" role="search" aria-label="Cari lowongan" autocomplete="off">
                <label class="teks-tersembunyi" for="kata-kunci-dashboard">Kata kunci lowongan</label>
                <input id="kata-kunci-dashboard" name="kata_kunci" value="{{ $penyaring['kata_kunci'] ?? '' }}" placeholder="Masukkan kata kunci" autocomplete="off">
                <label class="penyaring-dashboard" for="lokasi-dashboard">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M19 10c0 5.5-7 11-7 11S5 15.5 5 10a7 7 0 1 1 14 0Z"/><circle cx="12" cy="10" r="2.2"/></svg>
                    <select id="lokasi-dashboard" name="lokasi" aria-label="Semua lokasi">
                        <option value="">Semua Lokasi</option>
                        @foreach ($daftarLokasi as $lokasiTersedia)
                            <option value="{{ $lokasiTersedia }}" @selected(($penyaring['lokasi'] ?? '') === $lokasiTersedia)>{{ $lokasiTersedia }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="penyaring-dashboard" for="kategori-dashboard">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="4" y="7" width="16" height="13" rx="2"/><path d="M9 7V5h6v2M4 12h16M10 15h4"/></svg>
                    <select id="kategori-dashboard" name="kategori" aria-label="Semua kategori">
                        <option value="">Semua Kategori</option>
                        @foreach ($daftarKategori as $kategoriTersedia)
                            <option value="{{ $kategoriTersedia->id }}" @selected(($penyaring['kategori'] ?? '') == $kategoriTersedia->id)>{{ $kategoriTersedia->nama }}</option>
                        @endforeach
                    </select>
                </label>
                <button type="submit" aria-label="Cari lowongan"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.8"/><path d="m16 16 4 4"/></svg></button>
            </form>

            <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                <div>
                    <h2 class="text-xl font-bold text-slate-900">Rekomendasi Pekerjaan Untuk Anda</h2>
                    <p class="text-xs text-slate-500">Dihitung berdasarkan bobot kecocokan: keahlian (35%), pengalaman (20%), pendidikan (15%), lokasi (15%), dan kategori (15%).</p>
                </div>
            </div>

            <div class="kisi-rekomendasi">
                @forelse ($daftarLowongan as $lowongan)
                    @php($hasilCocok = $rekomendasiSkor->get($lowongan->id))
                    <article class="kartu-rekomendasi">
                        <div class="alasan-rekomendasi">
                            <strong>{{ $hasilCocok['skor'] ?? 0 }}% Cocok</strong>
                            <span>{{ implode(' · ', array_slice($hasilCocok['alasan'] ?? [], 0, 2)) }}</span>
                        </div>
                        <div class="identitas-rekomendasi">
                            <span class="logo-rekomendasi" aria-hidden="true">{{ mb_strtoupper(mb_substr(str_replace('PT ', '', $lowongan->perusahaan->name), 0, 1)) }}</span>
                            <div>
                                <p>{{ $lowongan->perusahaan->name }}</p>
                                <h2><a href="{{ route('lowongan.show', $lowongan) }}">{{ $lowongan->judul }}</a></h2>
                                <span class="lokasi-rekomendasi"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M19 10c0 5.5-7 11-7 11S5 15.5 5 10a7 7 0 1 1 14 0Z"/><circle cx="12" cy="10" r="2.2"/></svg>{{ $lowongan->lokasi }}</span>
                            </div>
                        </div>
                        <ul class="tag-rekomendasi">
                            <li>{{ $lowongan->tipe_kerja }}</li>
                            <li>{{ $lowongan->kategori->nama }}</li>
                            @if ($lowongan->adalahEksternal())
                                <li>Eksternal · Remotive</li>
                            @endif
                        </ul>
                        <div class="bawah-rekomendasi">
                            <span>
                                @if ($lowongan->gaji_min && $lowongan->gaji_max)
                                    Rp {{ number_format($lowongan->gaji_min, 0, ',', '.') }}–{{ number_format($lowongan->gaji_max, 0, ',', '.') }} / bulan
                                @else
                                    Gaji sesuai ketentuan
                                @endif
                            </span>
                            <span>{{ $lowongan->batas_lamaran ? 'Batas ' . $lowongan->batas_lamaran->format('d M Y') : 'Sumber Eksternal' }}</span>
                        </div>
                        <div class="aksi-kartu-lowongan flex flex-wrap gap-2">
                            <a class="tombol-utama flex-1 text-center" href="{{ route('lowongan.show', $lowongan) }}">Lihat &amp; Lamar</a>
                        </div>
                    </article>
                @empty
                    <p class="kosong-rekomendasi">Belum ada lowongan aktif. Silakan cek kembali nanti.</p>
                @endforelse
            </div>
            <div class="paginasi-katalog">{{ $daftarLowongan->links() }}</div>
        </div>
    @endif
</x-layout>
