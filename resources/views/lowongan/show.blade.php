<x-layout :judul="$lowongan->judul . ' - ' . $lowongan->perusahaan->name . ' | Lokerku'">
    @php($profilPt = $lowongan->perusahaan->profil_pendaftaran ?? [])
    <section class="pembungkus detail-lowongan-halaman" aria-labelledby="judul-lowongan">
        <nav class="jejak-halaman" aria-label="Navigasi halaman">
            <a href="{{ auth()->check() ? route('katalog') : route('home') }}">Beranda</a>
            <span aria-hidden="true">&rsaquo;</span>
            <a href="{{ route(auth()->check() ? 'katalog' : 'home', ['kategori' => $lowongan->kategori_id]) }}">{{ $lowongan->kategori->nama }}</a>
            <span aria-hidden="true">&rsaquo;</span>
            <span>{{ $lowongan->judul }}</span>
        </nav>

        @if ($errors->any())
            <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800" role="alert">{{ $errors->first() }}</div>
        @endif

        <div class="tata-letak-detail">
            <div class="kolom-utama-detail">
                <article class="kartu-detail-lowongan">
                    <header class="kepala-detail-lowongan">
                        <div class="baris-atas-detail">
                            <div>
                                <div class="mb-2 flex flex-wrap items-center gap-2">
                                    @if ($lowongan->adalahEksternal())
                                        <span class="rounded-full bg-indigo-50 px-3 py-0.5 text-xs font-semibold text-indigo-700">Lowongan Eksternal · Sumber Remotive</span>
                                    @else
                                        <span class="rounded-full bg-blue-50 px-3 py-0.5 text-xs font-semibold text-blue-700">Lowongan Internal Lokerku</span>
                                    @endif
                                </div>
                                <h1 id="judul-lowongan">{{ $lowongan->judul }}</h1>
                                <p class="subjudul-detail-pt">
                                    <a href="#profil-perusahaan">{{ $lowongan->perusahaan->name }}</a>
                                    <span aria-hidden="true">&middot;</span>
                                    <span>Diposting {{ $lowongan->created_at->locale('id')->diffForHumans() }}</span>
                                </p>
                            </div>
                            <div class="aksi-atas-detail">
                                @auth
                                    @if (auth()->user()->role === 'user')
                                        <form method="POST" action="{{ route('lowongan.simpan', $lowongan) }}">
                                            @csrf
                                            <button class="tombol tombol-sekunder" type="submit">
                                                {{ $sudahDisimpan ? 'Tersimpan' : 'Simpan' }}
                                            </button>
                                        </form>
                                        @if ($lowongan->adalahEksternal() && $lowongan->sumber_url)
                                            <a class="tombol tombol-utama" href="{{ $lowongan->sumber_url }}" target="_blank" rel="noopener noreferrer">Lamar di Situs Sumber</a>
                                        @elseif ($sudahMelamar && $lamaranPengguna)
                                            <a class="tombol tombol-sekunder" href="{{ route('lamaran.show', $lamaranPengguna) }}">Lihat Status Lamaran</a>
                                        @elseif ($lowongan->masihMenerimaLamaran())
                                            <a class="tombol tombol-utama" href="{{ route('lamaran.create', $lowongan) }}">Lamar Sekarang</a>
                                        @endif
                                    @endif
                                @else
                                    @if ($lowongan->adalahEksternal() && $lowongan->sumber_url)
                                        <a class="tombol tombol-utama" href="{{ $lowongan->sumber_url }}" target="_blank" rel="noopener noreferrer">Lamar di Situs Sumber</a>
                                    @else
                                        <a class="tombol tombol-utama" href="{{ route('login') }}">Lamar Sekarang</a>
                                    @endif
                                @endauth
                            </div>
                        </div>

                        <div class="kisi-meta-detail">
                            <div class="item-meta-detail">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                                <div>
                                    <span class="label-detail">Lokasi</span>
                                    <strong class="nilai-detail">{{ $lowongan->lokasi }}</strong>
                                </div>
                            </div>
                            <div class="item-meta-detail">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="4" y="7" width="16" height="13" rx="2"/><path d="M9 7V5h6v2M4 12h16"/></svg>
                                <div>
                                    <span class="label-detail">Tipe Pekerjaan</span>
                                    <strong class="nilai-detail">{{ $lowongan->tipe_kerja }}</strong>
                                </div>
                            </div>
                            <div class="item-meta-detail">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h10"/></svg>
                                <div>
                                    <span class="label-detail">Kategori / Fungsi</span>
                                    <strong class="nilai-detail">{{ $lowongan->kategori->nama }}</strong>
                                </div>
                            </div>
                            <div class="item-meta-detail">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M7 7V4h10v3M3 12h18M10 12v3h4v-3"/></svg>
                                <div>
                                    <span class="label-detail">Estimasi Gaji</span>
                                    <strong class="nilai-detail">
                                        @if ($lowongan->gaji_min && $lowongan->gaji_max)
                                            Rp {{ number_format($lowongan->gaji_min, 0, ',', '.') }} – {{ number_format($lowongan->gaji_max, 0, ',', '.') }} / bulan
                                        @else
                                            Tidak dicantumkan oleh sumber
                                        @endif
                                    </strong>
                                </div>
                            </div>
                            <div class="item-meta-detail">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 11h18"/></svg>
                                <div>
                                    <span class="label-detail">Batas Lamaran</span>
                                    <strong class="nilai-detail">{{ $lowongan->batas_lamaran?->format('d M Y') ?? 'Mengikuti situs sumber' }}</strong>
                                </div>
                            </div>
                            <div class="item-meta-detail">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="m9 12 2 2 4-4"/></svg>
                                <div>
                                    <span class="label-detail">Status Lowongan</span>
                                    <strong class="nilai-detail">{{ $lowongan->statusLowongan() }}</strong>
                                </div>
                            </div>
                        </div>
                    </header>

                    <div class="deskripsi-detail-lowongan">
                        <h2>Deskripsi Pekerjaan</h2>
                        <p>{!! nl2br(e($lowongan->deskripsi)) !!}</p>
                    </div>

                    <footer class="aksi-detail-lowongan">
                        @if ($lowongan->adalahEksternal() && $lowongan->sumber_url)
                            <div class="rounded-xl border border-indigo-200 bg-indigo-50/70 p-5">
                                <h2 class="text-base font-bold text-indigo-950">Lowongan Eksternal (Remotive)</h2>
                                <p class="mt-1 text-sm text-indigo-800">Proses pengajuan lamaran untuk posisi ini dilakukan langsung pada situs sumber asli.</p>
                                <a class="tombol-utama mt-4" href="{{ $lowongan->sumber_url }}" target="_blank" rel="noopener noreferrer">Lamar di Situs Sumber</a>
                            </div>
                        @elseif (! $lowongan->masihMenerimaLamaran())
                            <p class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-600">Lowongan ini sudah {{ mb_strtolower($lowongan->statusLowongan()) }} dan tidak menerima lamaran baru.</p>
                        @else
                            @auth
                                @if (auth()->user()->role === 'user')
                                    @if ($sudahMelamar && $lamaranPengguna)
                                        <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4">
                                            <div>
                                                <p class="text-sm font-semibold text-emerald-900">Anda sudah melamar posisi ini.</p>
                                                <p class="text-xs text-emerald-700">Status saat ini: {{ $lamaranPengguna->status }}</p>
                                            </div>
                                            <a class="tombol-utama" href="{{ route('lamaran.show', $lamaranPengguna) }}">Lihat Detail &amp; Tracking</a>
                                        </div>
                                    @else
                                        <div class="flex flex-wrap items-center justify-between gap-4 rounded-xl border border-blue-200 bg-blue-50/70 p-5">
                                            <div>
                                                <h2 class="text-base font-bold text-slate-900">Tertarik dengan posisi ini?</h2>
                                                <p class="mt-1 text-sm text-slate-600">Lengkapi formulir lamaran, unggah CV, surat lamaran, dan ijazah pada halaman pengajuan.</p>
                                            </div>
                                            <a class="tombol-utama shrink-0" href="{{ route('lamaran.create', $lowongan) }}">Lamar Sekarang</a>
                                        </div>
                                    @endif
                                @endif
                            @else
                                <div class="flex flex-wrap items-center justify-between gap-4 rounded-xl border border-blue-200 bg-blue-50/70 p-5">
                                    <div>
                                        <h2 class="text-base font-bold text-slate-900">Siap melamar posisi ini?</h2>
                                        <p class="mt-1 text-sm text-slate-600">Masuk terlebih dahulu dengan akun pencari kerja untuk mengisi formulir lamaran.</p>
                                    </div>
                                    <a class="tombol-utama shrink-0" href="{{ route('login') }}">Masuk untuk Melamar</a>
                                </div>
                            @endauth
                        @endif
                    </footer>
                </article>
            </div>

            <aside class="kolom-profil-pt" id="profil-perusahaan" aria-label="Profil perusahaan">
                <section class="kartu-profil-pt">
                    <div class="kepala-profil-pt">
                        <span class="inisial-perusahaan inisial-pt-besar" aria-hidden="true">{{ mb_strtoupper(mb_substr(str_replace('PT ', '', $lowongan->perusahaan->name), 0, 1)) }}</span>
                        <div>
                            <h2>{{ $lowongan->perusahaan->name }}</h2>
                            <p class="status-pt-aktif">{{ $lowongan->perusahaan->sudahTerverifikasi() ? 'Perusahaan Terverifikasi' : 'Perusahaan Terdaftar' }}</p>
                        </div>
                    </div>

                    <ul class="daftar-info-pt">
                        <li>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                            <span>{{ $profilPt['kota_perusahaan'] ?? $lowongan->lokasi }}</span>
                        </li>
                        @if (! empty($profilPt['bidang_usaha']))
                            <li>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M9 7V5h6v2"/></svg>
                                <span>{{ $profilPt['bidang_usaha'] }}</span>
                            </li>
                        @endif
                        @if (! empty($profilPt['jumlah_karyawan']))
                            <li>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="9" cy="8" r="3"/><path d="M3 20c.5-3 2.5-5 6-5s5.5 2 6 5M16 11a2.5 2.5 0 1 0 0-5M17 15c2.2.3 3.6 1.8 4 5"/></svg>
                                <span>{{ $profilPt['jumlah_karyawan'] }} Karyawan</span>
                            </li>
                        @endif
                        @if (! empty($profilPt['alamat_perusahaan']))
                            <li>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M3 21h18M5 21V7l7-4 7 4v14"/></svg>
                                <span>{{ $profilPt['alamat_perusahaan'] }}</span>
                            </li>
                        @endif
                        @if (! empty($profilPt['situs_perusahaan']))
                            <li>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a14 14 0 0 1 0 18M12 3a14 14 0 0 0 0 18"/></svg>
                                <a href="{{ $profilPt['situs_perusahaan'] }}" target="_blank" rel="noopener noreferrer">{{ $profilPt['situs_perusahaan'] }}</a>
                            </li>
                        @endif
                    </ul>

                    <div class="tentang-pt">
                        <h3>Tentang Perusahaan</h3>
                        <p>{{ $profilPt['deskripsi_perusahaan'] ?? $lowongan->perusahaan->name . ' membuka kesempatan karier melalui Lokerku.' }}</p>
                    </div>
                </section>

                @if ($lowonganLainPerusahaan->isNotEmpty())
                    <section class="kartu-profil-pt kartu-lowongan-lain-pt">
                        <h3>Lowongan Lainnya di {{ $lowongan->perusahaan->name }}</h3>
                        <ul class="daftar-lowongan-lain-pt">
                            @foreach ($lowonganLainPerusahaan as $lain)
                                <li>
                                    <a href="{{ route('lowongan.show', $lain) }}">
                                        <strong>{{ $lain->judul }}</strong>
                                        <span>{{ $lain->lokasi }} &middot; {{ $lain->tipe_kerja }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </section>
                @endif
            </aside>
        </div>
    </section>
</x-layout>
