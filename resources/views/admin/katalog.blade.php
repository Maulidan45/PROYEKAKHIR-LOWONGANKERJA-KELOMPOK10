<x-panel-admin judul="Katalog Lowongan" aktif="katalog">
    <!-- Search & Category Filter Bar -->
    <form class="flex flex-col gap-3 rounded-3xl border-[1.5px] border-slate-200/90 bg-white/80 p-3.5 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.1)] backdrop-blur-xl sm:flex-row sm:items-center sm:p-4" method="GET">
        <div class="flex-1">
            <label class="sr-only" for="cari-katalog-admin">Cari posisi, lokasi, atau perusahaan</label>
            <input id="cari-katalog-admin" type="search" name="kata_kunci" value="{{ $cari }}" class="w-full rounded-full border border-slate-200/90 bg-white/95 px-5 py-2.5 text-sm text-slate-800 placeholder:text-slate-400 focus:border-[#007AFF] focus:outline-none" placeholder="Cari posisi, lokasi, atau perusahaan…">
        </div>
        <div class="sm:w-52">
            <label class="sr-only" for="filter-kategori-katalog">Filter kategori</label>
            <select id="filter-kategori-katalog" name="kategori" class="w-full rounded-full border border-slate-200/90 bg-white/95 px-4 py-2.5 text-sm text-slate-800 focus:border-[#007AFF] focus:outline-none">
                <option value="">Semua Kategori</option>
                @foreach ($daftarKategori as $kat)
                    <option value="{{ $kat->id }}" @selected($kategoriAktif === (string) $kat->id)>{{ $kat->nama }}</option>
                @endforeach
            </select>
        </div>
        <button class="inline-flex min-h-11 shrink-0 items-center justify-center rounded-full bg-[#007AFF] px-6 text-sm font-semibold text-white shadow-md shadow-[#007AFF]/20 transition hover:bg-[#0062CC]" type="submit">Cari</button>
    </form>

    <!-- Clean 3-Column Job Grid -->
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3 sm:gap-5">
        @forelse ($daftarLowongan as $lowongan)
            @php
                $gajiTeks = ($lowongan->gaji_min && $lowongan->gaji_max)
                    ? 'Rp ' . number_format($lowongan->gaji_min, 0, ',', '.') . '–' . number_format($lowongan->gaji_max, 0, ',', '.')
                    : 'Gaji sesuai ketentuan';
                $detailPayload = [
                    'judul' => $lowongan->judul,
                    'perusahaan' => $lowongan->perusahaan->name,
                    'inisial' => mb_strtoupper(mb_substr(str_replace('PT ', '', $lowongan->perusahaan->name), 0, 1)),
                    'lokasi' => $lowongan->lokasi,
                    'tipe' => $lowongan->tipe_kerja,
                    'kategori' => $lowongan->kategori->nama,
                    'gaji' => $gajiTeks,
                    'waktu' => $lowongan->created_at->locale('id')->diffForHumans(),
                    'batas' => $lowongan->batas_lamaran ? $lowongan->batas_lamaran->locale('id')->translatedFormat('d M Y') : 'Tidak ditentukan',
                    'deskripsi' => strip_tags((string) $lowongan->deskripsi),
                    'eksternal' => $lowongan->adalahEksternal(),
                    'sumber_url' => $lowongan->sumber_url,
                ];
            @endphp
            <article class="flex flex-col justify-between rounded-3xl border-[1.5px] border-slate-200/90 bg-white/80 p-5 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.1)] backdrop-blur-xl transition hover:-translate-y-0.5 hover:shadow-xl sm:rounded-[28px] sm:p-6">
                <div>
                    <div class="flex items-center gap-3">
                        <span class="grid size-11 shrink-0 place-items-center rounded-2xl border border-sky-200 bg-[#E0F2FE] text-sm font-extrabold text-[#007AFF]" aria-hidden="true">{{ $detailPayload['inisial'] }}</span>
                        <div class="min-w-0">
                            <p class="truncate text-xs font-semibold text-slate-500">{{ $lowongan->perusahaan->name }}</p>
                            <p class="mt-0.5 truncate text-xs text-slate-400">{{ $lowongan->lokasi }}</p>
                        </div>
                    </div>

                    <h2 class="mt-4 text-base font-bold leading-snug text-slate-900">
                        <button type="button" class="text-left transition hover:text-[#007AFF]" onclick='bukaDetailLokerAdmin(@json($detailPayload))'>{{ $lowongan->judul }}</button>
                    </h2>

                    <div class="mt-3 flex flex-wrap gap-1.5">
                        <span class="rounded-full bg-[#E0F2FE] px-3 py-1 text-xs font-semibold text-[#007AFF]">{{ $lowongan->tipe_kerja }}</span>
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">{{ $lowongan->kategori->nama }}</span>
                    </div>
                </div>

                <div class="mt-5 flex items-center justify-between gap-2 border-t border-slate-200/70 pt-4">
                    <div class="min-w-0">
                        <p class="truncate text-xs font-bold text-slate-800">{{ $gajiTeks }}</p>
                        <p class="text-[11px] text-slate-400">{{ $detailPayload['waktu'] }}</p>
                    </div>
                    <button type="button" class="inline-flex shrink-0 items-center justify-center rounded-full bg-[#007AFF] px-4 py-2 text-xs font-bold text-white shadow-sm shadow-[#007AFF]/20 transition hover:bg-[#0062CC]" onclick='bukaDetailLokerAdmin(@json($detailPayload))'>
                        Lihat
                    </button>
                </div>
            </article>
        @empty
            <div class="col-span-full rounded-3xl border-[1.5px] border-dashed border-slate-300 bg-white/75 p-10 text-center text-sm text-slate-500">Tidak ada lowongan tersedia.</div>
        @endforelse
    </div>

    <div>{{ $daftarLowongan->links() }}</div>

    <!-- In-Admin iOS Glass Detail Modal (No external page redirect) -->
    <div id="modal-detail-loker-admin" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/35 p-4 backdrop-blur-sm" role="dialog" aria-modal="true" aria-labelledby="modal-loker-judul" onclick="if (event.target === this) tutupDetailLokerAdmin()">
        <div class="max-h-[85vh] w-full max-w-2xl overflow-y-auto rounded-[28px] border-[1.5px] border-slate-200/90 bg-white/95 p-6 shadow-2xl backdrop-blur-2xl sm:p-8">
            <div class="flex items-start justify-between gap-4 border-b border-slate-200/70 pb-5">
                <div class="flex min-w-0 items-center gap-3.5">
                    <span id="modal-loker-inisial" class="grid size-12 shrink-0 place-items-center rounded-2xl border border-sky-200 bg-[#E0F2FE] text-base font-extrabold text-[#007AFF]">L</span>
                    <div class="min-w-0">
                        <h2 id="modal-loker-judul" class="text-lg font-extrabold text-slate-900 sm:text-xl"></h2>
                        <p id="modal-loker-perusahaan" class="mt-0.5 text-sm font-medium text-slate-500"></p>
                    </div>
                </div>
                <button type="button" onclick="tutupDetailLokerAdmin()" class="grid size-9 shrink-0 place-items-center rounded-full border border-slate-200 bg-white text-slate-500 transition hover:bg-slate-100 hover:text-slate-900" aria-label="Tutup detail">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="mt-4 flex flex-wrap gap-2">
                <span id="modal-loker-tipe" class="rounded-full bg-[#E0F2FE] px-3 py-1 text-xs font-semibold text-[#007AFF]"></span>
                <span id="modal-loker-kategori" class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-700"></span>
                <span id="modal-loker-lokasi" class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-700"></span>
            </div>

            <div class="mt-5 grid grid-cols-1 gap-3 rounded-2xl border border-slate-200/80 bg-slate-50/80 p-4 sm:grid-cols-3">
                <div>
                    <p class="text-[11px] font-semibold text-slate-400 uppercase">Gaji</p>
                    <p id="modal-loker-gaji" class="mt-0.5 text-xs font-bold text-slate-900"></p>
                </div>
                <div>
                    <p class="text-[11px] font-semibold text-slate-400 uppercase">Batas Lamaran</p>
                    <p id="modal-loker-batas" class="mt-0.5 text-xs font-bold text-slate-900"></p>
                </div>
                <div>
                    <p class="text-[11px] font-semibold text-slate-400 uppercase">Diposting</p>
                    <p id="modal-loker-waktu" class="mt-0.5 text-xs font-bold text-slate-900"></p>
                </div>
            </div>

            <div class="mt-5">
                <h3 class="text-xs font-bold tracking-wider text-slate-400 uppercase">Deskripsi Pekerjaan</h3>
                <p id="modal-loker-deskripsi" class="mt-2 whitespace-pre-line text-sm leading-relaxed text-slate-600"></p>
            </div>

            <div class="mt-6 flex flex-wrap items-center justify-end gap-2 border-t border-slate-200/70 pt-4">
                <button type="button" onclick="tutupDetailLokerAdmin()" class="rounded-full bg-[#007AFF] px-5 py-2 text-xs font-bold text-white shadow-sm shadow-[#007AFF]/20 transition hover:bg-[#0062CC]">Tutup</button>
            </div>
        </div>
    </div>
</x-panel-admin>
