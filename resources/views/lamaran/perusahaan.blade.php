<x-layout judul="Pelamar | Lokerku">
    <div class="mx-auto w-[min(72rem,calc(100%-2rem))] py-8 sm:w-[min(72rem,calc(100%-3rem))] sm:py-12">
        <section class="mx-auto min-w-0 max-w-5xl">
            <h1 class="teks-tersembunyi">Pelamar</h1>
            <section class="overflow-hidden rounded-lg border border-slate-200 bg-white" aria-label="Daftar pelamar">
                @forelse ($daftarLamaran as $lamaran)
                    <article class="flex flex-wrap items-center gap-4 border-b border-slate-100 px-5 py-4 last:border-b-0 sm:flex-nowrap sm:px-6">
                        <span class="grid size-10 shrink-0 place-items-center rounded-full bg-blue-50 text-sm font-bold text-blue-800" aria-hidden="true">{{ mb_strtoupper(mb_substr($lamaran->pelamar->name, 0, 1)) }}</span>
                        <div class="min-w-0 flex-1">
                            <strong class="block truncate text-sm font-semibold text-slate-900">{{ $lamaran->pelamar->name }}</strong>
                            <p class="mt-0.5 truncate text-sm text-slate-600">{{ $lamaran->pelamar->email }}</p>
                            <p class="mt-0.5 truncate text-xs text-slate-500">{{ $lamaran->lowongan->judul }} · {{ $lamaran->created_at->format('d M Y') }}</p>
                        </div>
                        <x-status-lamaran :status="$lamaran->status" />
                        <a class="tombol-kedua" href="{{ route('lowongan.pelamar', $lamaran->lowongan) }}">Tinjau</a>
                    </article>
                @empty
                    <div class="px-6 py-12 text-center">
                        <h2 class="text-lg font-semibold text-slate-900">Belum ada lamaran</h2>
                        <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-600">Lamaran untuk semua lowongan perusahaan akan muncul di sini.</p>
                        <a class="tombol-utama mt-4" href="{{ route('lowongan.create') }}">Pasang lowongan</a>
                    </div>
                @endforelse
            </section>
            <div class="mt-6">{{ $daftarLamaran->links() }}</div>
        </section>
    </div>
</x-layout>