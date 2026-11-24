<x-layout :judul="'Lowongan | Lokerku'">
    @php($halamanPerusahaan = auth()->user()->role === 'perusahaan')
    <div @class(['mx-auto w-[min(72rem,calc(100%-2rem))] py-8 sm:w-[min(72rem,calc(100%-3rem))] sm:py-12' => $halamanPerusahaan, 'pembungkus halaman-aplikasi' => ! $halamanPerusahaan])>
        @unless ($halamanPerusahaan)
            <x-menu-peran :jenis-akun="auth()->user()->role" />
        @endunless
        <section @class(['mx-auto min-w-0 max-w-5xl' => $halamanPerusahaan, 'isi-aplikasi' => ! $halamanPerusahaan])>
            @if ($halamanPerusahaan)
                <h1 class="teks-tersembunyi">Lowongan saya</h1>
                @can('create', App\Models\Lowongan::class)
                    <div class="mb-5 flex justify-end">
                        <a class="tombol-utama" href="{{ route('lowongan.create') }}" data-buka-modal="modal-lowongan">
                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                            Pasang lowongan
                        </a>
                    </div>
                @endcan
                <div class="mb-6 grid grid-cols-2 gap-3 lg:grid-cols-4" aria-label="Ringkasan rekrutmen">
                    <a class="rounded-lg border border-slate-200 bg-white p-4 transition hover:border-blue-300 hover:bg-blue-50/50 sm:p-5" href="{{ route('lowongan.index') }}">
                        <span class="block text-sm font-medium text-slate-600">Total lowongan</span>
                        <span class="mt-2 block text-2xl font-bold tracking-tight text-slate-900 tabular-nums sm:text-3xl">{{ $statistik['total'] }}</span>
                    </a>
                    <a class="rounded-lg border border-slate-200 bg-white p-4 transition hover:border-blue-300 hover:bg-blue-50/50 sm:p-5" href="{{ route('lowongan.index', ['status' => 'aktif']) }}">
                        <span class="block text-sm font-medium text-slate-600">Aktif</span>
                        <span class="mt-2 block text-2xl font-bold tracking-tight text-emerald-700 tabular-nums sm:text-3xl">{{ $statistik['aktif'] }}</span>
                    </a>
                    <a class="rounded-lg border border-slate-200 bg-white p-4 transition hover:border-blue-300 hover:bg-blue-50/50 sm:p-5" href="{{ route('lowongan.index', ['status' => 'kedaluwarsa']) }}">
                        <span class="block text-sm font-medium text-slate-600">Kedaluwarsa</span>
                        <span class="mt-2 block text-2xl font-bold tracking-tight text-amber-700 tabular-nums sm:text-3xl">{{ $statistik['kedaluwarsa'] }}</span>
                    </a>
                    <a class="rounded-lg border border-slate-200 bg-white p-4 transition hover:border-blue-300 hover:bg-blue-50/50 sm:p-5" href="{{ route('perusahaan.pelamar.index') }}">
                        <span class="block text-sm font-medium text-slate-600">Lamaran masuk</span>
                        <span class="mt-2 block text-2xl font-bold tracking-tight text-slate-900 tabular-nums sm:text-3xl">{{ $statistik['lamaran'] }}</span>
                    </a>
                </div>
                <form class="toolbar-lowongan" method="GET" role="search" aria-label="Cari dan saring lowongan" autocomplete="off">
                    <label class="teks-tersembunyi" for="cari-lowongan">Cari posisi atau lokasi</label>
                    <div class="kolom-cari-lowongan">
                        <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                        <input id="cari-lowongan" type="search" name="kata_kunci" value="{{ request()->query('kata_kunci') }}" placeholder="Ketik posisi atau lokasi…" autocomplete="off">
                    </div>
                    <label class="teks-tersembunyi" for="status-lowongan">Saring menurut status</label>
                    <select id="status-lowongan" name="status" aria-label="Saring menurut status">
                        <option value="">Semua status</option>
                        @foreach (['aktif' => 'Aktif', 'kedaluwarsa' => 'Kedaluwarsa', 'ditutup' => 'Ditutup'] as $nilai => $label)
                            <option value="{{ $nilai }}" @selected(request()->query('status') === $nilai)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <button class="tombol-utama tombol-cari-lowongan" type="submit">Cari lowongan</button>
                </form>
                <section class="grid gap-3" aria-label="Lowongan yang saya pasang">
                    @forelse ($daftarLowongan as $lowongan)
                        <article class="rounded-lg border border-slate-200 bg-white p-5 transition hover:border-blue-300 hover:shadow-sm sm:p-6">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="inline-flex min-h-6 items-center rounded-full bg-blue-50 px-2.5 text-xs font-semibold text-blue-800">{{ $lowongan->kategori->nama }}</span>
                                        <span class="inline-flex min-h-6 items-center rounded-full bg-slate-100 px-2.5 text-xs font-medium text-slate-600">{{ $lowongan->tipe_kerja }}</span>
                                    </div>
                                    <h2 class="mt-2 truncate text-base font-semibold text-slate-900">{{ $lowongan->judul }}</h2>
                                    <p class="mt-1 flex items-center gap-1.5 text-sm text-slate-600">
                                        <svg class="size-4 shrink-0 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 21s7-5.1 7-11a7 7 0 1 0-14 0c0 5.9 7 11 7 11Z"/><circle cx="12" cy="10" r="2.5"/></svg>
                                        <span class="truncate">{{ $lowongan->lokasi }} · Batas lamaran {{ $lowongan->batas_lamaran->format('d M Y') }}</span>
                                    </p>
                                </div>
                                @php($status = $lowongan->statusLowongan())
                                <span @class(['inline-flex min-h-7 shrink-0 items-center rounded px-2.5 text-xs font-semibold', 'bg-emerald-50 text-emerald-800' => $status === 'Aktif', 'bg-amber-50 text-amber-800' => $status === 'Kedaluwarsa', 'bg-slate-100 text-slate-600' => $status === 'Ditutup'])>{{ $status }}</span>
                            </div>
                            <p class="mt-3 line-clamp-2 text-sm leading-6 text-slate-600">{{ $lowongan->deskripsi }}</p>
                            <div class="mt-4 flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 pt-4">
                                <div class="flex flex-wrap items-center gap-x-5 gap-y-1 text-sm">
                                    <p class="font-semibold text-slate-900">
                                        @if ($lowongan->gaji_min === $lowongan->gaji_max)
                            Rp {{ number_format($lowongan->gaji_min, 0, ',', '.') }}
                                        @else
                            Rp {{ number_format($lowongan->gaji_min, 0, ',', '.') }}–{{ number_format($lowongan->gaji_max, 0, ',', '.') }}
                                        @endif
                                        <span class="font-normal text-slate-500">/ bulan</span>
                                    </p>
                                    <p class="text-slate-600"><strong class="font-semibold text-slate-900 tabular-nums">{{ $lowongan->lamarans_count }}</strong> lamaran</p>
                                </div>
                                <div class="flex flex-wrap items-center gap-2">
                                    @can('update', $lowongan)
                                        <a class="tombol-kedua" href="{{ route('lowongan.edit', $lowongan) }}">Ubah</a>
                                        <a class="tombol-kedua" href="{{ route('lowongan.pelamar', $lowongan) }}">Lihat pelamar</a>
                                        <form method="POST" action="{{ route('lowongan.destroy', $lowongan) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button class="tombol-bahaya inline-flex min-h-11 items-center rounded-md px-3" type="submit">Hapus</button>
                                        </form>
                                    @endcan
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="empty-lowongan">
                            <span class="empty-lowongan-ikon" aria-hidden="true">
                                @if (request()->filled('kata_kunci') || request()->filled('status'))
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5M8 11h6"/></svg>
                                @else
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M4 7h16v13H4zM8 7V5h8v2M4 11h16M10 15h4"/></svg>
                                @endif
                            </span>
                            <h2>{{ request()->filled('kata_kunci') || request()->filled('status') ? 'Lowongan tidak ditemukan' : 'Belum ada lowongan' }}</h2>
                            <p>
                                @if (request()->filled('kata_kunci') || request()->filled('status'))
                                    Tidak ada lowongan yang cocok. Coba kata kunci atau status lain.
                                @else
                                    Lowongan yang Anda pasang akan muncul di sini bersama jumlah pelamarnya.
                                @endif
                            </p>
                            @if (request()->filled('kata_kunci') || request()->filled('status'))
                                <a class="tombol-kedua" href="{{ route('lowongan.index') }}">Hapus pencarian</a>
                            @else
                                @can('create', App\Models\Lowongan::class)
                                    <a class="tombol-utama" href="{{ route('lowongan.create') }}" data-buka-modal="modal-lowongan">Pasang lowongan pertama</a>
                                @endcan
                            @endif
                        </div>
                    @endforelse
                </section>
                @can('create', App\Models\Lowongan::class)
                    @php($lowonganBaru = new App\Models\Lowongan)
                    <dialog id="modal-lowongan" class="dialog-lowongan" aria-labelledby="judul-modal-lowongan" @if ($errors->any()) data-terbuka @endif>
                        <form method="POST" action="{{ route('lowongan.store') }}">
                            @csrf
                            <header>
                                <div>
                                    <h2 class="text-xl font-bold tracking-tight text-slate-900" id="judul-modal-lowongan">Pasang lowongan</h2>
                                    <p class="mt-1 text-sm text-slate-600">Lengkapi detail posisi agar kandidat memahami kesempatan yang ditawarkan.</p>
                                </div>
                                <button type="button" class="tombol-tutup" data-tutup-modal aria-label="Tutup">
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg>
                                </button>
                            </header>
                            @include('lowongan.bagian-form', ['lowongan' => $lowonganBaru])
                            @if ($errors->any())
                                <div class="rounded-md border border-red-200 bg-red-50 p-4 text-sm text-red-900" role="alert">{{ $errors->first() }}</div>
                            @endif
                            <footer>
                                <button type="button" class="tombol-kedua" data-tutup-modal>Batal</button>
                                <button class="tombol-utama" type="submit" data-teks-proses="Menyimpan…">Simpan lowongan</button>
                            </footer>
                        </form>
                    </dialog>
                @endcan
            @else
                <header class="kepala-halaman-aplikasi">
                    <div><h1>Kelola lowongan</h1><p>Tinjau lowongan dan aktivitas pelamar dari seluruh perusahaan.</p></div>
                    @can('create', App\Models\Lowongan::class)
                        <a class="tombol" href="{{ route('lowongan.create') }}">Pasang lowongan</a>
                    @endcan
                </header>
                <section class="admin-lowongan-lista" id="pelamar" aria-label="Lowongan dan akses pelamar">
                    @forelse ($daftarLowongan as $lowongan)
                        @php($statusLowongan = $lowongan->statusLowongan())
                        <article class="admin-lowongan-kartu">
                            <div class="admin-lowongan-utama">
                                <span class="admin-lowongan-ikon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M4 7h16v12H4zM8 7V5h8v2M4 11h16M10 15h4"/></svg></span>
                                <div class="min-w-0">
                                    <h2>{{ $lowongan->judul }}</h2>
                                    <p>{{ $lowongan->perusahaan->name }} <span aria-hidden="true">·</span> {{ $lowongan->kategori->nama }}</p>
                                    <p class="admin-lowongan-meta">{{ $lowongan->lokasi }} <span aria-hidden="true">·</span> {{ $lowongan->tipe_kerja }} <span aria-hidden="true">·</span> Dipasang {{ $lowongan->created_at->format('d M Y') }}</p>
                                </div>
                            </div>
                            <div class="admin-lowongan-samping">
                                <span @class(['admin-lowongan-status', 'status-aktif' => $statusLowongan === 'Aktif', 'status-kedaluwarsa' => $statusLowongan === 'Kedaluwarsa', 'status-ditutup' => $statusLowongan === 'Ditutup'])>{{ $statusLowongan }}</span>
                                <span class="admin-lowongan-jumlah"><strong>{{ $lowongan->lamarans_count }}</strong> lamaran</span>
                            </div>
                            <div class="admin-lowongan-aksi">
                                @can('update', $lowongan)
                                    <a class="tombol-kedua" href="{{ route('lowongan.pelamar', $lowongan) }}">Lihat pelamar</a>
                                    <a class="tombol-kedua" href="{{ route('lowongan.edit', $lowongan) }}">Ubah lowongan</a>
                                    <form method="POST" action="{{ route('lowongan.destroy', $lowongan) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="tombol-bahaya" type="submit">Hapus</button>
                                    </form>
                                @endcan
                            </div>
                        </article>
                    @empty
                        <div class="empty-lowongan">
                            <span class="empty-lowongan-ikon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M4 7h16v13H4zM8 7V5h8v2M4 11h16M10 15h4"/></svg></span>
                            <h2>Belum ada lowongan</h2>
                            <p>Lowongan dari perusahaan akan muncul di sini untuk ditinjau.</p>
                        </div>
                    @endforelse
                </section>
            @endif
            <div class="mt-6">{{ $daftarLowongan->links() }}</div>
        </section>
    </div>
</x-layout>