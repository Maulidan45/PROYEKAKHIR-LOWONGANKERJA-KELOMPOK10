<x-layout :judul="'Riwayat Lamaran | Lokerku'">
    <div class="mx-auto w-[min(72rem,calc(100%-2rem))] py-8 sm:w-[min(72rem,calc(100%-3rem))] sm:py-12">
        <section class="mx-auto max-w-5xl">
            <header class="mb-6 flex flex-col items-start justify-between gap-4 border-b border-slate-200 pb-6 sm:flex-row sm:items-center">
                <div>
                    <p class="text-xs font-bold tracking-wider text-blue-700 uppercase">Pelacakan Seleksi</p>
                    <h1 class="mt-1 text-3xl font-bold tracking-tight text-slate-900">Riwayat Lamaran</h1>
                    <p class="mt-1 text-sm text-slate-600">Pantau perkembangan tahapan seleksi dari seluruh lamaran yang telah Anda kirim.</p>
                </div>
                <a class="tombol-utama shrink-0" href="{{ route('katalog') }}">Cari Lowongan</a>
            </header>

            <div class="mb-6 flex flex-wrap gap-2">
                <a @class(['rounded-lg border px-3 py-1.5 text-sm font-medium transition', 'border-blue-600 bg-blue-600 text-white' => ! $statusAktif, 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50' => $statusAktif]) href="{{ route('lamaran.index') }}">Semua</a>
                @foreach (App\Models\Lamaran::STATUS as $st)
                    <a @class(['rounded-lg border px-3 py-1.5 text-sm font-medium transition', 'border-blue-600 bg-blue-600 text-white' => $statusAktif === $st, 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50' => $statusAktif !== $st]) href="{{ route('lamaran.index', ['status' => $st]) }}">{{ $st }}</a>
                @endforeach
            </div>

            <div class="grid gap-4">
                @forelse ($daftarLamaran as $lamaran)
                    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs transition hover:border-blue-300 sm:p-6">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <p class="text-sm font-semibold text-blue-700">{{ $lamaran->lowongan->perusahaan->name }}</p>
                                    <x-status-lamaran :status="$lamaran->status" />
                                </div>
                                <h2 class="mt-1 text-xl font-bold tracking-tight text-slate-900">{{ $lamaran->lowongan->judul }}</h2>
                                <p class="mt-1 text-sm text-slate-600">{{ $lamaran->lowongan->lokasi }} · {{ $lamaran->lowongan->kategori->nama }}</p>
                                <p class="mt-2 text-xs text-slate-500">Dilamar pada {{ $lamaran->created_at->format('d M Y, H:i') }}</p>
                                @if ($lamaran->jadwalWawancaraTerbaru)
                                    <p class="mt-2 inline-flex items-center gap-1.5 rounded-lg bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-800">
                                        Jadwal Wawancara: {{ $lamaran->jadwalWawancaraTerbaru->tanggal->format('d M Y') }} pukul {{ $lamaran->jadwalWawancaraTerbaru->jam }}
                                    </p>
                                @endif
                            </div>
                            <div class="flex shrink-0 flex-wrap items-center gap-2">
                                <a class="tombol-utama" href="{{ route('lamaran.show', $lamaran) }}">Lihat Detail</a>
                            </div>
                        </div>
                    </article>
                @empty
                    <section class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-10 text-center">
                        <h2 class="text-xl font-semibold tracking-tight text-slate-900">Belum ada lamaran</h2>
                        <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-600">Pilih lowongan yang sesuai dari katalog untuk mulai melamar.</p>
                        <a class="tombol-utama mt-5" href="{{ route('katalog') }}">Lihat Lowongan</a>
                    </section>
                @endforelse
            </div>

            <div class="mt-8">{{ $daftarLamaran->links() }}</div>
        </section>
    </div>
</x-layout>
