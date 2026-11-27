@php
    $metaRingkasan = [
        'Akun terdaftar' => ['ikon' => 'M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75', 'bg' => 'bg-blue-50 text-blue-700', 'catatan' => 'Pengguna dari seluruh peran'],
        'Lowongan aktif' => ['ikon' => 'M4 7h16v12H4zM8 7V5h8v2M4 11h16M10 15h4', 'bg' => 'bg-emerald-50 text-emerald-700', 'catatan' => 'Tayang di katalog publik'],
        'Kategori' => ['ikon' => 'M4.5 4.5h6v6h-6zM13.5 4.5h6v6h-6zM4.5 13.5h6v6h-6zM13.5 13.5h6v6h-6z', 'bg' => 'bg-indigo-50 text-indigo-700', 'catatan' => 'Pengelompokan lowongan'],
        'Lamaran' => ['ikon' => 'M6 3h9l3 3v15H6zM9 12h6M9 16h6', 'bg' => 'bg-amber-50 text-amber-700', 'catatan' => 'Total lamaran tercatat'],
    ];
    $warnaLencana = [
        'admin' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
        'perusahaan' => 'bg-blue-50 text-blue-700 border-blue-200',
        'user' => 'bg-slate-100 text-slate-700 border-slate-200',
    ];
@endphp

