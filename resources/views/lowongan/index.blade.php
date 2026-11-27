@if (auth()->user()->role === 'admin')
<x-panel-admin judul="Kelola Lowongan" aktif="lowongan">
    <form class="flex flex-col gap-3 rounded-3xl border-[1.5px] border-slate-200/90 bg-white/80 p-4 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.1)] backdrop-blur-xl sm:flex-row sm:items-center" method="GET">
        <div class="flex-1">
            <label class="sr-only" for="cari-lowongan-admin">Cari judul, lokasi, atau perusahaan</label>
            <input id="cari-lowongan-admin" type="search" name="kata_kunci" value="{{ request()->query('kata_kunci') }}" class="w-full rounded-full border border-slate-200/90 bg-white/95 px-5 py-2.5 text-sm text-slate-800 placeholder:text-slate-400 focus:border-[#007AFF] focus:outline-none" placeholder="Cari judul, lokasi, atau perusahaan…">
        </div>
        <div class="sm:w-44">
            <label class="sr-only" for="status-lowongan-admin">Filter status</label>
            <select id="status-lowongan-admin" name="status" class="w-full rounded-full border border-slate-200/90 bg-white/95 px-4 py-2.5 text-sm text-slate-800 focus:border-[#007AFF] focus:outline-none">
                <option value="">Semua Status</option>
                @foreach (['aktif' => 'Aktif', 'kedaluwarsa' => 'Kedaluwarsa', 'ditutup' => 'Ditutup'] as $nilai => $label)
                    <option value="{{ $nilai }}" @selected(request()->query('status') === $nilai)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <button class="inline-flex min-h-11 shrink-0 items-center justify-center rounded-full bg-[#007AFF] px-6 text-sm font-semibold text-white shadow-md shadow-[#007AFF]/20 transition hover:bg-[#0062CC]" type="submit">Cari</button>
    </form>

    <div class="grid gap-4">
        @forelse ($daftarLowongan as $lowongan)
            @php($statusLowongan = $lowongan->statusLowongan())
            <article class="flex flex-col justify-between gap-4 rounded-3xl border-[1.5px] border-slate-200/90 bg-white/80 p-5 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.1)] backdrop-blur-xl transition hover:-translate-y-0.5 hover:shadow-xl sm:flex-row sm:items-center sm:p-6">
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <span @class([
                            'rounded-full px-3 py-1 text-xs font-bold',
                            'bg-emerald-100 text-emerald-800' => $statusLowongan === 'Aktif',
                            'bg-amber-100 text-amber-800' => $statusLowongan === 'Kedaluwarsa',
                            'bg-slate-100 text-slate-600' => $statusLowongan === 'Ditutup',
                        ])>{{ $statusLowongan }}</span>
                        <span class="rounded-full bg-[#E0F2FE] px-3 py-1 text-xs font-semibold text-[#007AFF]">{{ $lowongan->kategori->nama }}</span>
                        @if ($lowongan->adalahEksternal())
                            <span class="rounded-full bg-sky-100 px-3 py-1 text-xs font-semibold text-sky-800">Eksternal · Remotive</span>
                        @endif
                    </div>
                    <h2 class="mt-2.5 truncate text-base font-bold text-slate-900">{{ $lowongan->judul }}</h2>
                    <p class="mt-0.5 truncate text-sm text-slate-600">{{ $lowongan->perusahaan->name }} &middot; {{ $lowongan->lokasi }} &middot; {{ $lowongan->tipe_kerja }}</p>
                    <p class="mt-1 text-xs text-slate-500">Dipasang {{ $lowongan->created_at->format('d M Y') }} &middot; {{ $lowongan->lamarans_count }} lamaran</p>
                </div>
                <div class="flex shrink-0 flex-wrap items-center gap-2">
                    <a class="rounded-full border border-slate-200/80 bg-white/85 px-4 py-2 text-xs font-semibold text-slate-700 transition hover:border-[#007AFF]/40 hover:bg-[#E0F2FE]/70 hover:text-[#007AFF]" href="{{ route('lowongan.show', $lowongan) }}">Pratinjau</a>
                    <a class="rounded-full border border-slate-200/80 bg-white/85 px-4 py-2 text-xs font-semibold text-slate-700 transition hover:border-[#007AFF]/40 hover:bg-[#E0F2FE]/70 hover:text-[#007AFF]" href="{{ route('lowongan.edit', $lowongan) }}">Ubah</a>
                    <form method="POST" action="{{ route('lowongan.destroy', $lowongan) }}">
                        @csrf
                        @method('DELETE')
                        <button class="rounded-full bg-rose-50 px-4 py-2 text-xs font-bold text-rose-600 transition hover:bg-rose-100" type="submit">Hapus</button>
                    </form>
                </div>
            </article>
        @empty
            <div class="rounded-3xl border border-dashed border-slate-300 bg-white/60 p-12 text-center text-sm text-slate-500 backdrop-blur-md">Belum ada lowongan ditemukan.</div>
        @endforelse
    </div>

    <div>{{ $daftarLowongan->links() }}</div>
