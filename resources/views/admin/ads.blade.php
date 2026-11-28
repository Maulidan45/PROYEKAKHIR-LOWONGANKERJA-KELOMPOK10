<x-panel-admin judul="Kelola Ads Lowongan" aktif="dashboard">
    <x-slot:aksi>
        <a href="{{ route('dashboard.role', ['role' => 'admin']) }}" class="order-first mr-auto inline-flex min-h-9 items-center gap-1.5 rounded-full border-[1.5px] border-slate-200/90 bg-white/85 px-4 text-xs font-bold text-slate-700 shadow-xs backdrop-blur-md transition hover:border-[#007AFF]/40 hover:bg-[#E0F2FE] hover:text-[#007AFF] sm:min-h-10">
            <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="m15 18-6-6 6-6"/></svg>
            Kembali ke Beranda
        </a>
    </x-slot:aksi>

    <!-- 4 KPI Ringkasan Ads -->
    <div class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4">
        <section class="rounded-3xl border-[1.5px] border-slate-200/90 bg-white/85 p-4 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.08)] backdrop-blur-xl sm:p-5">
            <p class="text-xs font-semibold text-slate-500">Iklan Aktif</p>
            <p class="mt-1.5 text-2xl font-extrabold text-[#007AFF] tabular-nums sm:text-3xl">{{ number_format($statistikAds['aktif'], 0, ',', '.') }}</p>
            <p class="mt-1 text-[11px] text-slate-400">Tayang paling atas di katalog</p>
        </section>
        <section class="rounded-3xl border-[1.5px] border-slate-200/90 bg-white/85 p-4 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.08)] backdrop-blur-xl sm:p-5">
            <p class="text-xs font-semibold text-slate-500">Estimasi Pendapatan</p>
            <p class="mt-1.5 text-xl font-extrabold text-emerald-600 tabular-nums sm:text-2xl">Rp {{ number_format($statistikAds['pendapatan'], 0, ',', '.') }}</p>
            <p class="mt-1 text-[11px] text-slate-400">Dari {{ $statistikAds['total'] }} transaksi iklan</p>
        </section>
        <section class="rounded-3xl border-[1.5px] border-slate-200/90 bg-white/85 p-4 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.08)] backdrop-blur-xl sm:p-5">
            <p class="text-xs font-semibold text-slate-500">Perusahaan Beriklan</p>
            <p class="mt-1.5 text-2xl font-extrabold text-slate-900 tabular-nums sm:text-3xl">{{ number_format($statistikAds['perusahaan'], 0, ',', '.') }}</p>
            <p class="mt-1 text-[11px] text-slate-400">Mitra pengiklan terdaftar</p>
        </section>
        <section class="rounded-3xl border-[1.5px] border-slate-200/90 bg-white/85 p-4 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.08)] backdrop-blur-xl sm:p-5">
            <p class="text-xs font-semibold text-slate-500">Iklan Kedaluwarsa</p>
            <p class="mt-1.5 text-2xl font-extrabold text-slate-700 tabular-nums sm:text-3xl">{{ number_format($statistikAds['kedaluwarsa'], 0, ',', '.') }}</p>
            <p class="mt-1 text-[11px] text-slate-400">Masa tayang selesai</p>
        </section>
    </div>

    <!-- Grafik Ads: Distribusi Paket + Top Pengiklan -->
    <div class="grid grid-cols-1 gap-4 lg:grid-cols-12 sm:gap-5">
        <section class="rounded-3xl border-[1.5px] border-slate-200/90 bg-white/85 p-5 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.08)] backdrop-blur-xl sm:p-6 lg:col-span-6">
            <div class="flex items-center justify-between gap-2">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Grafik Distribusi Paket Ads</h2>
                    <p class="text-xs text-slate-500">Perbandingan paket iklan yang dipilih perusahaan</p>
                </div>
                <span class="rounded-full bg-[#E0F2FE] px-3 py-1 text-[11px] font-bold text-[#007AFF]">{{ $statistikAds['total'] }} Total</span>
            </div>

            @php($maksPaket = max(1, max($distribusiPaket)))
            <div class="mt-6 flex h-44 items-end justify-around gap-4 px-2">
                @foreach ($distribusiPaket as $label => $jumlah)
                    @php($persen = max(12, (int) round(($jumlah / $maksPaket) * 100)))
                    <div class="flex flex-1 flex-col items-center gap-2">
                        <span class="rounded-full bg-[#007AFF] px-2.5 py-0.5 text-[11px] font-bold text-white tabular-nums">{{ $jumlah }}</span>
                        <div class="flex h-28 w-full items-end justify-center">
                            <div class="w-full max-w-[56px] rounded-2xl bg-gradient-to-t from-[#007AFF] to-sky-400 shadow-md shadow-[#007AFF]/20 transition-all" style="height: {{ $persen }}%;"></div>
                        </div>
                        <span class="text-xs font-bold text-slate-700">{{ $label }}</span>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="rounded-3xl border-[1.5px] border-slate-200/90 bg-white/85 p-5 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.08)] backdrop-blur-xl sm:p-6 lg:col-span-6">
            <div class="flex items-center justify-between gap-2">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Perusahaan Pengiklan Teratas</h2>
                    <p class="text-xs text-slate-500">Kontribusi anggaran iklan per perusahaan</p>
                </div>
                <span class="rounded-full bg-emerald-100 px-3 py-1 text-[11px] font-bold text-emerald-700">Top 5</span>
            </div>

            @php($maksSpend = max(1, (int) ($topPerusahaan->max('pendapatan') ?? 1)))
            <div class="mt-4 space-y-3">
                @forelse ($topPerusahaan as $pt)
                    @php($lebar = max(10, (int) round(($pt['pendapatan'] / $maksSpend) * 100)))
                    <div>
                        <div class="flex items-center justify-between text-xs">
                            <strong class="truncate font-bold text-slate-800">{{ $pt['nama'] }}</strong>
                            <span class="shrink-0 font-bold text-[#007AFF] tabular-nums">Rp {{ number_format($pt['pendapatan'], 0, ',', '.') }} ({{ $pt['total_ads'] }} ads)</span>
                        </div>
                        <div class="mt-1.5 h-2.5 w-full overflow-hidden rounded-full bg-slate-100">
                            <div class="h-full rounded-full bg-gradient-to-r from-[#007AFF] to-sky-400" style="width: {{ $lebar }}%;"></div>
                        </div>
                    </div>
                @empty
                    <p class="py-10 text-center text-xs text-slate-500">Belum ada perusahaan yang memasang iklan lowongan.</p>
                @endforelse
            </div>
        </section>
    </div>

    <!-- Filter & Daftar Iklan -->
    <form class="flex flex-col gap-3 rounded-3xl border-[1.5px] border-slate-200/90 bg-white/80 p-4 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.08)] backdrop-blur-xl sm:flex-row sm:items-center" method="GET">
        <div class="flex-1">
            <label class="sr-only" for="cari-ads">Cari lowongan atau perusahaan</label>
            <input id="cari-ads" type="search" name="kata_kunci" value="{{ $cari }}" class="w-full rounded-full border border-slate-200/90 bg-white/95 px-5 py-2.5 text-sm text-slate-800 placeholder:text-slate-400 focus:border-[#007AFF] focus:outline-none" placeholder="Cari judul loker atau nama perusahaan…">
        </div>
        <div class="sm:w-48">
            <label class="sr-only" for="filter-status-ads">Status iklan</label>
            <select id="filter-status-ads" name="status" class="w-full rounded-full border border-slate-200/90 bg-white/95 px-4 py-2.5 text-sm text-slate-800 focus:border-[#007AFF] focus:outline-none">
                <option value="semua" @selected($statusAktif === 'semua')>Semua Iklan</option>
                <option value="aktif" @selected($statusAktif === 'aktif')>Sedang Aktif</option>
                <option value="kedaluwarsa" @selected($statusAktif === 'kedaluwarsa')>Kedaluwarsa</option>
            </select>
        </div>
        <button class="inline-flex min-h-11 shrink-0 items-center justify-center rounded-full bg-[#007AFF] px-6 text-sm font-semibold text-white shadow-md shadow-[#007AFF]/20 transition hover:bg-[#0062CC]" type="submit">Saring</button>
    </form>

    <div class="grid gap-4">
        @forelse ($daftarAds as $iklan)
            @php($aktifSekarang = $iklan->sedangDiiklankan())
            <article class="rounded-3xl border-[1.5px] border-slate-200/90 bg-white/85 p-5 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.08)] backdrop-blur-xl sm:p-6">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <span @class([
                                'rounded-full px-3 py-0.5 text-[11px] font-bold',
                                'bg-emerald-100 text-emerald-700' => $aktifSekarang,
                                'bg-slate-200 text-slate-600' => ! $aktifSekarang,
                            ])>{{ $aktifSekarang ? 'Ads Aktif' : 'Kedaluwarsa' }}</span>
                            @if ($iklan->iklan_paket)
                                <span class="rounded-full bg-[#E0F2FE] px-3 py-0.5 text-[11px] font-bold text-[#007AFF]">{{ $iklan->iklan_paket }}</span>
                            @endif
                            <span class="rounded-full bg-slate-100 px-3 py-0.5 text-[11px] font-semibold text-slate-600">{{ $iklan->lamarans_count }} Pelamar</span>
                        </div>
                        <h3 class="mt-2 text-base font-bold text-slate-900">
                            <a href="{{ route('lowongan.show', $iklan) }}" class="hover:text-[#007AFF]">{{ $iklan->judul }}</a>
                        </h3>
                        <p class="mt-0.5 text-xs text-slate-500">
                            {{ $iklan->perusahaan->name }} &middot; {{ $iklan->kategori?->nama ?? '-' }} &middot;
                            Berlaku s/d <strong>{{ $iklan->iklan_sampai?->format('d M Y H:i') ?? '-' }}</strong>
                        </p>
                    </div>
                </div>

                <div class="mt-4 flex flex-wrap items-center justify-between gap-3 border-t border-slate-200/70 pt-4">
                    <form method="POST" action="{{ route('admin.ads.update', $iklan) }}" class="flex flex-wrap items-center gap-2" data-kirim>
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="aksi" value="perpanjang">
                        <select name="paket" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-700 focus:border-[#007AFF] focus:outline-none">
                            <option value="3_hari">Perpanjang 3 Hari (Rp 25.000)</option>
                            <option value="7_hari">Perpanjang 7 Hari (Rp 50.000)</option>
                            <option value="14_hari">Perpanjang 14 Hari (Rp 90.000)</option>
                            <option value="30_hari">Perpanjang 30 Hari (Rp 150.000)</option>
                        </select>
                        <button type="submit" class="rounded-full bg-[#007AFF] px-4 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-[#0062CC]" data-teks-proses="Memproses…">Perpanjang Ads</button>
                    </form>

                    @if ($aktifSekarang)
                        <form method="POST" action="{{ route('admin.ads.update', $iklan) }}" data-kirim>
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="aksi" value="hentikan">
                            <button type="submit" class="rounded-full bg-rose-50 px-4 py-2 text-xs font-bold text-rose-600 transition hover:bg-rose-100">Hentikan Iklan</button>
                        </form>
                    @endif
                </div>
            </article>
        @empty
            <div class="rounded-3xl border-[1.5px] border-dashed border-slate-300 bg-white/80 p-10 text-center">
                <p class="text-sm font-bold text-slate-800">Belum ada lowongan yang diiklankan.</p>
                <p class="mt-1 text-xs text-slate-500">Iklan lowongan yang dibeli perusahaan akan muncul dan dapat dikelola di sini.</p>
            </div>
        @endforelse
    </div>

    <div>{{ $daftarAds->links() }}</div>
</x-panel-admin>