<x-panel-admin judul="Ringkasan Sistem" label="Pusat Kendali" aktif="dashboard" deskripsi="Pantau statistik akun, verifikasi perusahaan, lowongan aktif, dan kategori dari satu tempat.">
    <x-slot:aksi>
        <a class="inline-flex min-h-11 items-center gap-2 rounded-lg border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 transition hover:border-blue-300 hover:bg-blue-50 hover:text-blue-700" href="{{ route('admin.verifikasi.index') }}">
            Verifikasi ({{ $menungguVerifikasi ?? 0 }})
        </a>
        <form method="POST" action="{{ route('admin.lowongan.sinkron') }}" data-kirim>
            @csrf
            <button class="tombol-utama inline-flex items-center gap-2 whitespace-nowrap" type="submit" data-teks-proses="Menarik data…">
                <svg class="size-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 11a8 8 0 1 0-2.3 5.7M20 5v6h-6"/></svg>
                Tarik Data API Remotive
            </button>
        </form>
    </x-slot:aksi>

    @if ($errors->any())
        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800" role="alert">{{ $errors->first() }}</div>
    @endif

    @if (($menungguVerifikasi ?? 0) > 0)
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3 rounded-xl border border-amber-200 bg-amber-50 px-5 py-4">
            <div>
                <p class="text-sm font-semibold text-amber-900">{{ $menungguVerifikasi }} perusahaan menunggu verifikasi legalitas</p>
                <p class="text-xs text-amber-700">Perusahaan baru belum dapat mempublikasikan lowongan sebelum disetujui admin.</p>
            </div>
            <a class="inline-flex min-h-9 items-center rounded-lg bg-amber-600 px-3.5 text-xs font-semibold text-white transition hover:bg-amber-700" href="{{ route('admin.verifikasi.index', ['status' => 'Menunggu Verifikasi']) }}">Tinjau Sekarang</a>
        </div>
    @endif

    <dl class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($ringkasan as $label => $nilai)
            @php($meta = $metaRingkasan[$label] ?? ['ikon' => 'M12 7v5l3 2', 'bg' => 'bg-blue-50 text-blue-700', 'catatan' => ''])
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs">
                <div class="flex items-center justify-between gap-3">
                    <dt class="text-sm font-medium text-slate-600">{{ $label }}</dt>
                    <span class="grid size-10 shrink-0 place-items-center rounded-xl {{ $meta['bg'] }}" aria-hidden="true">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $meta['ikon'] }}"/></svg>
                    </span>
                </div>
                <dd class="mt-3 text-3xl font-bold tracking-tight text-slate-900 tabular-nums">{{ number_format($nilai, 0, ',', '.') }}</dd>
                <p class="mt-1 text-xs text-slate-500">{{ $meta['catatan'] }}</p>
            </div>
        @endforeach
    </dl>

    <div class="grid gap-6 lg:grid-cols-2">
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xs" id="daftar-akun" aria-labelledby="judul-akun-terdaftar">
            <header class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 p-5">
                <div class="min-w-0">
                    <h2 class="text-lg font-bold text-slate-900" id="judul-akun-terdaftar">Akun Terdaftar</h2>
                    <p class="text-xs text-slate-500">Distribusi pengguna berdasarkan peran akun.</p>
                </div>
                <div class="flex flex-wrap gap-1.5">
                    @foreach ($jenisAkunTersedia as $kunci => $label)
                        <span class="inline-flex items-center gap-1 rounded-full border px-2.5 py-0.5 text-xs font-semibold {{ $warnaLencana[$kunci] ?? 'bg-slate-100 text-slate-700' }}">
                            {{ $label }} <strong class="tabular-nums">{{ $jumlahAkun[$kunci] ?? 0 }}</strong>
                        </span>
                    @endforeach
                </div>
            </header>
            <ul class="divide-y divide-slate-100">
                @forelse ($daftarAkun as $akun)
                    <li class="flex items-center gap-3 px-5 py-3.5 transition hover:bg-slate-50">
                        <span class="grid size-9 shrink-0 place-items-center rounded-full bg-blue-50 text-xs font-bold text-blue-700" aria-hidden="true">{{ mb_strtoupper(mb_substr($akun->name, 0, 1)) }}</span>
                        <div class="min-w-0 flex-1">
                            <strong class="block truncate text-sm font-semibold text-slate-900">{{ $akun->name }}</strong>
                            <p class="truncate text-xs text-slate-500">{{ $akun->email }}</p>
                        </div>
                        <span class="rounded-full border px-2.5 py-0.5 text-xs font-semibold {{ $warnaLencana[$akun->role] ?? 'bg-slate-100 text-slate-700' }}">{{ $jenisAkunTersedia[$akun->role] ?? $akun->role }}</span>
                    </li>
                @empty
                    <li class="px-5 py-8 text-center text-sm text-slate-500">Belum ada akun terdaftar.</li>
                @endforelse
            </ul>
            <div class="border-t border-slate-100 px-5 py-3">{{ $daftarAkun->links() }}</div>
        </section>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xs" aria-labelledby="judul-lowongan-admin">
            <header class="flex items-center justify-between gap-3 border-b border-slate-100 p-5">
                <div class="min-w-0">
                    <h2 class="text-lg font-bold text-slate-900" id="judul-lowongan-admin">Lowongan Aktif Terbaru</h2>
                    <p class="text-xs text-slate-500">Posisi terbaru yang tayang di katalog.</p>
                </div>
                <a class="text-sm font-semibold text-blue-700 hover:underline" href="{{ route('lowongan.index') }}">Kelola semua</a>
            </header>
            <ul class="divide-y divide-slate-100">
                @forelse ($lowonganTerbaruAdmin as $itemLowongan)
                    <li class="flex items-center gap-3 px-5 py-3.5 transition hover:bg-slate-50">
                        <span class="grid size-9 shrink-0 place-items-center rounded-lg bg-slate-100 text-xs font-bold text-slate-700" aria-hidden="true">{{ mb_strtoupper(mb_substr(str_replace('PT ', '', $itemLowongan->perusahaan->name), 0, 1)) }}</span>
                        <div class="min-w-0 flex-1">
                            <a class="block truncate text-sm font-semibold text-slate-900 hover:text-blue-700" href="{{ route('lowongan.show', $itemLowongan) }}">{{ $itemLowongan->judul }}</a>
                            <p class="truncate text-xs text-slate-500">{{ $itemLowongan->perusahaan->name }} &middot; {{ $itemLowongan->lokasi }}</p>
                        </div>
                        <span class="shrink-0 rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-medium text-blue-700">{{ $itemLowongan->kategori->nama }}</span>
                    </li>
                @empty
                    <li class="px-5 py-8 text-center text-sm text-slate-500">Belum ada lowongan aktif.</li>
                @endforelse
            </ul>
        </section>
    </div>
</x-panel-admin>
