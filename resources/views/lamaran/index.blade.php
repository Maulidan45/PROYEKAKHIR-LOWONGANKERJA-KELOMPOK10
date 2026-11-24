<x-layout :judul="'Lamaran saya | Lokerku'">
    <div class="mx-auto w-[min(72rem,calc(100%-2rem))] py-8 sm:w-[min(72rem,calc(100%-3rem))] sm:py-12">
        <section class="mx-auto max-w-5xl">
            <header class="mb-8 flex flex-col items-start justify-between gap-4 border-b border-slate-200 pb-7 sm:flex-row sm:items-center">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">Lamaran saya</h1>
                </div>
                <a class="tombol" href="{{ route('home') }}">Cari lowongan</a>
            </header>

            <div class="grid gap-4">
                @forelse ($daftarLamaran as $lamaran)
                    <article class="rounded-lg border border-slate-200 bg-white p-5 transition-colors hover:border-blue-300 sm:p-6">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-blue-800">{{ $lamaran->lowongan->perusahaan->name }}</p>
                                <h2 class="mt-1 text-xl font-semibold tracking-tight text-slate-900">{{ $lamaran->lowongan->judul }}</h2>
                                <p class="mt-2 text-sm text-slate-600">{{ $lamaran->lowongan->lokasi }} · {{ $lamaran->lowongan->kategori->nama }}</p>
                                <p class="mt-3 text-sm text-slate-500">Dilamar pada {{ $lamaran->created_at->format('d M Y') }}</p>
                            </div>
                            <x-status-lamaran :status="$lamaran->status" />
                        </div>
                    </article>
                @empty
                    <section class="rounded-lg border border-dashed border-slate-300 bg-slate-50 p-8 text-center sm:p-12">
                        <h2 class="text-xl font-semibold tracking-tight text-slate-900">Belum ada lamaran</h2>
                        <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-600">Pilih lowongan yang sesuai dari katalog untuk mulai melamar.</p>
                        <a class="mt-5 inline-flex min-h-11 items-center font-semibold text-blue-800" href="{{ route('home') }}">Lihat lowongan</a>
                    </section>
                @endforelse
            </div>

            <div class="mt-8">{{ $daftarLamaran->links() }}</div>
        </section>
    </div>
</x-layout>
