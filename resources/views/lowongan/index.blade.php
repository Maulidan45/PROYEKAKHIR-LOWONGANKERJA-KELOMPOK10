@if (auth()->user()->role === 'admin')
<x-panel-admin judul="Kelola Lowongan" label="Moderasi Lowongan" aktif="lowongan" deskripsi="Tinjau dan moderasi lowongan dari seluruh perusahaan serta sumber eksternal.">
    <form class="mb-5 flex flex-col gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-xs sm:flex-row sm:items-center" method="GET">
        <div class="flex-1">
            <label class="sr-only" for="cari-lowongan-admin">Cari judul, lokasi, atau perusahaan</label>
            <input id="cari-lowongan-admin" type="search" name="kata_kunci" value="{{ request()->query('kata_kunci') }}" placeholder="Cari judul, lokasi, atau perusahaan…">
        </div>
        <div class="sm:w-44">
            <label class="sr-only" for="status-lowongan-admin">Filter status</label>
            <select id="status-lowongan-admin" name="status">
                <option value="">Semua Status</option>
                @foreach (['aktif' => 'Aktif', 'kedaluwarsa' => 'Kedaluwarsa', 'ditutup' => 'Ditutup'] as $nilai => $label)
                    <option value="{{ $nilai }}" @selected(request()->query('status') === $nilai)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <button class="tombol-utama shrink-0" type="submit">Cari</button>
    </form>

    <div class="grid gap-3">
        @forelse ($daftarLowongan as $lowongan)
            @php($statusLowongan = $lowongan->statusLowongan())
            <article class="flex flex-col justify-between gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-xs sm:flex-row sm:items-center">
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <span @class([
                            'rounded-full px-2.5 py-0.5 text-xs font-semibold',
                            'bg-emerald-50 text-emerald-700' => $statusLowongan === 'Aktif',
                            'bg-amber-50 text-amber-800' => $statusLowongan === 'Kedaluwarsa',
                            'bg-slate-100 text-slate-600' => $statusLowongan === 'Ditutup',
                        ])>{{ $statusLowongan }}</span>
                        <span class="rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-medium text-blue-700">{{ $lowongan->kategori->nama }}</span>
                        @if ($lowongan->adalahEksternal())
                            <span class="rounded-full bg-indigo-50 px-2.5 py-0.5 text-xs font-semibold text-indigo-700">Eksternal · Remotive</span>
                        @endif
                    </div>
                    <h2 class="mt-2 truncate text-base font-bold text-slate-900">{{ $lowongan->judul }}</h2>
                    <p class="mt-0.5 truncate text-sm text-slate-600">{{ $lowongan->perusahaan->name }} &middot; {{ $lowongan->lokasi }} &middot; {{ $lowongan->tipe_kerja }}</p>
                    <p class="mt-1 text-xs text-slate-500">Dipasang {{ $lowongan->created_at->format('d M Y') }} &middot; {{ $lowongan->lamarans_count }} lamaran</p>
                </div>
                <div class="flex shrink-0 flex-wrap items-center gap-2">
                    <a class="tombol-kedua" href="{{ route('lowongan.show', $lowongan) }}">Pratinjau</a>
                    <a class="tombol-kedua" href="{{ route('lowongan.edit', $lowongan) }}">Ubah</a>
                    <form method="POST" action="{{ route('lowongan.destroy', $lowongan) }}">
                        @csrf
                        @method('DELETE')
                        <button class="tombol-bahaya rounded-lg px-3 py-2 text-sm font-semibold" type="submit">Hapus</button>
                    </form>
                </div>
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center text-sm text-slate-500">Belum ada lowongan ditemukan.</div>
        @endforelse
    </div>

    <div class="mt-6">{{ $daftarLowongan->links() }}</div>
</x-panel-admin>
@else
<x-layout :judul="'Kelola Lowongan | Lokerku'">
    <div class="mx-auto w-[min(72rem,calc(100%-2rem))] py-8 sm:w-[min(72rem,calc(100%-3rem))] sm:py-12">
        <section class="mx-auto min-w-0 max-w-5xl">
            <h1 class="teks-tersembunyi">Lowongan saya</h1>
            @if (! auth()->user()->sudahTerverifikasi())
                <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
                    <strong class="block font-semibold">Akun perusahaan berstatus: {{ auth()->user()->status_verifikasi }}</strong>
                    <p class="mt-1 text-amber-800">Hanya perusahaan terverifikasi yang dapat memasang lowongan baru. Silakan lengkapi dokumen legalitas di <a class="font-semibold underline" href="{{ route('akun.edit') }}">Profil Perusahaan</a>.</p>
                </div>
            @endif

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
                                    <span class="truncate">{{ $lowongan->lokasi }} · {{ $lowongan->batas_lamaran ? 'Batas lamaran ' . $lowongan->batas_lamaran->format('d M Y') : 'Tanpa batas waktu' }}</span>
                                </p>
                            </div>
                            @php($status = $lowongan->statusLowongan())
                            <span @class(['inline-flex min-h-7 shrink-0 items-center rounded px-2.5 text-xs font-semibold', 'bg-emerald-50 text-emerald-800' => $status === 'Aktif', 'bg-amber-50 text-amber-800' => $status === 'Kedaluwarsa', 'bg-slate-100 text-slate-600' => $status === 'Ditutup'])>{{ $status }}</span>
                        </div>
                        <p class="mt-3 line-clamp-2 text-sm leading-6 text-slate-600">{{ $lowongan->deskripsi }}</p>
                        <div class="mt-4 flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 pt-4">
                            <div class="flex flex-wrap items-center gap-x-5 gap-y-1 text-sm">
                                <p class="font-semibold text-slate-900">
                                    @if ($lowongan->gaji_min && $lowongan->gaji_max)
                                        @if ($lowongan->gaji_min === $lowongan->gaji_max)
                                            Rp {{ number_format($lowongan->gaji_min, 0, ',', '.') }}
                                        @else
                                            Rp {{ number_format($lowongan->gaji_min, 0, ',', '.') }}–{{ number_format($lowongan->gaji_max, 0, ',', '.') }}
                                        @endif
                                        <span class="font-normal text-slate-500">/ bulan</span>
                                    @else
                                        <span class="font-normal text-slate-500">Gaji sesuai ketentuan</span>
                                    @endif
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
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M4 7h16v13H4zM8 7V5h8v2M4 11h16M10 15h4"/></svg>
                        </span>
                        <h2>{{ request()->filled('kata_kunci') || request()->filled('status') ? 'Lowongan tidak ditemukan' : 'Belum ada lowongan' }}</h2>
                        <p>{{ request()->filled('kata_kunci') || request()->filled('status') ? 'Tidak ada lowongan yang cocok. Coba kata kunci atau status lain.' : 'Lowongan yang Anda pasang akan muncul di sini bersama jumlah pelamarnya.' }}</p>
                        @can('create', App\Models\Lowongan::class)
                            <a class="tombol-utama" href="{{ route('lowongan.create') }}" data-buka-modal="modal-lowongan">Pasang lowongan pertama</a>
                        @endcan
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

            <div class="mt-6">{{ $daftarLowongan->links() }}</div>
        </section>
    </div>
</x-layout>
@endif
