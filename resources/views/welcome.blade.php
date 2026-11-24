<x-layout judul="Lokerku | Lowongan kerja" :beranda="true">
    <section class="sorotan-karier" aria-labelledby="judul-sorotan">
        <div class="pembungkus isi-sorotan">
            <div class="isi-utama">
                <h1 id="judul-sorotan">#CariKerja makin mudah<br>Pakai <strong>lokerku</strong></h1>
                <form class="panel-pencarian" action="{{ route('home') }}" method="GET" role="search" aria-label="Cari lowongan" autocomplete="off">
                    <div class="pencarian-kata">
                        <label class="teks-tersembunyi" for="kata-kunci">Kata kunci lowongan</label>
                        <input id="kata-kunci" name="kata_kunci" value="{{ $cari }}" placeholder="Masukkan kata kunci" autocomplete="off">
                        <button class="tombol tombol-cari" type="submit" aria-label="Cari lowongan">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.8"/><path d="m16 16 4 4"/></svg>
                        </button>
                    </div>
                    <div class="penyaring-pencarian">
                        <label class="kolom-dengan-ikon" for="pilih-lokasi">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M19 10c0 5.5-7 11-7 11S5 15.5 5 10a7 7 0 1 1 14 0Z"/><circle cx="12" cy="10" r="2.2"/></svg>
                            <span class="teks-tersembunyi">Cari lokasi</span>
                            <select id="pilih-lokasi" name="lokasi" aria-label="Cari lokasi">
                                <option value="">Cari lokasi</option>
                                @foreach ($daftarLokasi as $lokasiTersedia)
                                    <option value="{{ $lokasiTersedia }}" @selected($lokasi === $lokasiTersedia)>{{ $lokasiTersedia }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label class="kolom-dengan-ikon" for="pilih-kategori">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="4" y="7" width="16" height="13" rx="2"/><path d="M9 7V5h6v2M4 12h16M10 15h4"/></svg>
                            <span class="teks-tersembunyi">Cari kategori</span>
                            <select id="pilih-kategori" name="kategori" aria-label="Cari kategori">
                                <option value="">Cari kategori</option>
                                @foreach ($daftarKategori as $kategoriTersedia)
                                    <option value="{{ $kategoriTersedia->id }}" @selected($kategori === (string) $kategoriTersedia->id)>{{ $kategoriTersedia->nama }}</option>
                                @endforeach
                            </select>
                        </label>
                    </div>
                    <input type="hidden" name="tipe" value="{{ $tipe }}">
                    <p class="saran-pencarian"><strong>Paling sering dicari:</strong>
                        @foreach (['Staf Admin', 'Staf Accounting', 'Pemasaran Digital', 'E-Commerce & Marketplace', 'Administrasi Penjualan', 'Driver', 'Konsultan Penjualan', 'Content Marketing', 'Staf HRD'] as $saran)
                            <a href="{{ route('home', ['kata_kunci' => $saran]) }}">{{ $saran }}</a>
                        @endforeach
                    </p>
                </form>
            </div>
            <figure class="ilustrasi-utama">
                <img src="{{ asset('images/career-team.jpg') }}" alt="Tim berdiskusi dan bekerja sama di sekitar meja" width="1000" height="667" fetchpriority="high">
            </figure>
        </div>
    </section>

    <section class="pembungkus katalog" id="lowongan" aria-labelledby="judul-katalog">
        <header class="kepala-katalog">
            <div>
                <p class="label-katalog">Temukan kesempatan berikutnya</p>
                <h2 id="judul-katalog">Lowongan Kerja Terbaru</h2>
            </div>
            <p class="jumlah-hasil">{{ $daftarLowongan->total() }} lowongan tersedia</p>
        </header>
        <nav class="penyaring-kategori" aria-label="Kategori lowongan">
            @foreach ($daftarKategori->take(4) as $kategoriTersedia)
                <a class="tautan-kategori {{ $kategori === (string) $kategoriTersedia->id ? 'sedang-aktif' : '' }}" href="{{ route('home', array_filter(['kategori' => $kategoriTersedia->id, 'kata_kunci' => $cari, 'lokasi' => $lokasi, 'tipe' => $tipe])) }}" @if ($kategori === (string) $kategoriTersedia->id) aria-current="page" @endif>{{ $kategoriTersedia->nama }}</a>
            @endforeach
            @if ($daftarKategori->count() > 4)
                <details class="kategori-lainnya">
                    <summary>Lainnya <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg></summary>
                    <div class="menu-kategori-lainnya">
                        @foreach ($daftarKategori->skip(4) as $kategoriTersedia)
                            <a href="{{ route('home', array_filter(['kategori' => $kategoriTersedia->id, 'kata_kunci' => $cari, 'lokasi' => $lokasi, 'tipe' => $tipe])) }}">{{ $kategoriTersedia->nama }}</a>
                        @endforeach
                    </div>
                </details>
            @endif
        </nav>
        <form class="alat-katalog" action="{{ route('home') }}" method="GET">
            <p class="keterangan-katalog">Jelajahi posisi yang sesuai denganmu</p>
            <div class="penyaring-tipe">
                <label for="pilih-tipe">Tipe kerja</label>
                <select id="pilih-tipe" name="tipe">
                    <option value="">Semua tipe</option>
                    @foreach (['Penuh waktu', 'Paruh waktu', 'Magang', 'Jarak jauh'] as $tipeTersedia)
                        <option value="{{ $tipeTersedia }}" @selected($tipe === $tipeTersedia)>{{ $tipeTersedia }}</option>
                    @endforeach
                </select>
                <input type="hidden" name="kata_kunci" value="{{ $cari }}">
                <input type="hidden" name="kategori" value="{{ $kategori }}">
                <input type="hidden" name="lokasi" value="{{ $lokasi }}">
                <button class="tombol" type="submit">Terapkan</button>
            </div>
        </form>
        @if (filled($cari) || filled($kategori) || filled($lokasi) || filled($tipe))
            <p class="penyaring-aktif">Filter sedang diterapkan. <a href="{{ route('home') }}">Hapus semua filter</a></p>
        @endif
        <div class="daftar-lowongan">
            @forelse ($daftarLowongan as $lowongan)
                <article class="kartu-lowongan kartu-lowongan-publik">
                    <div class="identitas-lowongan">
                        <span class="inisial-perusahaan" aria-hidden="true">{{ mb_strtoupper(mb_substr(str_replace('PT ', '', $lowongan->perusahaan->name), 0, 1)) }}</span>
                        <div>
                            <p class="perusahaan-lowongan">{{ $lowongan->perusahaan->name }}</p>
                            <h3>{{ $lowongan->judul }}</h3>
                            <p class="lokasi-lowongan"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>{{ $lowongan->lokasi }}</p>
                        </div>
                    </div>
                    <ul class="rincian-lowongan" aria-label="Informasi posisi">
                        <li>{{ $lowongan->tipe_kerja }}</li>
                        <li>{{ $lowongan->kategori->nama }}</li>
                    </ul>
                    <div class="bagian-bawah-lowongan">
                        <p class="gaji-lowongan"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M7 7V4h10v3M3 12h18M10 12v3h4v-3"/></svg>Rp {{ number_format($lowongan->gaji_min, 0, ',', '.') }}–{{ number_format($lowongan->gaji_max, 0, ',', '.') }}<span class="periode-gaji">/ bulan</span></p>
                        <span class="kode-lowongan">Batas {{ $lowongan->batas_lamaran->format('d M Y') }}</span>
                    </div>
                    @auth
                        @if (auth()->user()->role === 'user')
                            <form class="aksi-kartu-lowongan" method="POST" action="{{ route('lamaran.store', $lowongan) }}">
                                @csrf
                                <button class="tombol tombol-sekunder" type="submit">Lamar lowongan</button>
                            </form>
                        @endif
                    @endauth
                </article>
            @empty
                    <h3>Belum ada lowongan yang sesuai dengan pencarianmu.</h3>
                    <p>Coba ubah kata kunci atau filter untuk melihat lowongan lainnya.</p>
                    <a class="tombol tombol-sekunder" href="{{ route('home') }}">Lihat semua lowongan</a>
                    <a class="tombol tombol-sekunder" href="{{ route('home') }}">Hapus semua filter</a>
                </div>
            @endforelse
        </div>
        <div class="paginasi-katalog">{{ $daftarLowongan->links() }}</div>
    </section>
</x-layout>