<x-layout judul="Lokerku | Lowongan kerja" :beranda="true">
    <section class="sorotan-karier" aria-labelledby="judul-sorotan">
        <div class="pembungkus isi-sorotan">
            <div class="isi-utama">
                <h1 id="judul-sorotan">#CariKerja makin mudah<br>Pakai <strong>lokerku</strong></h1>
                <form class="panel-pencarian" action="{{ route('home') }}" method="GET" role="search" aria-label="Cari lowongan" autocomplete="off" data-pencarian-langsung data-api-lowongan="{{ route('api.v1.lowongan.index') }}" data-api-kabupaten="{{ route('api.v1.wilayah.kabupaten') }}" data-home-url="{{ route('home') }}">
                    <div class="pencarian-kata">
                        <label class="teks-tersembunyi" for="kata-kunci">Kata kunci lowongan</label>
                        <input id="kata-kunci" name="kata_kunci" value="{{ $cari }}" placeholder="Masukkan kata kunci" autocomplete="off">
                        <button class="tombol tombol-cari" type="submit" aria-label="Cari lowongan">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.8"/><path d="m16 16 4 4"/></svg>
                        </button>
                    </div>
                    <div class="penyaring-pencarian">
                        <label class="kolom-dengan-ikon" for="pilih-provinsi">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M19 10c0 5.5-7 11-7 11S5 15.5 5 10a7 7 0 1 1 14 0Z"/><circle cx="12" cy="10" r="2.2"/></svg>
                            <span class="teks-tersembunyi">Pilih provinsi</span>
                            <select id="pilih-provinsi" name="provinsi" aria-label="Pilih provinsi">
                                <option value="">Semua provinsi</option>
                                @foreach ($daftarProvinsi as $provinsiTersedia)
                                    <option value="{{ $provinsiTersedia['id'] }}" @selected($provinsi === $provinsiTersedia['id'])>{{ $provinsiTersedia['nama'] }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label class="kolom-dengan-ikon" for="pilih-lokasi">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M19 10c0 5.5-7 11-7 11S5 15.5 5 10a7 7 0 1 1 14 0Z"/><circle cx="12" cy="10" r="2.2"/></svg>
                            <span class="teks-tersembunyi">Cari lokasi</span>
                            <select id="pilih-lokasi" name="lokasi" aria-label="Cari lokasi">
                                <option value="">{{ $provinsi !== '' ? 'Semua kota/kabupaten' : 'Pilih kota/kabupaten' }}</option>
                                @if ($provinsi !== '')
                                    @foreach ($daftarKabupaten as $kabupatenTersedia)
                                        <option value="{{ $kabupatenTersedia['nama'] }}" @selected($lokasi === $kabupatenTersedia['nama'])>{{ $kabupatenTersedia['nama'] }}</option>
                                    @endforeach
                                @else
                                    @foreach ($daftarLokasi as $lokasiTersedia)
                                        <option value="{{ $lokasiTersedia }}" @selected($lokasi === $lokasiTersedia)>{{ $lokasiTersedia }}</option>
                                    @endforeach
                                @endif
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
                    <input type="hidden" name="gaji_min" value="{{ $gajiMin }}">
                    <input type="hidden" name="urutkan" value="{{ $urutkan }}">
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
            <p class="jumlah-hasil" data-jumlah-hasil>{{ $daftarLowongan->total() }} lowongan tersedia</p>
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
                <label for="pilih-gaji">Gaji min.</label>
                <select id="pilih-gaji" name="gaji_min">
                    <option value="">Semua gaji</option>
                    @foreach ([3000000 => 'Rp 3 jt+', 5000000 => 'Rp 5 jt+', 8000000 => 'Rp 8 jt+', 10000000 => 'Rp 10 jt+'] as $nominal => $labelGaji)
                        <option value="{{ $nominal }}" @selected($gajiMin === (string) $nominal)>{{ $labelGaji }}</option>
                    @endforeach
                </select>
                <label for="pilih-urutkan">Urutkan</label>
                <select id="pilih-urutkan" name="urutkan">
                    <option value="terbaru" @selected($urutkan === 'terbaru')>Terbaru</option>
                    <option value="gaji_tertinggi" @selected($urutkan === 'gaji_tertinggi')>Gaji tertinggi</option>
                    <option value="batas_terdekat" @selected($urutkan === 'batas_terdekat')>Batas terdekat</option>
                </select>
                <input type="hidden" name="kata_kunci" value="{{ $cari }}">
                <input type="hidden" name="kategori" value="{{ $kategori }}">
                <input type="hidden" name="provinsi" value="{{ $provinsi }}">
                <input type="hidden" name="lokasi" value="{{ $lokasi }}">
                <button class="tombol" type="submit">Terapkan</button>
            </div>
        </form>
        <p class="penyaring-aktif" data-penyaring-aktif @if (! (filled($cari) || filled($kategori) || filled($provinsi) || filled($lokasi) || filled($tipe) || filled($gajiMin) || $urutkan !== 'terbaru')) hidden @endif>Filter sedang diterapkan. <a href="{{ route('home') }}">Hapus semua filter</a></p>
        <div class="daftar-lowongan" data-daftar-lowongan data-bisa-lamar="{{ auth()->check() && auth()->user()->role === 'user' ? '1' : '0' }}" data-csrf="{{ csrf_token() }}">
            @forelse ($daftarLowongan as $lowongan)
                <article class="kartu-lowongan kartu-lowongan-publik">
                    <div class="kepala-kartu-publik">
                        <div class="identitas-lowongan">
                            <span class="inisial-perusahaan" aria-hidden="true">{{ mb_strtoupper(mb_substr(str_replace('PT ', '', $lowongan->perusahaan->name), 0, 1)) }}</span>
                            <div>
                                <p class="perusahaan-lowongan">{{ $lowongan->perusahaan->name }}</p>
                                <h3><a class="tautan-lowongan" href="{{ route('lowongan.show', $lowongan) }}">{{ $lowongan->judul }}</a></h3>
                                <p class="lokasi-lowongan"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>{{ $lowongan->lokasi }}</p>
                            </div>
                        </div>
                        @auth
                            @if (auth()->user()->role === 'user')
                                @php($tersimpan = in_array($lowongan->id, $lowonganTersimpanIds ?? [], true))
                                <form class="aksi-simpan-kartu" method="POST" action="{{ route('lowongan.simpan', $lowongan) }}">
                                    @csrf
                                    <button class="tombol-ikon-simpan {{ $tersimpan ? 'tersimpan' : '' }}" type="submit" aria-label="{{ $tersimpan ? 'Hapus dari favorit' : 'Simpan ke favorit' }}" title="{{ $tersimpan ? 'Tersimpan' : 'Simpan lowongan' }}">
                                        <svg viewBox="0 0 24 24" fill="{{ $tersimpan ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 21s-7-4.4-9.3-8.6C1 9.2 2.7 5.5 6.4 5.1c2-.2 3.8.8 4.8 2.3h1.6c1-1.5 2.8-2.5 4.8-2.3 3.7.4 5.4 4.1 3.7 7.3C19 16.6 12 21 12 21Z"/></svg>
                                    </button>
                                </form>
                            @endif
                        @else
                            <a class="tombol-ikon-simpan aksi-simpan-kartu" href="{{ route('login') }}" aria-label="Masuk untuk menyimpan lowongan" title="Masuk untuk menyimpan lowongan">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 21s-7-4.4-9.3-8.6C1 9.2 2.7 5.5 6.4 5.1c2-.2 3.8.8 4.8 2.3h1.6c1-1.5 2.8-2.5 4.8-2.3 3.7.4 5.4 4.1 3.7 7.3C19 16.6 12 21 12 21Z"/></svg>
                            </a>
                        @endauth
                    </div>
                    <ul class="rincian-lowongan" aria-label="Informasi posisi">
                        <li>{{ $lowongan->tipe_kerja }}</li>
                        <li>{{ $lowongan->kategori->nama }}</li>
                        @if ($lowongan->adalahEksternal())
                            <li>Eksternal · Remotive</li>
                        @endif
                    </ul>
                    <div class="bagian-bawah-lowongan">
                        <p class="gaji-lowongan"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M7 7V4h10v3M3 12h18M10 12v3h4v-3"/></svg>
                            @if ($lowongan->gaji_min && $lowongan->gaji_max)
                                Rp {{ number_format($lowongan->gaji_min, 0, ',', '.') }}–{{ number_format($lowongan->gaji_max, 0, ',', '.') }}<span class="periode-gaji">/ bulan</span>
                            @else
                                Gaji sesuai ketentuan
                            @endif
                        </p>
                        <span class="kode-lowongan">{{ $lowongan->created_at->locale('id')->diffForHumans() }}</span>
                    </div>
                </article>
            @empty
                <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center">
                    <h3 class="text-lg font-bold text-slate-900">Belum ada lowongan yang sesuai dengan pencarianmu.</h3>
                    <p class="mt-1 text-sm text-slate-600">Coba ubah kata kunci atau filter untuk melihat lowongan lainnya.</p>
                    <a class="tombol tombol-sekunder mt-4" href="{{ auth()->check() ? route('katalog') : route('home') }}">Lihat semua lowongan</a>
                </div>
            @endforelse
        </div>
        <div class="paginasi-katalog">{{ $daftarLowongan->links() }}</div>
    </section>
</x-layout>