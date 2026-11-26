<x-layout :judul="$lowongan->judul . ' - ' . $lowongan->perusahaan->name . ' | Lokerku'">
    @php($profilPt = $lowongan->perusahaan->profil_pendaftaran ?? [])
    <section class="pembungkus detail-lowongan-halaman" aria-labelledby="judul-lowongan">
        <nav class="jejak-halaman" aria-label="Navigasi halaman">
            <a href="{{ route('home') }}">Beranda</a>
            <span aria-hidden="true">&rsaquo;</span>
            <a href="{{ route('home', ['kategori' => $lowongan->kategori_id]) }}">{{ $lowongan->kategori->nama }}</a>
            <span aria-hidden="true">&rsaquo;</span>
            <span>{{ $lowongan->judul }}</span>
        </nav>

        <div class="tata-letak-detail">
            <div class="kolom-utama-detail">
                <article class="kartu-detail-lowongan">
                    <header class="kepala-detail-lowongan">
                        <div class="baris-atas-detail">
                            <div>
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
                                        @unless ($sudahMelamar)
                                            <a class="tombol tombol-utama" href="#form-lamar">Lamar Sekarang</a>
                                        @endunless
                                    @endif
                                @else
                                    <a class="tombol tombol-utama" href="{{ route('login') }}">Lamar Sekarang</a>
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
                                    <strong class="nilai-detail">Rp {{ number_format($lowongan->gaji_min, 0, ',', '.') }} – {{ number_format($lowongan->gaji_max, 0, ',', '.') }} / bulan</strong>
                                </div>
                            </div>
                            <div class="item-meta-detail">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 11h18"/></svg>
                                <div>
                                    <span class="label-detail">Batas Lamaran</span>
                                    <strong class="nilai-detail">{{ $lowongan->batas_lamaran->format('d M Y') }}</strong>
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

                    <footer class="aksi-detail-lowongan" id="form-lamar">
                        @auth
                            @if (auth()->user()->role === 'user')
                                @if ($sudahMelamar)
                                    <p class="status-sudah-lamar">Anda sudah melamar posisi ini.</p>
                                @else
                                    <h2>Kirim Lamaran</h2>
                                    <form class="form-lamar-detail" method="POST" action="{{ route('lamaran.store', $lowongan) }}" enctype="multipart/form-data" data-kirim>
                                        @csrf
                                        <div class="bidang-formulir">
                                            <label for="cv-lamaran">Unggah CV (PDF, opsional, maks. 2 MB)</label>
                                            <input id="cv-lamaran" type="file" name="cv" accept="application/pdf,.pdf">
                                            @error('cv')<p class="pesan-galat">{{ $message }}</p>@enderror
                                        </div>
                                        <div class="bidang-formulir">
                                            <label for="catatan-lamaran">Catatan singkat untuk perusahaan (opsional)</label>
                                            <textarea id="catatan-lamaran" name="catatan" rows="3" placeholder="Ceritakan singkat pengalaman atau kesiapan Anda...">{{ old('catatan') }}</textarea>
                                            @error('catatan')<p class="pesan-galat">{{ $message }}</p>@enderror
                                        </div>
                                        <button class="tombol tombol-utama" type="submit" data-teks-proses="Mengirim lamaran...">Lamar Sekarang</button>
                                    </form>
                                @endif
                            @endif
                        @else
                            <a class="tombol tombol-utama" href="{{ route('login') }}">Masuk untuk melamar</a>
                        @endauth
                    </footer>
                </article>
            </div>

            <aside class="kolom-profil-pt" id="profil-perusahaan" aria-label="Profil perusahaan">
                <section class="kartu-profil-pt">
                    <div class="kepala-profil-pt">
                        <span class="inisial-perusahaan inisial-pt-besar" aria-hidden="true">{{ mb_strtoupper(mb_substr(str_replace('PT ', '', $lowongan->perusahaan->name), 0, 1)) }}</span>
                        <div>
                            <h2>{{ $lowongan->perusahaan->name }}</h2>
                            <p class="status-pt-aktif">Perusahaan Terdaftar</p>
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
