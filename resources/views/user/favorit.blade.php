<x-panel-user judul="Lowongan Favorit" aktif="favorit">
    <div class="grid grid-cols-2 gap-2.5 sm:gap-4 md:grid-cols-3">
        @forelse ($daftarFavorit as $lowongan)
            <article class="flex min-w-0 flex-col justify-between rounded-2xl border-[1.5px] border-slate-200/90 bg-white/90 p-3 shadow-[0_8px_24px_-10px_rgba(15,23,42,0.08)] backdrop-blur-xl transition hover:-translate-y-0.5 hover:border-[#007AFF]/40 sm:rounded-3xl sm:p-4">
                <div class="min-w-0">
                    <div class="flex items-start justify-between gap-1.5 sm:gap-2.5">
                        <div class="flex min-w-0 items-center gap-2 sm:gap-2.5">
                            @php
                                $fotoPt = $lowongan->perusahaan->foto_profil_path;
                                $logoUrl = $fotoPt
                                    ? (str_starts_with($fotoPt, 'http') ? $fotoPt : asset('storage/' . $fotoPt))
                                    : ($lowongan->sumber === 'remotive' && str_starts_with((string) $lowongan->eksternal_id, 'remotive-')
                                        ? 'https://remotive.com/job/' . substr((string) $lowongan->eksternal_id, 9) . '/logo'
                                        : null);
                            @endphp
                            @if ($logoUrl)
                                <img src="{{ $logoUrl }}" alt="{{ $lowongan->perusahaan->name }}" class="size-8 shrink-0 rounded-xl border border-slate-200 bg-white object-cover sm:size-10 sm:rounded-2xl" loading="lazy" onerror="this.classList.add('hidden'); this.nextElementSibling.classList.remove('hidden'); this.nextElementSibling.classList.add('grid');">
                                <span class="hidden size-8 shrink-0 place-items-center rounded-xl border border-sky-200 bg-[#E0F2FE] text-xs font-extrabold text-[#007AFF] sm:size-10 sm:rounded-2xl sm:text-sm">{{ mb_strtoupper(mb_substr(str_replace('PT ', '', $lowongan->perusahaan->name), 0, 1)) }}</span>
                            @else
                                <span class="grid size-8 shrink-0 place-items-center rounded-xl border border-sky-200 bg-[#E0F2FE] text-xs font-extrabold text-[#007AFF] sm:size-10 sm:rounded-2xl sm:text-sm">{{ mb_strtoupper(mb_substr(str_replace('PT ', '', $lowongan->perusahaan->name), 0, 1)) }}</span>
                            @endif
                            <div class="min-w-0">
                                <h2 class="line-clamp-2 !text-xs font-bold !leading-snug text-slate-900 sm:!text-sm">
                                    <a class="hover:text-[#007AFF]" href="{{ route('lowongan.show', $lowongan) }}">{{ $lowongan->judul }}</a>
                                </h2>
                                <p class="mt-0.5 truncate text-[10px] text-slate-500 sm:text-xs">{{ $lowongan->perusahaan->name }}</p>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('lowongan.simpan', $lowongan) }}" class="shrink-0">
                            @csrf
                            <button type="submit" class="grid size-7 shrink-0 place-items-center rounded-lg border border-[#007AFF] bg-[#E0F2FE] text-[#007AFF] transition hover:bg-rose-50 hover:border-rose-300 hover:text-rose-600 sm:size-8 sm:rounded-xl" title="Hapus dari favorit" aria-label="Hapus dari favorit">
                                <svg class="size-3.5 sm:size-4" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="1.8"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg>
                            </button>
                        </form>
                    </div>

                    <p class="mt-1.5 truncate text-[10px] font-medium text-slate-400">{{ $lowongan->lokasi }}</p>
                    <p class="mt-2 hidden text-xs leading-snug text-slate-500 sm:line-clamp-2">{{ Str::limit(trim(preg_replace('/\s+/', ' ', strip_tags((string) $lowongan->deskripsi))), 90) }}</p>

                    <div class="mt-2 flex flex-wrap gap-1 sm:mt-2.5 sm:gap-1.5">
                        @if ($lowongan->sedangDiiklankan())
                            <span class="inline-flex items-center gap-1 rounded-full bg-gradient-to-r from-[#007AFF] to-sky-500 px-2 py-0.5 text-[10px] font-bold text-white shadow-xs sm:px-2.5 sm:text-[11px]">&#9733; Rekomendasi</span>
                        @endif
                        <span class="rounded-full bg-[#E0F2FE] px-2 py-0.5 text-[10px] font-semibold text-[#007AFF] sm:px-2.5 sm:text-[11px]">{{ $lowongan->tipe_kerja }}</span>
                    </div>
                </div>

                <div class="mt-2.5 flex flex-col gap-2 border-t border-slate-200/60 pt-2.5 sm:mt-3 sm:flex-row sm:items-center sm:justify-between sm:pt-3">
                    <p class="truncate text-[11px] font-bold text-slate-800 sm:text-xs">
                        @if ($lowongan->gaji_min && $lowongan->gaji_max)
                            Rp {{ number_format($lowongan->gaji_min, 0, ',', '.') }}–{{ number_format($lowongan->gaji_max, 0, ',', '.') }}
                        @else
                            Gaji kompetitif
                        @endif
                    </p>
                    <a href="{{ route('lowongan.show', $lowongan) }}" class="inline-flex w-full shrink-0 items-center justify-center rounded-xl bg-[#007AFF] px-3 py-1.5 text-[11px] font-bold !text-white shadow-sm shadow-[#007AFF]/20 transition hover:bg-[#0062CC] sm:w-auto sm:px-3.5 sm:py-1.5 sm:text-xs">
                        Lihat Loker
                    </a>
                </div>
            </article>
        @empty
            <div class="col-span-full flex flex-col items-center justify-center rounded-3xl border-[1.5px] border-dashed border-slate-300 bg-white/80 p-10 text-center">
                <p class="text-sm font-bold text-slate-800">Belum ada lowongan favorit.</p>
                <p class="mt-1 text-xs text-slate-500">Klik ikon simpan pada lowongan yang Anda minati agar muncul di sini.</p>
                <a href="{{ route('katalog') }}" class="mt-4 inline-flex rounded-full bg-[#007AFF] px-5 py-2 text-xs font-bold !text-white">Cari Lowongan</a>
            </div>
        @endforelse
    </div>

    <div>{{ $daftarFavorit->links() }}</div>
</x-panel-user>
