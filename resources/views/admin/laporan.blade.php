<x-panel-admin judul="Laporan Pengguna & Lowongan" aktif="laporan">
    @if ($errors->any())
        <div class="rounded-3xl border-[1.5px] border-rose-300 bg-rose-50/90 px-5 py-4 text-sm font-medium text-rose-800 backdrop-blur-md" role="alert">{{ $errors->first() }}</div>
    @endif

    <form class="flex flex-col gap-3 rounded-3xl border-[1.5px] border-slate-200/90 bg-white/80 p-4 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.1)] backdrop-blur-xl sm:flex-row sm:items-center" method="GET">
        <div class="flex-1">
            <label class="sr-only" for="filter-jenis-laporan">Jenis laporan</label>
            <select id="filter-jenis-laporan" name="jenis" class="w-full rounded-full border border-slate-200/90 bg-white/95 px-4 py-2.5 text-sm text-slate-800 focus:border-[#007AFF] focus:outline-none">
                <option value="">Semua Sumber Laporan</option>
                <option value="lowongan" @selected($jenisAktif === 'lowongan')>Laporan Lowongan (dari Pekerja)</option>
                <option value="pengguna" @selected($jenisAktif === 'pengguna')>Laporan Pelamar (dari Perusahaan)</option>
            </select>
        </div>
        <div class="sm:w-52">
            <label class="sr-only" for="filter-status-laporan">Status laporan</label>
            <select id="filter-status-laporan" name="status" class="w-full rounded-full border border-slate-200/90 bg-white/95 px-4 py-2.5 text-sm text-slate-800 focus:border-[#007AFF] focus:outline-none">
                <option value="">Semua Status</option>
                @foreach (App\Models\Laporan::STATUS as $kunci => $label)
                    <option value="{{ $kunci }}" @selected($statusAktif === $kunci)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <button class="inline-flex min-h-11 shrink-0 items-center justify-center rounded-full bg-[#007AFF] px-6 text-sm font-semibold text-white shadow-md shadow-[#007AFF]/20 transition hover:bg-[#0062CC]" type="submit">Saring</button>
    </form>

    <div class="grid gap-4">
        @forelse ($daftarLaporan as $laporan)
            <article class="rounded-3xl border-[1.5px] border-slate-200/90 bg-white/85 p-5 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.08)] backdrop-blur-xl sm:p-6">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <span @class([
                                'rounded-full px-3 py-0.5 text-[11px] font-bold',
                                'bg-amber-100 text-amber-800' => $laporan->jenis === 'lowongan',
                                'bg-indigo-100 text-indigo-800' => $laporan->jenis === 'pengguna',
                            ])>{{ $laporan->jenis === 'lowongan' ? 'Laporan Loker' : 'Laporan Pelamar' }}</span>
                            <span class="rounded-full bg-slate-100 px-3 py-0.5 text-[11px] font-bold text-slate-700">{{ $laporan->kategori_laporan }}</span>
                            <span @class([
                                'rounded-full px-3 py-0.5 text-[11px] font-bold',
                                'bg-rose-100 text-rose-700' => $laporan->status === 'baru',
                                'bg-sky-100 text-[#007AFF]' => $laporan->status === 'ditinjau',
                                'bg-emerald-100 text-emerald-700' => $laporan->status === 'selesai',
                                'bg-slate-200 text-slate-600' => $laporan->status === 'ditolak',
                            ])>{{ App\Models\Laporan::STATUS[$laporan->status] ?? $laporan->status }}</span>
                        </div>

                        <h2 class="mt-2.5 text-base font-bold text-slate-900">
                            @if ($laporan->jenis === 'lowongan')
                                Loker: {{ $laporan->lowongan?->judul ?? 'Lowongan telah dihapus' }}
                            @else
                                Pelamar: {{ $laporan->terlaporUser?->name ?? 'Pengguna telah dihapus' }}
                            @endif
                        </h2>
                        <p class="mt-0.5 text-xs text-slate-500">
                            Dilaporkan oleh <strong class="text-slate-700">{{ $laporan->pelapor->name }}</strong> ({{ $laporan->pelapor->role === 'perusahaan' ? 'Perusahaan' : 'Pencari Kerja' }}) &middot; {{ $laporan->created_at->format('d M Y H:i') }}
                        </p>
                    </div>

                    @if ($laporan->terlaporUser)
                        <div class="text-right text-xs">
                            <p class="font-semibold text-slate-700">{{ $laporan->terlaporUser->name }}</p>
                            <p class="text-slate-400">{{ $laporan->terlaporUser->email }}</p>
                            @if ($laporan->terlaporUser->is_banned)
                                <span class="mt-1 inline-block rounded-full bg-rose-100 px-2.5 py-0.5 text-[10px] font-bold text-rose-700">Akun Dibanned</span>
                            @endif
                        </div>
                    @endif
                </div>

                <p class="mt-3 rounded-2xl border border-slate-200/70 bg-slate-50/80 p-3.5 text-xs leading-relaxed text-slate-700">{{ $laporan->deskripsi }}</p>

                <form method="POST" action="{{ route('admin.laporan.update', $laporan) }}" class="mt-4 flex flex-col gap-3 border-t border-slate-200/70 pt-4 sm:flex-row sm:items-end" data-kirim>
                    @csrf
                    @method('PATCH')
                    <div class="sm:w-40">
                        <label class="mb-1 block text-[11px] font-bold text-slate-600">Status</label>
                        <select name="status" class="w-full rounded-full border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 focus:border-[#007AFF] focus:outline-none">
                            @foreach (App\Models\Laporan::STATUS as $kunci => $label)
                                <option value="{{ $kunci }}" @selected($laporan->status === $kunci)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sm:w-52">
                        <label class="mb-1 block text-[11px] font-bold text-slate-600">Tindakan Tegas</label>
                        <select name="tindakan" class="w-full rounded-full border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 focus:border-[#007AFF] focus:outline-none">
                            <option value="none">Hanya ubah status</option>
                            @if ($laporan->lowongan && $laporan->lowongan->aktif)
                                <option value="nonaktifkan_lowongan">Nonaktifkan Lowongan</option>
                            @endif
                            @if ($laporan->terlaporUser && ! $laporan->terlaporUser->is_banned && $laporan->terlaporUser->role !== 'admin')
                                <option value="ban_pengguna">Banned Akun Terlapor</option>
                            @endif
                        </select>
                    </div>
                    <div class="flex-1">
                        <label class="mb-1 block text-[11px] font-bold text-slate-600">Catatan Admin</label>
                        <input type="text" name="catatan_admin" value="{{ $laporan->catatan_admin }}" maxlength="1000" class="w-full rounded-full border border-slate-200 bg-white px-4 py-2 text-xs text-slate-800 focus:border-[#007AFF] focus:outline-none" placeholder="Catatan penanganan laporan…">
                    </div>
                    <button type="submit" class="inline-flex min-h-9 shrink-0 items-center justify-center rounded-full bg-[#007AFF] px-5 text-xs font-bold text-white shadow-sm transition hover:bg-[#0062CC]" data-teks-proses="Menyimpan…">Simpan</button>
                </form>
            </article>
        @empty
            <div class="rounded-3xl border-[1.5px] border-dashed border-slate-300 bg-white/80 p-10 text-center">
                <p class="text-sm font-bold text-slate-800">Belum ada laporan masuk.</p>
                <p class="mt-1 text-xs text-slate-500">Laporan dari pencari kerja maupun perusahaan akan muncul di halaman ini.</p>
            </div>
        @endforelse
    </div>

    <div>{{ $daftarLaporan->links() }}</div>
</x-panel-admin>
