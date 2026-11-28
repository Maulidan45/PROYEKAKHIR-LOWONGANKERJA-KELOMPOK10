<x-panel-user judul="Riwayat Lamaran" aktif="lamaran">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex flex-wrap gap-2">
            <a @class(['rounded-full border-[1.5px] px-4 py-1.5 text-xs font-semibold transition', 'border-[#007AFF] bg-[#007AFF] !text-white shadow-sm' => ! $statusAktif, 'border-slate-200/90 bg-white/85 text-slate-700 hover:bg-[#E0F2FE] hover:text-[#007AFF]' => $statusAktif]) href="{{ route('lamaran.index') }}">Semua</a>
            @foreach (App\Models\Lamaran::STATUS as $st)
                <a @class(['rounded-full border-[1.5px] px-4 py-1.5 text-xs font-semibold transition', 'border-[#007AFF] bg-[#007AFF] !text-white shadow-sm' => $statusAktif === $st, 'border-slate-200/90 bg-white/85 text-slate-700 hover:bg-[#E0F2FE] hover:text-[#007AFF]' => $statusAktif !== $st]) href="{{ route('lamaran.index', ['status' => $st]) }}">{{ $st }}</a>
            @endforeach
        </div>
        <a class="rounded-full bg-[#007AFF] px-5 py-2 text-xs font-bold !text-white shadow-sm shadow-[#007AFF]/20 transition hover:bg-[#0062CC]" href="{{ route('katalog') }}">Cari Lowongan</a>
    </div>

    <div class="grid gap-4">
        @forelse ($daftarLamaran as $lamaran)
            <article class="rounded-3xl border-[1.5px] border-slate-200/90 bg-white/85 p-5 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.08)] backdrop-blur-xl transition hover:border-[#007AFF]/40 sm:p-6">
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
                    <section class="flex flex-col items-center justify-center rounded-3xl border-[1.5px] border-dashed border-slate-300 bg-white/80 p-10 text-center">
                        <h2 class="text-base font-bold tracking-tight text-slate-900">Belum ada lamaran</h2>
                        <p class="mt-1.5 max-w-md text-center text-xs leading-5 text-slate-500">Pilih lowongan yang sesuai dari katalog untuk mulai melamar.</p>
                        <a class="mt-4 inline-flex rounded-full bg-[#007AFF] px-5 py-2 text-xs font-bold !text-white shadow-sm shadow-[#007AFF]/20" href="{{ route('katalog') }}">Lihat Lowongan</a>
                    </section>
                @endforelse
            </div>

            <div>{{ $daftarLamaran->links() }}</div>
</x-panel-user>
