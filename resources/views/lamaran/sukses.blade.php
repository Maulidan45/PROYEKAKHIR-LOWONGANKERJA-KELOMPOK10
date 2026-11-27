<x-layout judul="Lamaran Berhasil Dikirim | Lokerku">
    <div class="mx-auto w-[min(42rem,calc(100%-2rem))] py-12 sm:py-16">
        <section class="rounded-2xl border border-emerald-200 bg-white p-8 text-center shadow-xs">
            <div class="mx-auto grid size-16 place-items-center rounded-full bg-emerald-50 text-emerald-600" aria-hidden="true">
                <svg class="size-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="m9 12 2 2 4-4"/></svg>
            </div>
            <p class="mt-4 text-xs font-bold tracking-wider text-emerald-700 uppercase">Lamaran Terkirim</p>
            <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Lamaran Berhasil Dikirim!</h1>
            <p class="mt-3 text-sm leading-relaxed text-slate-600">
                Lamaran Anda untuk posisi <strong>{{ $lamaran->lowongan->judul }}</strong> di <strong>{{ $lamaran->lowongan->perusahaan->name }}</strong> telah diterima oleh sistem. Kami juga telah mengirimkan notifikasi konfirmasi ke akun Anda.
            </p>
            <div class="mt-6 flex flex-wrap justify-center gap-3">
                <a class="tombol-utama" href="{{ route('lamaran.show', $lamaran) }}">Lihat Detail &amp; Tracking</a>
                <a class="tombol-kedua" href="{{ route('lamaran.index') }}">Riwayat Lamaran</a>
            </div>
        </section>
    </div>
</x-layout>
