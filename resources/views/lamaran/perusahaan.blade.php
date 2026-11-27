<x-layout judul="Daftar Pelamar | Lokerku">
    <div class="mx-auto w-[min(72rem,calc(100%-2rem))] py-8 sm:w-[min(72rem,calc(100%-3rem))] sm:py-12">
        <section class="mx-auto min-w-0 max-w-5xl">
            <x-kepala-halaman label="Rekrutmen" judul="Daftar Pelamar" deskripsi="Tinjau profil pelamar, unduh dokumen persyaratan, dan kelola tahapan seleksi.">
                <a class="tombol-kedua" href="{{ route('wawancara.index') }}">Jadwal Wawancara</a>
            </x-kepala-halaman>

            <form class="mb-6 flex flex-col gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-xs sm:flex-row sm:items-center" method="GET">
                <div class="flex-1">
                    <label class="sr-only" for="filter-lowongan">Filter lowongan</label>
                    <select id="filter-lowongan" name="lowongan">
                        <option value="">Semua Lowongan</option>
                        @foreach ($daftarLowonganPerusahaan as $low)
                            <option value="{{ $low->id }}" @selected((string) $lowonganAktif === (string) $low->id)>{{ $low->judul }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm:w-52">
                    <label class="sr-only" for="filter-status">Filter status</label>
                    <select id="filter-status" name="status">
                        <option value="">Semua Tahapan</option>
                        @foreach (App\Models\Lamaran::STATUS as $st)
                            <option value="{{ $st }}" @selected($statusAktif === $st)>{{ $st }}</option>
                        @endforeach
                    </select>
                </div>
                <button class="tombol-utama shrink-0" type="submit">Saring</button>
            </form>

            <section class="grid gap-4" aria-label="Daftar pelamar">
                @forelse ($daftarLamaran as $lamaran)
                    @php($snap = $lamaran->data_pelamar ?? [])
                    @php($namaPelamar = $snap['nama_lengkap'] ?? $lamaran->pelamar->name)
                    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs sm:p-6">
                        <div class="flex flex-wrap items-start justify-between gap-4">
                            <div class="flex min-w-0 items-start gap-3.5">
                                <span class="grid size-11 shrink-0 place-items-center rounded-full bg-blue-50 text-sm font-bold text-blue-800" aria-hidden="true">{{ mb_strtoupper(mb_substr($namaPelamar, 0, 1)) }}</span>
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h2 class="text-base font-bold text-slate-900">{{ $namaPelamar }}</h2>
                                        <x-status-lamaran :status="$lamaran->status" />
                                    </div>
                                    <p class="mt-0.5 text-sm text-slate-600">{{ $snap['email'] ?? $lamaran->pelamar->email }} &middot; {{ $snap['telepon'] ?? ($lamaran->pelamar->profil_pendaftaran['telepon'] ?? '-') }}</p>
                                    <p class="mt-1 text-xs text-slate-500">
                                        Melamar: <strong class="text-slate-700">{{ $lamaran->lowongan->judul }}</strong> &middot;
                                        {{ $snap['pendidikan_terakhir'] ?? '-' }} &middot;
                                        {{ $snap['domisili'] ?? '-' }} &middot;
                                        {{ $lamaran->created_at->format('d M Y') }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex flex-wrap items-center gap-2">
                                <a class="tombol-utama" href="{{ route('lamaran.show', $lamaran) }}">Lihat Profil &amp; Detail</a>
                                @if ($lamaran->cv_path)
                                    <a class="tombol-kedua" href="{{ route('lamaran.dokumen', ['lamaran' => $lamaran, 'jenis' => 'cv']) }}">Lihat CV</a>
                                @endif
                            </div>
                        </div>

                        <div class="mt-4 flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 pt-4">
                            <form class="flex flex-wrap items-center gap-2" method="POST" action="{{ route('lamaran.update', $lamaran) }}" data-kirim>
                                @csrf
                                @method('PATCH')
                                <select class="w-48" name="status" aria-label="Tahapan seleksi {{ $namaPelamar }}">
                                    @foreach (App\Models\Lamaran::STATUS as $st)
                                        <option value="{{ $st }}" @selected($lamaran->status === $st)>{{ $st }}</option>
                                    @endforeach
                                </select>
                                <button class="tombol-kedua" type="submit" data-teks-proses="Menyimpan…">Proses Lamaran</button>
                            </form>

                            <div class="flex flex-wrap items-center gap-2">
                                <form method="POST" action="{{ route('lamaran.update', $lamaran) }}" data-kirim>
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="Diterima">
                                    <button class="inline-flex min-h-10 items-center rounded-lg border border-emerald-200 bg-emerald-50 px-3.5 text-sm font-semibold text-emerald-800 transition hover:bg-emerald-100" type="submit">Terima</button>
                                </form>
                                <form method="POST" action="{{ route('lamaran.update', $lamaran) }}" data-kirim>
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="Ditolak">
                                    <button class="inline-flex min-h-10 items-center rounded-lg border border-red-200 bg-red-50 px-3.5 text-sm font-semibold text-red-800 transition hover:bg-red-100" type="submit">Tolak</button>
                                </form>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-12 text-center">
                        <h2 class="text-lg font-semibold text-slate-900">Belum ada lamaran</h2>
                        <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-600">Lamaran untuk lowongan perusahaan Anda akan muncul di sini.</p>
                        <a class="tombol-utama mt-4" href="{{ route('lowongan.create') }}">Pasang Lowongan</a>
                    </div>
                @endforelse
            </section>
            <div class="mt-6">{{ $daftarLamaran->links() }}</div>
        </section>
    </div>
</x-layout>
