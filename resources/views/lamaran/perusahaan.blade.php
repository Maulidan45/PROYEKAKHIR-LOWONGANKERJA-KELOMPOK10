<x-panel-perusahaan judul="Daftar Pelamar" aktif="pelamar">
    <form class="flex flex-col gap-3 rounded-3xl border-[1.5px] border-slate-200/90 bg-white/80 p-4 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.08)] backdrop-blur-xl sm:flex-row sm:items-center" method="GET">
        <div class="flex-1">
            <label class="sr-only" for="filter-lowongan">Filter lowongan</label>
            <select id="filter-lowongan" name="lowongan" class="w-full rounded-full border border-slate-200/90 bg-white/95 px-4 py-2.5 text-sm text-slate-800 focus:border-[#007AFF] focus:outline-none">
                <option value="">Semua Lowongan</option>
                @foreach ($daftarLowonganPerusahaan as $low)
                    <option value="{{ $low->id }}" @selected((string) $lowonganAktif === (string) $low->id)>{{ $low->judul }}</option>
                @endforeach
            </select>
        </div>
        <div class="sm:w-52">
            <label class="sr-only" for="filter-status">Filter status</label>
            <select id="filter-status" name="status" class="w-full rounded-full border border-slate-200/90 bg-white/95 px-4 py-2.5 text-sm text-slate-800 focus:border-[#007AFF] focus:outline-none">
                <option value="">Semua Tahapan</option>
                @foreach (App\Models\Lamaran::STATUS as $st)
                    <option value="{{ $st }}" @selected($statusAktif === $st)>{{ $st }}</option>
                @endforeach
            </select>
        </div>
        <button class="inline-flex min-h-11 shrink-0 items-center justify-center rounded-full bg-[#007AFF] px-6 text-sm font-semibold text-white shadow-md shadow-[#007AFF]/20 transition hover:bg-[#0062CC]" type="submit">Saring</button>
    </form>

    <section class="grid gap-4" aria-label="Daftar pelamar">
        @forelse ($daftarLamaran as $lamaran)
            @php($snap = $lamaran->data_pelamar ?? [])
            @php($namaPelamar = $snap['nama_lengkap'] ?? $lamaran->pelamar->name)
            <article class="rounded-3xl border-[1.5px] border-slate-200/90 bg-white/80 p-5 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.08)] backdrop-blur-xl sm:p-6">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div class="flex min-w-0 items-start gap-3.5">
                        <span class="grid size-11 shrink-0 place-items-center rounded-full bg-[#E0F2FE] text-sm font-extrabold text-[#007AFF]" aria-hidden="true">{{ mb_strtoupper(mb_substr($namaPelamar, 0, 1)) }}</span>
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <h2 class="text-base font-bold text-slate-900">{{ $namaPelamar }}</h2>
                                <x-status-lamaran :status="$lamaran->status" />
                            </div>
                            <p class="mt-0.5 text-xs text-slate-600">{{ $snap['email'] ?? $lamaran->pelamar->email }} &middot; {{ $snap['telepon'] ?? ($lamaran->pelamar->profil_pendaftaran['telepon'] ?? '-') }}</p>
                            <p class="mt-1 text-xs text-slate-500">
                                Melamar: <strong class="text-slate-700">{{ $lamaran->lowongan->judul }}</strong> &middot;
                                {{ $snap['pendidikan_terakhir'] ?? '-' }} &middot;
                                {{ $snap['domisili'] ?? '-' }} &middot;
                                {{ $lamaran->created_at->format('d M Y') }}
                            </p>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <a class="rounded-full bg-[#007AFF] px-4 py-2 text-xs font-bold !text-white shadow-sm shadow-[#007AFF]/20 transition hover:bg-[#0062CC]" href="{{ route('lamaran.show', $lamaran) }}">Lihat Detail</a>
                        @if ($lamaran->cv_path)
                            <a class="rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-700 transition hover:bg-[#E0F2FE] hover:text-[#007AFF]" href="{{ route('lamaran.dokumen', ['lamaran' => $lamaran, 'jenis' => 'cv']) }}">Lihat CV</a>
                        @endif
                    </div>
                </div>

                <div class="mt-4 flex flex-wrap items-center justify-between gap-3 border-t border-slate-200/70 pt-4">
                    <form class="flex flex-wrap items-center gap-2" method="POST" action="{{ route('lamaran.update', $lamaran) }}" data-kirim>
                        @csrf
                        @method('PATCH')
                        <select class="w-48 rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-700 focus:border-[#007AFF] focus:outline-none" name="status" aria-label="Tahapan seleksi {{ $namaPelamar }}">
                            @foreach (App\Models\Lamaran::STATUS as $st)
                                <option value="{{ $st }}" @selected($lamaran->status === $st)>{{ $st }}</option>
                            @endforeach
                        </select>
                        <button class="rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-700 transition hover:bg-[#E0F2FE] hover:text-[#007AFF]" type="submit" data-teks-proses="Menyimpan…">Proses Lamaran</button>
                    </form>

                    <div class="flex flex-wrap items-center gap-2">
                        <form method="POST" action="{{ route('lamaran.update', $lamaran) }}" data-kirim>
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="Diterima">
                            <button class="rounded-full bg-emerald-50 px-4 py-2 text-xs font-bold text-emerald-700 transition hover:bg-emerald-100" type="submit">Terima</button>
                        </form>
                        <form method="POST" action="{{ route('lamaran.update', $lamaran) }}" data-kirim>
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="Ditolak">
                            <button class="rounded-full bg-rose-50 px-4 py-2 text-xs font-bold text-rose-600 transition hover:bg-rose-100" type="submit">Tolak</button>
                        </form>
                        <button type="button" class="rounded-full border border-rose-200 bg-white px-3.5 py-2 text-xs font-bold text-rose-600 transition hover:bg-rose-50" onclick="const m = document.getElementById('modal-laporkan-pelamar'); document.getElementById('input-terlapor-pelamar').value = '{{ $lamaran->pelamar_id }}'; document.getElementById('input-lowongan-pelamar').value = '{{ $lamaran->lowongan_id }}'; document.getElementById('nama-terlapor-pelamar').textContent = @js($namaPelamar); m.classList.remove('hidden'); m.classList.add('flex');">Laporkan</button>
                    </div>
                </div>
            </article>
        @empty
            <div class="flex flex-col items-center justify-center rounded-3xl border-[1.5px] border-dashed border-slate-300 bg-white/80 px-6 py-12 text-center backdrop-blur-xl">
                <h2 class="text-base font-bold text-slate-900">Belum ada lamaran</h2>
                <p class="mt-1 max-w-md text-center text-xs text-slate-500">Lamaran untuk lowongan perusahaan Anda akan muncul di sini.</p>
                <a class="mt-4 inline-flex items-center rounded-full bg-[#007AFF] px-5 py-2 text-xs font-bold !text-white" href="{{ route('lowongan.create') }}">Pasang Lowongan</a>
            </div>
        @endforelse
    </section>

    <div>{{ $daftarLamaran->links() }}</div>

    <!-- Modal Laporkan Pelamar Bermasalah -->
    <div id="modal-laporkan-pelamar" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/45 p-4 backdrop-blur-sm" role="dialog" aria-modal="true" aria-labelledby="judul-laporkan-pelamar">
        <div class="w-full max-w-md rounded-3xl border border-slate-200 bg-white p-6 shadow-2xl">
            <h2 id="judul-laporkan-pelamar" class="text-base font-extrabold text-slate-900">Laporkan Pelamar Bermasalah</h2>
            <p class="mt-1 text-xs text-slate-500">Laporkan <strong id="nama-terlapor-pelamar" class="text-slate-800"></strong> ke admin jika terindikasi spam, dokumen palsu, atau perilaku tidak profesional.</p>
            <form method="POST" action="{{ route('laporan.store') }}" class="mt-4 space-y-4" data-kirim>
                @csrf
                <input type="hidden" name="jenis" value="pengguna">
                <input type="hidden" name="terlapor_user_id" id="input-terlapor-pelamar" value="">
                <input type="hidden" name="lowongan_id" id="input-lowongan-pelamar" value="">
                <div>
                    <label for="kat-laporan-pelamar" class="mb-1.5 block text-xs font-bold text-slate-700">Alasan Laporan</label>
                    <select id="kat-laporan-pelamar" name="kategori_laporan" required class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 focus:border-[#007AFF] focus:outline-none">
                        @foreach (App\Models\Laporan::KATEGORI_PENGGUNA as $kat)
                            <option value="{{ $kat }}">{{ $kat }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="desk-laporan-pelamar" class="mb-1.5 block text-xs font-bold text-slate-700">Detail Masalah</label>
                    <textarea id="desk-laporan-pelamar" name="deskripsi" rows="3" required minlength="10" maxlength="1500" class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm text-slate-800 focus:border-[#007AFF] focus:outline-none" placeholder="Jelaskan kendala atau pelanggaran yang dilakukan pelamar…"></textarea>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-50" onclick="const m = document.getElementById('modal-laporkan-pelamar'); m.classList.add('hidden'); m.classList.remove('flex');">Batal</button>
                    <button type="submit" class="rounded-full bg-rose-600 px-5 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-rose-700" data-teks-proses="Mengirim…">Kirim Laporan</button>
                </div>
            </form>
        </div>
    </div>
</x-panel-perusahaan>
