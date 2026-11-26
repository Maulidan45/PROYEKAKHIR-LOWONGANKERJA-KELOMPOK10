@php
    $metaRingkasan = [
        'Akun terdaftar' => ['ikon' => 'M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75', 'warna' => 'biru', 'catatan' => 'Pengguna dari seluruh peran'],
        'Lowongan aktif' => ['ikon' => 'M4 7h16v12H4zM8 7V5h8v2M4 11h16M10 15h4', 'warna' => 'emerald', 'catatan' => 'Tayang di katalog publik'],
        'Kategori' => ['ikon' => 'M4.5 4.5h6v6h-6zM13.5 4.5h6v6h-6zM4.5 13.5h6v6h-6zM13.5 13.5h6v6h-6z', 'warna' => 'violet', 'catatan' => 'Pengelompokan lowongan'],
        'Lamaran' => ['ikon' => 'M6 3h9l3 3v15H6zM9 12h6M9 16h6', 'warna' => 'amber', 'catatan' => 'Lamaran sepanjang waktu'],
    ];
    $warnaAvatar = ['admin' => 'avatar-violet', 'perusahaan' => 'avatar-biru'];
@endphp

<x-panel-admin judul="Ringkasan" label="Pusat kendali" aktif="dashboard" deskripsi="Pantau statistik akun, lowongan aktif, dan kategori dari satu tempat.">
    <x-slot:aksi>
        <form method="POST" action="{{ route('admin.lowongan.sinkron') }}" data-kirim>
            @csrf
            <button class="tombol-utama inline-flex items-center gap-2 whitespace-nowrap" type="submit" data-teks-proses="Menarik data…">
                <svg class="size-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 11a8 8 0 1 0-2.3 5.7M20 5v6h-6"/></svg>
                Tarik data API
            </button>
        </form>
    </x-slot:aksi>

    @if ($errors->any())
        <div class="peringatan-admin" role="alert">{{ $errors->first() }}</div>
    @endif

    <dl class="kisi-statistik">
        @foreach ($ringkasan as $label => $nilai)
            @php($meta = $metaRingkasan[$label] ?? ['ikon' => 'M12 7v5l3 2', 'warna' => 'biru', 'catatan' => ''])
            <div class="kartu-statistik">
                <span class="kartu-statistik-ikon ikon-{{ $meta['warna'] }}" aria-hidden="true">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $meta['ikon'] }}"/></svg>
                </span>
                <dt class="kartu-statistik-label">{{ $label }}</dt>
                <dd class="kartu-statistik-nilai">{{ number_format($nilai, 0, ',', '.') }}</dd>
                <span class="kartu-statistik-catatan">{{ $meta['catatan'] }}</span>
            </div>
        @endforeach
    </dl>

    <div class="kisi-panel-admin">
        <div class="tumpuk-panel">

            <section class="kartu-admin" id="daftar-akun" aria-labelledby="judul-akun-terdaftar">
                <header class="kartu-admin-kepala">
                    <div class="min-w-0">
                        <h2 class="kartu-admin-judul" id="judul-akun-terdaftar">Akun terdaftar</h2>
                        <p class="kartu-admin-deskripsi">Distribusi pengguna berdasarkan peran akun.</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($jenisAkunTersedia as $kunci => $label)
                            <span class="lencana-peran lencana-{{ $kunci }}">
                                {{ $label }}
                                <strong>{{ $jumlahAkun[$kunci] ?? 0 }}</strong>
                            </span>
                        @endforeach
                    </div>
                </header>
                <ul class="daftar-admin">
                    @forelse ($daftarAkun as $akun)
                        <li class="baris-admin">
                            <span @class(['avatar-admin', 'avatar-kecil', $warnaAvatar[$akun->role] ?? 'avatar-slate'])" aria-hidden="true">{{ mb_strtoupper(mb_substr($akun->name, 0, 1)) }}</span>
                            <div class="min-w-0 flex-1">
                                <strong class="baris-admin-judul truncate">{{ $akun->name }}</strong>
                                <p class="baris-admin-meta truncate">{{ $akun->email }}</p>
                            </div>
                            <span class="lencana-peran lencana-{{ $akun->role }}">{{ $jenisAkunTersedia[$akun->role] }}</span>
                        </li>
                    @empty
                        <li class="kosong-admin">Belum ada akun terdaftar.</li>
                    @endforelse
                </ul>
                <div class="kartu-admin-kaki">{{ $daftarAkun->links() }}</div>
            </section>

            <section class="kartu-admin" aria-labelledby="judul-lowongan-admin">

            <section class="kartu-admin" aria-labelledby="judul-lowongan-admin">
                <header class="kartu-admin-kepala">
                    <div class="min-w-0">
                        <h2 class="kartu-admin-judul" id="judul-lowongan-admin">Lowongan aktif terbaru</h2>
                        <p class="kartu-admin-deskripsi">Posisi terbaru yang tayang di katalog.</p>
                    </div>
                    <a class="tautan-admin" href="{{ route('lowongan.index') }}">Kelola semua</a>
                </header>
                <ul class="daftar-admin">
                    @forelse ($lowonganTerbaruAdmin as $itemLowongan)
                        <li class="baris-admin">
                            <span class="avatar-admin avatar-kecil avatar-slate" aria-hidden="true">{{ mb_strtoupper(mb_substr(str_replace('PT ', '', $itemLowongan->perusahaan->name), 0, 1)) }}</span>
                            <div class="min-w-0 flex-1">
                                <a class="baris-admin-judul block truncate" href="{{ route('lowongan.show', $itemLowongan) }}">{{ $itemLowongan->judul }}</a>
                                <p class="baris-admin-meta truncate">{{ $itemLowongan->perusahaan->name }} &middot; {{ $itemLowongan->lokasi }}</p>
                            </div>
                            <span class="lencana-netral">{{ $itemLowongan->kategori->nama }}</span>
                        </li>
                    @empty
                        <li class="kosong-admin">Belum ada lowongan aktif.</li>
                    @endforelse
                </ul>
            </section>
        </div>
    </div>
</x-panel-admin>
