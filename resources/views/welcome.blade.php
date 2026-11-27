<x-layout judul="Lokerku | Lowongan kerja" :beranda="true">
    <!-- Full-Width Blue Hero Banner (Matches LokerID Reference) -->
    <section class="mt-3 bg-[#0888E8] text-white" aria-labelledby="judul-sorotan">
        <div class="mx-auto grid w-[min(76rem,calc(100%-2rem))] items-center gap-8 py-10 lg:grid-cols-[minmax(0,1.15fr)_minmax(0,0.85fr)] lg:gap-10 lg:py-12">
            <!-- Left: Headline + White Search Card -->
            <div>
                <h1 id="judul-sorotan" class="text-3xl font-bold leading-tight tracking-tight text-white sm:text-4xl lg:text-[2.75rem]">
                    #CariKerja makin mudah<br>
                    Pakai <strong class="font-extrabold">lokerku</strong>
                </h1>

                <form class="mt-6 rounded-xl bg-white p-4 text-slate-900 shadow-lg sm:p-5" action="{{ route('home') }}" method="GET" role="search" aria-label="Cari lowongan" autocomplete="off" data-pencarian-langsung data-api-lowongan="{{ route('api.v1.lowongan.index') }}" data-api-kabupaten="{{ route('api.v1.wilayah.kabupaten') }}" data-home-url="{{ route('home') }}">
                    <!-- Keyword Row -->
                    <div class="flex items-center gap-2.5">
                        <div class="flex-1">
                            <label class="teks-tersembunyi" for="kata-kunci">Kata kunci lowongan</label>
                            <input id="kata-kunci" name="kata_kunci" value="{{ $cari }}" class="h-11 w-full rounded-lg border border-slate-300 bg-white px-3.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#0888E8] focus:outline-none" placeholder="Masukkan kata kunci" autocomplete="off">
                        </div>
                        <button class="grid size-11 shrink-0 place-items-center rounded-lg bg-[#1D4ED8] text-white transition hover:bg-[#1E40AF]" type="submit" aria-label="Cari lowongan">
                            <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.8"/><path d="m16 16 4 4"/></svg>
                        </button>
                    </div>

                    <!-- Location & Category Filters Row -->
                    <div class="mt-3 grid grid-cols-1 gap-2.5 sm:grid-cols-3">
                        <label class="flex h-11 items-center gap-2 rounded-lg border border-slate-300 bg-white px-3 text-xs text-slate-700 focus-within:border-[#0888E8]" for="pilih-provinsi">
                            <svg class="size-4 shrink-0 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M19 10c0 5.5-7 11-7 11S5 15.5 5 10a7 7 0 1 1 14 0Z"/><circle cx="12" cy="10" r="2.2"/></svg>
                            <span class="teks-tersembunyi">Pilih provinsi</span>
                            <select id="pilih-provinsi" name="provinsi" aria-label="Pilih provinsi" class="h-full w-full !min-h-0 !border-0 !bg-transparent !p-0 text-xs font-medium text-slate-700 focus:outline-none">
                                <option value="">Semua provinsi</option>
                                @foreach ($daftarProvinsi as $provinsiTersedia)
                                    <option value="{{ $provinsiTersedia['id'] }}" @selected($provinsi === $provinsiTersedia['id'])>{{ $provinsiTersedia['nama'] }}</option>
                                @endforeach
                            </select>
                        </label>

                        <label class="flex h-11 items-center gap-2 rounded-lg border border-slate-300 bg-white px-3 text-xs text-slate-700 focus-within:border-[#0888E8]" for="pilih-lokasi">
                            <svg class="size-4 shrink-0 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M19 10c0 5.5-7 11-7 11S5 15.5 5 10a7 7 0 1 1 14 0Z"/><circle cx="12" cy="10" r="2.2"/></svg>
                            <span class="teks-tersembunyi">Cari lokasi</span>
                            <select id="pilih-lokasi" name="lokasi" aria-label="Cari lokasi" class="h-full w-full !min-h-0 !border-0 !bg-transparent !p-0 text-xs font-medium text-slate-700 focus:outline-none">
                                <option value="">{{ $provinsi !== '' ? 'Semua kota/kabupaten' : 'Cari lokasi' }}</option>
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

                        <label class="flex h-11 items-center gap-2 rounded-lg border border-slate-300 bg-white px-3 text-xs text-slate-700 focus-within:border-[#0888E8]" for="pilih-kategori">
                            <svg class="size-4 shrink-0 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="4" y="7" width="16" height="13" rx="2"/><path d="M9 7V5h6v2M4 12h16M10 15h4"/></svg>
                            <span class="teks-tersembunyi">Cari kategori</span>
                            <select id="pilih-kategori" name="kategori" aria-label="Cari kategori" class="h-full w-full !min-h-0 !border-0 !bg-transparent !p-0 text-xs font-medium text-slate-700 focus:outline-none">
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

                    <!-- Popular Keywords -->
                    <p class="mt-4 flex flex-wrap items-center gap-x-2.5 gap-y-1 text-xs leading-relaxed text-slate-700">
                        <strong class="font-bold text-slate-900">Paling sering dicari:</strong>
                        @foreach (['Staf Admin', 'Staf Accounting', 'Pemasaran Digital', 'E-Commerce & Marketplace', 'Administrasi Penjualan', 'Driver', 'Konsultan Penjualan', 'Content Marketing', 'Staf HRD'] as $saran)
                            <a class="text-slate-700 underline decoration-slate-400 underline-offset-2 transition hover:text-[#0888E8]" href="{{ route('home', ['kata_kunci' => $saran]) }}">{{ $saran }}</a>
                        @endforeach
                    </p>
                </form>
            </div>

            <!-- Right: Visual -->
            <div class="relative mx-auto w-full max-w-md lg:max-w-none">
                <div class="overflow-hidden rounded-2xl bg-white/10 p-1.5 shadow-xl">
                    <img class="aspect-[4/3] w-full rounded-xl object-cover" src="{{ asset('images/career-team.jpg') }}" alt="Tim berdiskusi dan bekerja sama di sekitar meja" width="1000" height="667" fetchpriority="high">
                </div>
            </div>
        </div>
    </section>

    <!-- Job Catalog Section (Clean White LokerID Style) -->
    <section class="bg-white py-10 sm:py-12" id="lowongan" aria-labelledby="judul-katalog">
        <div class="mx-auto w-[min(76rem,calc(100%-2rem))]">
            <header class="flex flex-wrap items-center justify-between gap-3">
                <h2 id="judul-katalog" class="text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">Lowongan Kerja Terbaru</h2>
                <p class="text-sm font-medium text-slate-500" data-jumlah-hasil>{{ $daftarLowongan->total() }} lowongan tersedia</p>
            </header>

            <!-- Category Tabs (Rectangular-Rounded like Reference) -->
            <nav class="mt-5 flex flex-wrap items-center gap-2" aria-label="Kategori lowongan">
                <a @class([
                    'rounded-lg border px-3.5 py-2 text-xs font-semibold transition',
                    'border-[#0888E8] bg-[#EBF5FF] text-[#0888E8]' => $kategori === '',
                    'border-slate-200 bg-white text-slate-700 hover:border-slate-300 hover:bg-slate-50' => $kategori !== '',
                ]) href="{{ route('home', array_filter(['kata_kunci' => $cari, 'lokasi' => $lokasi, 'tipe' => $tipe])) }}">Semua</a>
                @foreach ($daftarKategori->take(5) as $kategoriTersedia)
                    <a @class([
                        'rounded-lg border px-3.5 py-2 text-xs font-semibold transition',
                        'border-[#0888E8] bg-[#EBF5FF] text-[#0888E8]' => $kategori === (string) $kategoriTersedia->id,
                        'border-slate-200 bg-white text-slate-700 hover:border-slate-300 hover:bg-slate-50' => $kategori !== (string) $kategoriTersedia->id,
                    ]) href="{{ route('home', array_filter(['kategori' => $kategoriTersedia->id, 'kata_kunci' => $cari, 'lokasi' => $lokasi, 'tipe' => $tipe])) }}" @if ($kategori === (string) $kategoriTersedia->id) aria-current="page" @endif>{{ $kategoriTersedia->nama }}</a>
                @endforeach
                @if ($daftarKategori->count() > 5)
                    <details class="relative">
                        <summary class="inline-flex cursor-pointer list-none items-center gap-1 rounded-lg border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 transition hover:border-slate-300 hover:bg-slate-50">
                            Lainnya
                            <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
                        </summary>
                        <div class="absolute left-0 z-20 mt-1.5 w-60 rounded-lg border border-slate-200 bg-white p-1.5 shadow-lg">
                            @foreach ($daftarKategori->skip(5) as $kategoriTersedia)
                                <a class="block rounded-md px-3 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 hover:text-[#0888E8]" href="{{ route('home', array_filter(['kategori' => $kategoriTersedia->id, 'kata_kunci' => $cari, 'lokasi' => $lokasi, 'tipe' => $tipe])) }}">{{ $kategoriTersedia->nama }}</a>
                            @endforeach
                        </div>
                    </details>
                @endif
            </nav>

            <p class="mt-3 text-xs font-medium text-slate-600" data-penyaring-aktif @if (! (filled($cari) || filled($kategori) || filled($provinsi) || filled($lokasi) || filled($tipe) || filled($gajiMin) || $urutkan !== 'terbaru')) hidden @endif>
                Filter sedang diterapkan. <a class="font-semibold text-[#0888E8] hover:underline" href="{{ route('home') }}">Hapus semua filter</a>
            </p>

            <!-- 3-Column Job Grid (Clean White Cards like Reference) -->
            <div class="mt-6 grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3" data-daftar-lowongan data-bisa-lamar="{{ auth()->check() && auth()->user()->role === 'user' ? '1' : '0' }}" data-csrf="{{ csrf_token() }}">
                @forelse ($daftarLowongan as $lowongan)
                    <article class="flex flex-col justify-between rounded-xl border border-slate-200 bg-white p-5 transition hover:border-[#0888E8] hover:shadow-md">
                        <div>
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex min-w-0 items-start gap-3">
                                    <span class="grid size-11 shrink-0 place-items-center rounded-full border border-slate-200 bg-slate-50 text-sm font-extrabold text-[#0888E8]" aria-hidden="true">{{ mb_strtoupper(mb_substr(str_replace('PT ', '', $lowongan->perusahaan->name), 0, 1)) }}</span>
                                    <div class="min-w-0">
                                        <p class="truncate text-xs font-medium text-[#0888E8]">{{ $lowongan->perusahaan->name }}</p>
                                        <h3 class="mt-0.5 text-base font-bold leading-snug text-slate-900">
                                            <a class="text-slate-900 hover:text-[#0888E8]" href="{{ route('lowongan.show', $lowongan) }}">{{ $lowongan->judul }}</a>
                                        </h3>
                                        <p class="mt-1 flex items-center gap-1 truncate text-xs text-slate-500">
                                            <svg class="size-3.5 shrink-0 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                                            <span class="truncate">{{ $lowongan->lokasi }}</span>
                                        </p>
                                    </div>
                                </div>
                                @auth
                                    @if (auth()->user()->role === 'user')
                                        @php($tersimpan = in_array($lowongan->id, $lowonganTersimpanIds ?? [], true))
                                        <form method="POST" action="{{ route('lowongan.simpan', $lowongan) }}">
                                            @csrf
                                            <button class="grid size-8 shrink-0 place-items-center rounded-full border border-slate-200 bg-white text-slate-400 transition hover:border-[#0888E8] hover:text-[#0888E8] {{ $tersimpan ? '!border-[#0888E8] !bg-[#EBF5FF] !text-[#0888E8]' : '' }}" type="submit" aria-label="{{ $tersimpan ? 'Hapus dari favorit' : 'Simpan ke favorit' }}" title="{{ $tersimpan ? 'Tersimpan' : 'Simpan lowongan' }}">
                                                <svg class="size-4" viewBox="0 0 24 24" fill="{{ $tersimpan ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 21s-7-4.4-9.3-8.6C1 9.2 2.7 5.5 6.4 5.1c2-.2 3.8.8 4.8 2.3h1.6c1-1.5 2.8-2.5 4.8-2.3 3.7.4 5.4 4.1 3.7 7.3C19 16.6 12 21 12 21Z"/></svg>
                                            </button>
                                        </form>
                                    @endif
                                @else
                                    <a class="grid size-8 shrink-0 place-items-center rounded-full border border-slate-200 bg-white text-slate-400 transition hover:border-[#0888E8] hover:text-[#0888E8]" href="{{ route('login') }}" aria-label="Masuk untuk menyimpan lowongan" title="Masuk untuk menyimpan lowongan">
                                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 21s-7-4.4-9.3-8.6C1 9.2 2.7 5.5 6.4 5.1c2-.2 3.8.8 4.8 2.3h1.6c1-1.5 2.8-2.5 4.8-2.3 3.7.4 5.4 4.1 3.7 7.3C19 16.6 12 21 12 21Z"/></svg>
                                    </a>
                                @endauth
                            </div>

                            <div class="mt-3.5 flex flex-wrap gap-1.5">
                                @if ($lowongan->sedangDiiklankan())
                                    <span class="rounded-md bg-[#007AFF] px-2.5 py-1 text-xs font-bold text-white">Dipromosikan</span>
                                @endif
                                <span class="rounded-md bg-[#E6F2FE] px-2.5 py-1 text-xs font-medium text-slate-700">{{ $lowongan->tipe_kerja }}</span>
                                <span class="rounded-md bg-[#E6F2FE] px-2.5 py-1 text-xs font-medium text-slate-700">{{ $lowongan->kategori->nama }}</span>
                                @if ($lowongan->adalahEksternal())
                                    <span class="rounded-md bg-[#E6F2FE] px-2.5 py-1 text-xs font-medium text-slate-700">Remotive</span>
                                @endif
                            </div>
                        </div>

                        <div class="mt-4 flex items-center justify-between gap-2 border-t border-slate-100 pt-3.5 text-xs">
                            <p class="flex items-center gap-1.5 truncate font-semibold text-slate-700">
                                <svg class="size-4 shrink-0 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M7 7V4h10v3M3 12h18M10 12v3h4v-3"/></svg>
                                @if ($lowongan->gaji_min && $lowongan->gaji_max)
                                    Rp {{ number_format($lowongan->gaji_min, 0, ',', '.') }}–{{ number_format($lowongan->gaji_max, 0, ',', '.') }}
                                @else
                                    Gaji sesuai ketentuan
                                @endif
                            </p>
                            <span class="shrink-0 text-slate-400">{{ $lowongan->created_at->locale('id')->diffForHumans() }}</span>
                        </div>
                    </article>
                @empty
                    <div class="col-span-full rounded-xl border border-dashed border-slate-300 bg-slate-50 p-10 text-center">
                        <h3 class="text-base font-bold text-slate-900">Belum ada lowongan yang sesuai dengan pencarianmu.</h3>
                        <p class="mt-1 text-sm text-slate-600">Coba ubah kata kunci atau filter untuk melihat lowongan lainnya.</p>
                        <a class="mt-4 inline-flex items-center justify-center rounded-lg bg-[#0888E8] px-5 py-2 text-xs font-semibold !text-white transition hover:bg-[#0670C2]" href="{{ auth()->check() ? route('katalog') : route('home') }}">Lihat semua lowongan</a>
                    </div>
                @endforelse
            </div>

            <div class="mt-8">{{ $daftarLowongan->links() }}</div>
        </div>
    </section>
</x-layout>
