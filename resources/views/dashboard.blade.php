<x-layout :judul="'Dashboard | Lokerku'">
    @php($perusahaan = $jenisAkun === 'perusahaan')
    <div @class(['mx-auto w-[min(72rem,calc(100%-2rem))] py-8 sm:w-[min(72rem,calc(100%-3rem))] sm:py-12' => $perusahaan, 'pembungkus halaman-aplikasi' => ! $perusahaan && $jenisAkun === 'admin', 'pembungkus halaman-rekomendasi' => $jenisAkun === 'user'])>
        @if ($jenisAkun === 'admin')
            <x-menu-peran :jenis-akun="$jenisAkun" />
        @endif
        <section @class(['mx-auto min-w-0 max-w-5xl' => $jenisAkun !== 'user'])>
            @if ($perusahaan)
                <dl class="mb-7 grid gap-3 sm:grid-cols-3 sm:gap-4 lg:mb-8">
                    @foreach ($ringkasan as $label => $nilai)
                        @php($ikon = match ($label) {
                            'Lowongan aktif' => 'M4 7h16v12H4zM8 7V5h8v2M4 11h16M10 15h4',
                            'Total lamaran' => 'M6 3h9l3 3v15H6zM9 12h6M9 16h6',
                            default => 'M12 7v5l3 2M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
                        })
                        <div @class(['rounded-lg border bg-white p-5', 'border-slate-200' => $label !== 'Perlu ditinjau', 'border-amber-300 bg-amber-50/50' => $label === 'Perlu ditinjau'])>
                            <dt class="flex items-center gap-2 text-sm font-medium text-slate-600"><svg class="size-4 shrink-0 text-blue-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="{{ $ikon }}"/></svg>{{ $label }}</dt><dd class="mt-3 text-3xl font-bold tracking-tight text-slate-900 tabular-nums">{{ $nilai }}</dd>
                        </div>
                    @endforeach
                </dl>
                <div class="grid items-start gap-5 lg:grid-cols-[minmax(0,1fr)_280px]">
                    <section class="overflow-hidden rounded-lg border border-slate-200 bg-white" aria-labelledby="judul-aktivitas">
                        <header class="flex flex-col justify-between gap-3 border-b border-slate-200 px-5 py-5 sm:flex-row sm:items-center sm:px-6">
                            <div><p class="mb-1 text-xs font-bold tracking-[0.14em] text-blue-800 uppercase">Aktivitas terbaru</p><h2 class="text-xl font-semibold tracking-tight text-slate-900" id="judul-aktivitas">Lamaran masuk</h2><p class="mt-1 text-sm text-slate-600">Periksa kandidat dan perbarui status lamarannya.</p></div>
                            <span class="inline-flex min-h-8 w-fit items-center rounded-md bg-amber-50 px-3 text-sm font-medium text-amber-800">{{ $ringkasan['Perlu ditinjau'] }} perlu ditinjau</span>
                        </header>
                        @forelse ($lamaranTerbaru as $lamaran)
                            <article class="flex flex-wrap items-center gap-3 border-b border-slate-100 px-5 py-4 last:border-b-0 sm:flex-nowrap sm:gap-4 sm:px-6">
                                <span class="grid size-9 shrink-0 place-items-center rounded-full bg-blue-50 text-sm font-bold text-blue-800" aria-hidden="true">{{ mb_strtoupper(mb_substr($lamaran->pelamar->name, 0, 1)) }}</span>
                                <div class="min-w-0 flex-1"><strong class="block truncate text-sm font-semibold text-slate-900">{{ $lamaran->pelamar->name }}</strong><p class="mt-0.5 truncate text-sm text-slate-600">{{ $lamaran->lowongan->judul }} · {{ $lamaran->created_at->format('d M Y') }}</p></div>
                                <x-status-lamaran :status="$lamaran->status" />
                                <a class="inline-flex min-h-10 items-center font-semibold text-blue-800" href="{{ route('lowongan.pelamar', $lamaran->lowongan) }}">Lihat kandidat</a>
                            </article>
                        @empty
                            <div class="mx-5 my-5 rounded-md bg-slate-50 px-5 py-6 sm:mx-6">
                                @if ($ringkasan['Lowongan aktif'] > 0)
                                    <p class="text-sm font-semibold text-slate-900">Belum ada lamaran yang masuk.</p><a class="mt-1 inline-flex min-h-10 items-center text-sm font-medium text-blue-800" href="{{ route('lowongan.index') }}">Tinjau lowongan aktif Anda.</a>
                                @else
                                    <p class="text-sm font-semibold text-slate-900">Belum ada lowongan aktif.</p><a class="mt-1 inline-flex min-h-10 items-center text-sm font-medium text-blue-800" href="{{ route('lowongan.index') }}">Buka Lowongan saya untuk memasang posisi pertama.</a>
                                @endif
                            </div>
                        @endforelse
                        @if ($lamaranTerbaru->isNotEmpty())
                            <div class="border-t border-slate-200 px-5 py-2 sm:px-6"><a class="inline-flex min-h-10 items-center text-sm font-semibold text-blue-800" href="{{ route('perusahaan.pelamar.index') }}">Lihat semua pelamar</a></div>
                        @endif
                    </section>
                    <aside class="rounded-lg border border-blue-100 bg-blue-50/50 p-5">
                        <h2 class="text-base font-semibold tracking-tight text-slate-900">Aksi cepat</h2>
                        <div class="mt-3 grid gap-1">
                            <a class="flex min-h-11 items-center gap-3 rounded-md px-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50 hover:text-blue-800" href="{{ route('lowongan.index') }}"><svg class="size-4 shrink-0 text-blue-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>Kelola lowongan</a>
                            <a class="flex min-h-11 items-center gap-3 rounded-md px-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50 hover:text-blue-800" href="{{ route('perusahaan.pelamar.index') }}"><svg class="size-4 shrink-0 text-blue-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="9" cy="8" r="3"/><path d="M3 20c.5-3 2.5-5 6-5s5.5 2 6 5"/></svg>Tinjau semua pelamar</a>
                            <a class="flex min-h-11 items-center gap-3 rounded-md px-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50 hover:text-blue-800" href="{{ route('akun.edit') }}"><svg class="size-4 shrink-0 text-blue-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="3.25"/><path d="M5 20c.7-3.2 3.1-5 7-5s6.3 1.8 7 5"/></svg>Perbarui data perusahaan</a>
                        </div>
                    </aside>
                </div>
            @else
                @if ($jenisAkun === 'user')
                    <form class="pencarian-dashboard" action="{{ route('dashboard.role', ['role' => 'user']) }}" method="GET" role="search" aria-label="Cari lowongan" autocomplete="off"><label class="teks-tersembunyi" for="kata-kunci-dashboard">Kata kunci lowongan</label><input id="kata-kunci-dashboard" name="kata_kunci" value="{{ $penyaring['kata_kunci'] ?? '' }}" placeholder="Masukkan kata kunci" autocomplete="off"><label class="penyaring-dashboard" for="lokasi-dashboard"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M19 10c0 5.5-7 11-7 11S5 15.5 5 10a7 7 0 1 1 14 0Z"/><circle cx="12" cy="10" r="2.2"/></svg><select id="lokasi-dashboard" name="lokasi" aria-label="Semua lokasi"><option value="">Semua Lokasi</option>@foreach ($daftarLokasi as $lokasiTersedia)<option value="{{ $lokasiTersedia }}" @selected(($penyaring['lokasi'] ?? '') === $lokasiTersedia)>{{ $lokasiTersedia }}</option>@endforeach</select></label><label class="penyaring-dashboard" for="kategori-dashboard"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="4" y="7" width="16" height="13" rx="2"/><path d="M9 7V5h6v2M4 12h16M10 15h4"/></svg><select id="kategori-dashboard" name="kategori" aria-label="Semua kategori"><option value="">Semua Kategori</option>@foreach ($daftarKategori as $kategoriTersedia)<option value="{{ $kategoriTersedia->id }}" @selected(($penyaring['kategori'] ?? '') == $kategoriTersedia->id)>{{ $kategoriTersedia->nama }}</option>@endforeach</select></label><button type="submit" aria-label="Cari lowongan"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.8"/><path d="m16 16 4 4"/></svg></button></form>
                    <div class="kisi-rekomendasi">
                        @forelse ($daftarLowongan as $lowongan)
                            <article class="kartu-rekomendasi">
                                <div class="alasan-rekomendasi"><strong>Cocok</strong><span>{{ $lowongan->kategori->nama }} · {{ $lowongan->lokasi }}</span></div>
                                <div class="identitas-rekomendasi"><span class="logo-rekomendasi" aria-hidden="true">{{ mb_strtoupper(mb_substr(str_replace('PT ', '', $lowongan->perusahaan->name), 0, 1)) }}</span><div><p>{{ $lowongan->perusahaan->name }}</p><h2><a href="{{ route('home', ['kata_kunci' => $lowongan->judul]) }}">{{ $lowongan->judul }}</a></h2><span class="lokasi-rekomendasi"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M19 10c0 5.5-7 11-7 11S5 15.5 5 10a7 7 0 1 1 14 0Z"/><circle cx="12" cy="10" r="2.2"/></svg>{{ $lowongan->lokasi }}</span></div></div>
                                <ul class="tag-rekomendasi"><li>{{ $lowongan->tipe_kerja }}</li><li>{{ $lowongan->kategori->nama }}</li><li>Lamaran terbuka</li></ul>
                                <div class="bawah-rekomendasi"><span>Rp {{ number_format($lowongan->gaji_min, 0, ',', '.') }}–{{ number_format($lowongan->gaji_max, 0, ',', '.') }} / bulan</span><span>Batas {{ $lowongan->batas_lamaran->format('d M Y') }}</span></div>
                                <form class="aksi-kartu-lowongan" method="POST" action="{{ route('lamaran.store', $lowongan) }}">@csrf<button class="tombol tombol-sekunder" type="submit">Lamar lowongan</button></form>
                            </article>
                        @empty
                            <p class="kosong-rekomendasi">Belum ada lowongan aktif. Silakan cek kembali nanti.</p>
                        @endforelse
                    </div>
                    <div class="paginasi-katalog">{{ $daftarLowongan->links() }}</div>
                @else
                <header class="mb-8 flex flex-col items-start justify-between gap-5 border-b border-slate-200 pb-7 sm:mb-10 sm:flex-row sm:items-center sm:pb-8">
                    <div>
                        <p class="mb-2 text-xs font-bold tracking-[0.14em] text-blue-800 uppercase">{{ $jenisAkunTersedia[$jenisAkun] }}</p>
                        <h1 class="max-w-3xl text-3xl leading-tight font-bold tracking-tight text-slate-900 sm:text-4xl">{{ $jenisAkun === 'admin' ? 'Pusat kendali data' : 'Pantau lamaranmu' }}</h1>
                        <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600 sm:text-base">{{ $jenisAkun === 'admin' ? 'Tinjau akun, lowongan, dan kategori dari satu tempat.' : $pengguna->name }}</p>
                    </div>
                </header>
                <dl class="mb-8 grid grid-cols-1 gap-3 sm:grid-cols-3 sm:gap-4">@foreach ($ringkasan as $label => $nilai)<div class="rounded-lg border border-slate-200 bg-white p-5"><dt class="text-sm font-medium text-slate-600">{{ $label }}</dt><dd class="mt-3 text-3xl font-bold tracking-tight text-slate-900 tabular-nums">{{ $nilai }}</dd></div>@endforeach</dl>
                <section id="akun-saya" class="panel-profil" tabindex="-1"><h2>Informasi akun</h2><dl><div><dt>Email</dt><dd>{{ $pengguna->email }}</dd></div><div><dt>Jenis akun</dt><dd>{{ $jenisAkunTersedia[$jenisAkun] }}</dd></div></dl>
                    @if ($pengguna->profil_pendaftaran)<h3 class="judul-profil">Profil pendaftaran</h3><dl>@foreach ($pengguna->profil_pendaftaran as $kunci => $nilai)<div><dt>{{ str_replace('_', ' ', $kunci) }}</dt><dd>{{ $nilai }}</dd></div>@endforeach</dl>@endif
                </section>
                @if ($jenisAkun === 'admin')
                    <section class="panel-profil"><h2>Akun terdaftar</h2><dl class="ringkasan-kerja">@foreach ($jenisAkunTersedia as $kunci => $label)<div><dt>{{ $label }}</dt><dd>{{ $jumlahAkun[$kunci] ?? 0 }}</dd></div>@endforeach</dl><ul class="daftar-akun">@foreach ($daftarAkun as $akun)<li><span><strong>{{ $akun->name }}</strong><br>{{ $akun->email }}</span><span>{{ $jenisAkunTersedia[$akun->role] }}</span></li>@endforeach</ul>{{ $daftarAkun->links() }}</section>
                @endif
                @endif
            @endif
        </section>
    </div>
</x-layout>