</x-panel-admin>
@else
<x-panel-perusahaan judul="Kelola Lowongan" aktif="lowongan">
    @if (! auth()->user()->sudahTerverifikasi())
        <div class="rounded-3xl border-[1.5px] border-amber-300 bg-amber-50/90 p-5 text-sm text-amber-900 backdrop-blur-xl">
            <strong class="block font-bold">Akun perusahaan berstatus: {{ auth()->user()->status_verifikasi }}</strong>
            <p class="mt-1 text-xs text-amber-800">Hanya perusahaan terverifikasi yang dapat memasang lowongan baru. Silakan lengkapi dokumen legalitas di <a class="font-bold underline" href="{{ route('akun.edit') }}">Profil Perusahaan</a>.</p>
        </div>
    @endif

    @can('create', App\Models\Lowongan::class)
        <div class="flex justify-end">
            <a class="inline-flex min-h-10 items-center gap-2 rounded-full bg-[#007AFF] px-5 text-xs font-bold !text-white shadow-md shadow-[#007AFF]/25 transition hover:bg-[#0062CC]" href="{{ route('lowongan.create') }}" data-buka-modal="modal-lowongan">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                Pasang Lowongan
            </a>
        </div>
    @endcan

    <div class="grid grid-cols-2 gap-3 lg:grid-cols-4 sm:gap-4" aria-label="Ringkasan rekrutmen">
        <a class="rounded-3xl border-[1.5px] border-slate-200/90 bg-white/80 p-4 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.08)] backdrop-blur-xl transition hover:border-[#007AFF]/40 sm:p-5" href="{{ route('lowongan.index') }}">
            <span class="block text-xs font-semibold text-slate-500">Total lowongan</span>
            <span class="mt-2 block text-2xl font-extrabold tracking-tight text-slate-900 tabular-nums sm:text-3xl">{{ $statistik['total'] }}</span>
        </a>
        <a class="rounded-3xl border-[1.5px] border-slate-200/90 bg-white/80 p-4 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.08)] backdrop-blur-xl transition hover:border-[#007AFF]/40 sm:p-5" href="{{ route('lowongan.index', ['status' => 'aktif']) }}">
            <span class="block text-xs font-semibold text-emerald-700">Aktif</span>
            <span class="mt-2 block text-2xl font-extrabold tracking-tight text-emerald-600 tabular-nums sm:text-3xl">{{ $statistik['aktif'] }}</span>
        </a>
        <a class="rounded-3xl border-[1.5px] border-slate-200/90 bg-white/80 p-4 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.08)] backdrop-blur-xl transition hover:border-[#007AFF]/40 sm:p-5" href="{{ route('lowongan.index', ['status' => 'kedaluwarsa']) }}">
            <span class="block text-xs font-semibold text-amber-700">Kedaluwarsa</span>
            <span class="mt-2 block text-2xl font-extrabold tracking-tight text-amber-600 tabular-nums sm:text-3xl">{{ $statistik['kedaluwarsa'] }}</span>
        </a>
        <a class="rounded-3xl border-[1.5px] border-slate-200/90 bg-white/80 p-4 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.08)] backdrop-blur-xl transition hover:border-[#007AFF]/40 sm:p-5" href="{{ route('perusahaan.pelamar.index') }}">
            <span class="block text-xs font-semibold text-[#007AFF]">Lamaran masuk</span>
            <span class="mt-2 block text-2xl font-extrabold tracking-tight text-slate-900 tabular-nums sm:text-3xl">{{ $statistik['lamaran'] }}</span>
        </a>
    </div>

    <form class="flex flex-col gap-3 rounded-3xl border-[1.5px] border-slate-200/90 bg-white/80 p-4 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.08)] backdrop-blur-xl sm:flex-row sm:items-center" method="GET" role="search" aria-label="Cari dan saring lowongan" autocomplete="off">
        <div class="flex-1">
            <label class="sr-only" for="cari-lowongan">Cari posisi atau lokasi</label>
            <input id="cari-lowongan" type="search" name="kata_kunci" value="{{ request()->query('kata_kunci') }}" class="w-full rounded-full border border-slate-200/90 bg-white/95 px-5 py-2.5 text-sm text-slate-800 placeholder:text-slate-400 focus:border-[#007AFF] focus:outline-none" placeholder="Ketik posisi atau lokasi…" autocomplete="off">
        </div>
        <div class="sm:w-44">
            <label class="sr-only" for="status-lowongan">Saring menurut status</label>
            <select id="status-lowongan" name="status" class="w-full rounded-full border border-slate-200/90 bg-white/95 px-4 py-2.5 text-sm text-slate-800 focus:border-[#007AFF] focus:outline-none">
                <option value="">Semua status</option>
                @foreach (['aktif' => 'Aktif', 'kedaluwarsa' => 'Kedaluwarsa', 'ditutup' => 'Ditutup'] as $nilai => $label)
                    <option value="{{ $nilai }}" @selected(request()->query('status') === $nilai)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <button class="inline-flex min-h-11 shrink-0 items-center justify-center rounded-full bg-[#007AFF] px-6 text-sm font-semibold text-white shadow-md shadow-[#007AFF]/20 transition hover:bg-[#0062CC]" type="submit">Cari</button>
    </form>

    <section class="grid gap-4" aria-label="Lowongan yang saya pasang">
        @forelse ($daftarLowongan as $lowongan)
            <article class="rounded-3xl border-[1.5px] border-slate-200/90 bg-white/80 p-5 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.08)] backdrop-blur-xl transition hover:border-[#007AFF]/40 sm:p-6">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="rounded-full bg-[#E0F2FE] px-3 py-1 text-xs font-semibold text-[#007AFF]">{{ $lowongan->kategori->nama }}</span>
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">{{ $lowongan->tipe_kerja }}</span>
                        </div>
                        <h2 class="mt-2.5 truncate text-base font-bold text-slate-900">{{ $lowongan->judul }}</h2>
                        <p class="mt-1 flex items-center gap-1.5 text-xs text-slate-500">
                            <svg class="size-4 shrink-0 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 21s7-5.1 7-11a7 7 0 1 0-14 0c0 5.9 7 11 7 11Z"/><circle cx="12" cy="10" r="2.5"/></svg>
                            <span class="truncate">{{ $lowongan->lokasi }} · {{ $lowongan->batas_lamaran ? 'Batas lamaran ' . $lowongan->batas_lamaran->format('d M Y') : 'Tanpa batas waktu' }}</span>
                        </p>
                    </div>
                    @php($status = $lowongan->statusLowongan())
                    <span @class(['rounded-full px-3 py-1 text-xs font-bold', 'bg-emerald-100 text-emerald-800' => $status === 'Aktif', 'bg-amber-100 text-amber-800' => $status === 'Kedaluwarsa', 'bg-slate-100 text-slate-600' => $status === 'Ditutup'])>{{ $status }}</span>
                </div>
                <p class="mt-3 line-clamp-2 text-sm leading-relaxed text-slate-600">{{ $lowongan->deskripsi }}</p>
                <div class="mt-4 flex flex-wrap items-center justify-between gap-3 border-t border-slate-200/70 pt-4">
                    <div class="flex flex-wrap items-center gap-x-5 gap-y-1 text-xs">
                        <p class="font-bold text-slate-900">
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
                        <p class="text-slate-600"><strong class="font-bold text-slate-900 tabular-nums">{{ $lowongan->lamarans_count }}</strong> lamaran</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        @can('update', $lowongan)
                            <a class="rounded-full border border-slate-200 bg-white px-4 py-1.5 text-xs font-semibold text-slate-700 transition hover:border-[#007AFF]/40 hover:bg-[#E0F2FE] hover:text-[#007AFF]" href="{{ route('lowongan.edit', $lowongan) }}">Ubah</a>
                            <a class="rounded-full bg-[#E0F2FE] px-4 py-1.5 text-xs font-bold text-[#007AFF] transition hover:bg-sky-200/70" href="{{ route('lowongan.pelamar', $lowongan) }}">Lihat Pelamar</a>
                            <form method="POST" action="{{ route('lowongan.destroy', $lowongan) }}">
                                @csrf
                                @method('DELETE')
                                <button class="rounded-full bg-rose-50 px-4 py-1.5 text-xs font-bold text-rose-600 transition hover:bg-rose-100" type="submit">Hapus</button>
                            </form>
                        @endcan
                    </div>
                </div>
            </article>
        @empty
            <div class="rounded-3xl border-[1.5px] border-dashed border-slate-300 bg-white/80 p-10 text-center backdrop-blur-xl">
                <h2 class="text-base font-bold text-slate-900">{{ request()->filled('kata_kunci') || request()->filled('status') ? 'Lowongan tidak ditemukan' : 'Belum ada lowongan' }}</h2>
                <p class="mt-1 text-xs text-slate-500">{{ request()->filled('kata_kunci') || request()->filled('status') ? 'Tidak ada lowongan yang cocok. Coba kata kunci atau status lain.' : 'Lowongan yang Anda pasang akan muncul di sini bersama jumlah pelamarnya.' }}</p>
                @can('create', App\Models\Lowongan::class)
                    <a class="mt-4 inline-flex items-center rounded-full bg-[#007AFF] px-5 py-2 text-xs font-bold !text-white" href="{{ route('lowongan.create') }}" data-buka-modal="modal-lowongan">Pasang lowongan pertama</a>
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
                    <button class="tombol-utama" type="submit" data-teks-proses="Menyimpan…">Simpan</button>
                </footer>
            </form>
        </dialog>
    @endcan

    <div>{{ $daftarLowongan->links() }}</div>
</x-panel-perusahaan>
@endif
