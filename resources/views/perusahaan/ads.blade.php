<x-panel-perusahaan judul="Lokerku Ads" aktif="dashboard">
    <div class="flex justify-start">
        <a href="{{ route('dashboard.role', ['role' => 'perusahaan']) }}" class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-700 transition hover:bg-[#E0F2FE] hover:text-[#007AFF]">
            <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="m15 18-6-6 6-6"/></svg>
            Kembali ke Dashboard
        </a>
    </div>

    @if ($errors->any())
        <div class="rounded-3xl border-[1.5px] border-rose-300 bg-rose-50/90 px-5 py-3.5 text-sm font-medium text-rose-800 backdrop-blur-xl" role="alert">{{ $errors->first() }}</div>
    @endif

    @if (! $paymenkuConfigured)
        <div class="rounded-3xl border-[1.5px] border-amber-300 bg-amber-50/90 p-5 text-xs text-amber-900 shadow-xs backdrop-blur-xl">
            <strong class="block text-sm font-bold">API Key Paymenku Belum Dipasang</strong>
            <p class="mt-1">Tambahkan <code>PAYMENKU_API_KEY</code> dan <code>PAYMENKU_WEBHOOK_SECRET</code> di file <code>.env</code> untuk menampilkan kanal pembayaran aktif dari Paymenku secara langsung. URL Webhook Anda: <code>{{ route('webhook.paymenku') }}</code></p>
        </div>
    @elseif ($paymenkuError)
        <div class="rounded-3xl border-[1.5px] border-rose-300 bg-rose-50/90 p-5 text-xs text-rose-800 backdrop-blur-xl">
            <strong>Koneksi Paymenku:</strong> {{ $paymenkuError }}
        </div>
    @endif

    <!-- 3 KPI Ringkasan Ads Perusahaan -->
    <div class="grid grid-cols-1 gap-3 sm:grid-cols-3 sm:gap-4">
        <section class="rounded-3xl border-[1.5px] border-slate-200/90 bg-white/85 p-5 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.08)] backdrop-blur-xl">
            <p class="text-xs font-semibold text-slate-500">Iklan Aktif Saat Ini</p>
            <p class="mt-1.5 text-2xl font-extrabold text-[#007AFF] tabular-nums sm:text-3xl">{{ number_format($iklanAktifCount, 0, ',', '.') }}</p>
            <p class="mt-1 text-[11px] text-slate-400">Tayang teratas + badge Rekomendasi</p>
        </section>
        <section class="rounded-3xl border-[1.5px] border-slate-200/90 bg-white/85 p-5 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.08)] backdrop-blur-xl">
            <p class="text-xs font-semibold text-slate-500">Total Budget Iklan</p>
            <p class="mt-1.5 text-2xl font-extrabold text-emerald-600 tabular-nums sm:text-3xl">Rp {{ number_format($totalInvestasi, 0, ',', '.') }}</p>
            <p class="mt-1 text-[11px] text-slate-400">Dari {{ $riwayatIklan->count() }} riwayat kampanye iklan</p>
        </section>
        <section class="rounded-3xl border-[1.5px] border-slate-200/90 bg-white/85 p-5 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.08)] backdrop-blur-xl">
            <p class="text-xs font-semibold text-slate-500">Pelamar dari Loker Beriklan</p>
            <p class="mt-1.5 text-2xl font-extrabold text-slate-900 tabular-nums sm:text-3xl">{{ number_format($totalPelamarIklan, 0, ',', '.') }}</p>
            <p class="mt-1 text-[11px] text-slate-400">Kandidat yang mendaftar</p>
        </section>
    </div>

    <!-- Form Pengaturan Budget, Durasi & Kanal Pembayaran Paymenku -->
    @if ($lowonganAktif->isNotEmpty())
        <form method="POST" action="{{ route('perusahaan.ads.store') }}" class="grid grid-cols-1 gap-5 lg:grid-cols-12" data-kirim id="form-perusahaan-ads">
            @csrf
            <div class="space-y-5 rounded-3xl border-[1.5px] border-slate-200/90 bg-white/85 p-5 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.08)] backdrop-blur-xl sm:p-6 lg:col-span-7">
                <!-- 1. Pilih Lowongan -->
                <div>
                    <label for="ads-lowongan-id" class="mb-2 block text-xs font-extrabold tracking-wide text-slate-700 uppercase">1. Pilih Lowongan Aktif</label>
                    <select id="ads-lowongan-id" name="lowongan_id" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-800 focus:border-[#007AFF] focus:outline-none" required onchange="updateRingkasanAds()">
                        @foreach ($lowonganAktif as $loker)
                            <option value="{{ $loker->id }}" data-judul="{{ $loker->judul }}" data-lokasi="{{ $loker->lokasi }}" data-tipe="{{ $loker->tipe_kerja }}">
                                {{ $loker->judul }} ({{ $loker->lokasi }}){{ $loker->sedangDiiklankan() ? ' — Sedang Diiklankan' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 2. Pilih Durasi & Harga -->
                <div>
                    <span class="mb-2 block text-xs font-extrabold tracking-wide text-slate-700 uppercase">2. Atur Durasi &amp; Harga Paket</span>
                    <div class="grid grid-cols-1 gap-2.5 sm:grid-cols-2">
                        @foreach ($paketIklan as $kunci => $detail)
                            <label class="relative flex cursor-pointer flex-col justify-between rounded-2xl border-[1.5px] border-slate-200 bg-white p-4 transition hover:border-[#007AFF]/50 has-[:checked]:border-[#007AFF] has-[:checked]:bg-[#E0F2FE]/45 has-[:checked]:shadow-sm">
                                <input type="radio" name="paket" value="{{ $kunci }}" data-harga="{{ $detail['harga'] }}" data-hari="{{ $detail['hari'] }}" data-nama="{{ $detail['nama'] }}" class="sr-only" @checked(old('paket', '7_hari') === $kunci) onchange="updateRingkasanAds()">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-sm font-extrabold text-slate-900">{{ $detail['hari'] }} Hari</span>
                                    <span class="rounded-full bg-[#E0F2FE] px-2.5 py-0.5 text-[10px] font-bold text-[#007AFF]">{{ $detail['nama'] }}</span>
                                </div>
                                <p class="mt-1.5 text-lg font-extrabold text-[#007AFF] tabular-nums">Rp {{ number_format($detail['harga'], 0, ',', '.') }}</p>
                                <p class="mt-0.5 text-[11px] text-slate-500">{{ $detail['per_hari'] }} &middot; {{ $detail['fitur'] }}</p>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- 3. Atur Budget Boost Tambahan -->
                <div>
                    <span class="mb-2 block text-xs font-extrabold tracking-wide text-slate-700 uppercase">3. Atur Budget Boost Tambahan</span>
                    <div class="grid grid-cols-2 gap-2.5 sm:grid-cols-4">
                        @foreach ($budgetBoost as $nominal => $labelBoost)
                            <label class="cursor-pointer rounded-2xl border-[1.5px] border-slate-200 bg-white px-3 py-2.5 text-center transition hover:border-[#007AFF]/50 has-[:checked]:border-[#007AFF] has-[:checked]:bg-[#007AFF] has-[:checked]:text-white">
                                <input type="radio" name="budget_tambahan" value="{{ $nominal }}" data-boost="{{ $nominal }}" class="sr-only" @checked((int) old('budget_tambahan', 0) === $nominal) onchange="updateRingkasanAds()">
                                <span class="block text-xs font-bold">{{ $nominal === 0 ? 'Standar' : '+Rp '.number_format($nominal / 1000, 0).'rb' }}</span>
                                <span class="block text-[10px] opacity-80">{{ $nominal === 0 ? 'Rp 0' : 'Prioritas' }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- 4. Kanal Pembayaran Paymenku (Dinamis dari API /payment-channels) -->
                <div>
                    <span class="mb-2 block text-xs font-extrabold tracking-wide text-slate-700 uppercase">4. Metode Pembayaran (Paymenku)</span>
                    @if ($kanalPembayaran !== [])
                        <div class="grid grid-cols-1 gap-2.5 sm:grid-cols-2">
                            @foreach ($kanalPembayaran as $indeks => $kanal)
                                @php($kodeKanal = $kanal['code'] ?? '')
                                <label class="flex cursor-pointer items-center gap-3 rounded-2xl border-[1.5px] border-slate-200 bg-white p-3.5 transition hover:border-[#007AFF]/50 has-[:checked]:border-[#007AFF] has-[:checked]:bg-[#E0F2FE]/45">
                                    <input type="radio" name="channel_code" value="{{ $kodeKanal }}" data-label="{{ $kanal['name'] ?? $kodeKanal }}" class="sr-only" @checked(old('channel_code', $kanalPembayaran[0]['code'] ?? '') === $kodeKanal) onchange="updateRingkasanAds()">
                                    @if (! empty($kanal['icon']))
                                        <img src="{{ $kanal['icon'] }}" alt="{{ $kanal['name'] ?? $kodeKanal }}" class="size-8 shrink-0 rounded-lg object-contain">
                                    @else
                                        <span class="grid size-8 shrink-0 place-items-center rounded-lg bg-[#E0F2FE] text-[10px] font-extrabold text-[#007AFF]">PAY</span>
                                    @endif
                                    <div class="min-w-0 flex-1">
                                        <strong class="block truncate text-xs font-extrabold text-slate-900">{{ $kanal['name'] ?? $kodeKanal }}</strong>
                                        <span class="block truncate text-[10px] text-slate-500">
                                            {{ $kanal['group'] ?? strtoupper((string) ($kanal['type'] ?? 'Paymenku')) }}
                                            &middot; Min Rp {{ number_format((int) ($kanal['min_amount'] ?? 1000), 0, ',', '.') }}
                                        </span>
                                    </div>
                                </label>
                            @endforeach
                        </div>

                        <div id="wadah-telepon-ovo" class="mt-3 hidden">
                            <label for="customer-phone-ovo" class="mb-1 block text-xs font-bold text-slate-700">Nomor HP OVO (Wajib untuk OVO)</label>
                            <input id="customer-phone-ovo" name="customer_phone" type="tel" value="{{ old('customer_phone', $pengguna->profil_pendaftaran['telepon'] ?? '') }}" placeholder="08123456789" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 focus:border-[#007AFF] focus:outline-none">
                        </div>
                    @else
                        <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-4 text-xs text-slate-500">
                            Kanal pembayaran akan tampil otomatis setelah <code>PAYMENKU_API_KEY</code> dikonfigurasi.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right Column: Ringkasan Pembayaran + Pratinjau Kartu Pencari Kerja -->
            <div class="space-y-4 lg:col-span-5">
                <section class="rounded-3xl bg-gradient-to-br from-[#007AFF] via-[#0A84FF] to-sky-500 p-6 text-white shadow-lg shadow-[#007AFF]/25">
                    <div class="flex items-center justify-between text-xs font-bold tracking-wider uppercase">
                        <span>Ringkasan Pembayaran</span>
                        <span class="rounded-full bg-white/20 px-2.5 py-0.5 text-[10px]" id="ringkasan-metode">{{ $kanalPembayaran[0]['name'] ?? 'Paymenku' }}</span>
                    </div>
                    <dl class="mt-4 space-y-2.5 border-b border-white/20 pb-4 text-xs">
                        <div class="flex justify-between">
                            <dt class="text-sky-100">Durasi Tayang</dt>
                            <dd class="font-bold" id="ringkasan-durasi">7 Hari</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sky-100">Harga Paket</dt>
                            <dd class="font-bold tabular-nums" id="ringkasan-harga-paket">Rp 50.000</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sky-100">Budget Boost Tambahan</dt>
                            <dd class="font-bold tabular-nums" id="ringkasan-budget-boost">Rp 0</dd>
                        </div>
                    </dl>
                    <div class="mt-4 flex items-end justify-between">
                        <div>
                            <p class="text-[11px] text-sky-100">Total Tagihan Dasar</p>
                            <p class="text-2xl font-extrabold tracking-tight tabular-nums sm:text-3xl" id="ringkasan-total">Rp 50.000</p>
                            <p class="mt-0.5 text-[10px] text-sky-100">Biaya admin kanal mengikuti pengaturan merchant Paymenku.</p>
                        </div>
                    </div>
                    <button type="submit" @disabled(! $paymenkuConfigured || $kanalPembayaran === []) class="mt-5 inline-flex min-h-11 w-full items-center justify-center rounded-full bg-white px-6 text-sm font-extrabold text-[#007AFF] shadow-md transition hover:bg-sky-50 disabled:cursor-not-allowed disabled:opacity-50" data-teks-proses="Membuat transaksi Paymenku…">
                        Bayar via Paymenku
                    </button>
                </section>

                <!-- Live Preview Kartu di Halaman Pencari Kerja -->
                <section class="rounded-3xl border-[1.5px] border-slate-200/90 bg-white/85 p-5 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.08)] backdrop-blur-xl">
                    <p class="text-[11px] font-bold tracking-wider text-slate-400 uppercase">Pratinjau di Pencari Kerja (Posisi #1)</p>
                    <div class="mt-3 rounded-2xl border border-[#007AFF]/35 bg-white p-4 shadow-sm">
                        <div class="flex items-start gap-3">
                            <span class="grid size-10 shrink-0 place-items-center rounded-xl border border-sky-200 bg-[#E0F2FE] text-xs font-extrabold text-[#007AFF]">{{ mb_strtoupper(mb_substr(str_replace('PT ', '', $pengguna->name), 0, 1)) }}</span>
                            <div class="min-w-0 flex-1">
                                <h3 class="truncate text-sm font-bold text-slate-900" id="preview-judul-loker">{{ $lowonganAktif->first()->judul }}</h3>
                                <p class="truncate text-xs text-slate-500">{{ $pengguna->name }} &middot; <span id="preview-lokasi-loker">{{ $lowonganAktif->first()->lokasi }}</span></p>
                            </div>
                        </div>
                        <div class="mt-3 flex flex-wrap gap-1.5">
                            <span class="inline-flex items-center gap-1 rounded-full bg-gradient-to-r from-[#007AFF] to-sky-500 px-2.5 py-0.5 text-[11px] font-bold text-white shadow-xs">&#9733; Rekomendasi</span>
                            <span class="rounded-full bg-[#E0F2FE] px-2.5 py-0.5 text-[11px] font-semibold text-[#007AFF]" id="preview-tipe-loker">{{ $lowonganAktif->first()->tipe_kerja }}</span>
                        </div>
                    </div>
                </section>
            </div>
        </form>
    @else
        <div class="rounded-3xl border-[1.5px] border-dashed border-slate-300 bg-white/80 p-10 text-center">
            <h2 class="text-base font-bold text-slate-900">Belum ada lowongan aktif untuk diiklankan</h2>
            <p class="mt-1 text-xs text-slate-500">Buat lowongan aktif terlebih dahulu agar dapat mempromosikannya di Lokerku Ads.</p>
            <a href="{{ route('lowongan.index') }}" class="mt-4 inline-flex rounded-full bg-[#007AFF] px-5 py-2.5 text-xs font-bold !text-white">Ke Kelola Lowongan</a>
        </div>
    @endif

    <!-- Riwayat Transaksi Paymenku -->
    @if ($riwayatTransaksi->isNotEmpty())
        <section class="rounded-3xl border-[1.5px] border-slate-200/90 bg-white/85 p-5 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.08)] backdrop-blur-xl sm:p-6">
            <h2 class="text-base font-bold text-slate-900">Riwayat Pembayaran Paymenku</h2>
            <div class="mt-4 grid gap-3">
                @foreach ($riwayatTransaksi as $trx)
                    <div class="flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-slate-200/80 bg-white p-4">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <span @class([
                                    'rounded-full px-2.5 py-0.5 text-[11px] font-bold uppercase',
                                    'bg-emerald-100 text-emerald-700' => $trx->status === 'paid',
                                    'bg-amber-100 text-amber-800' => $trx->status === 'pending',
                                    'bg-rose-100 text-rose-700' => in_array($trx->status, ['expired', 'cancelled', 'failed'], true),
                                ])>{{ $trx->status }}</span>
                                <span class="text-xs font-bold text-slate-700">{{ $trx->channel_name ?? $trx->channel_code }}</span>
                                <span class="text-[11px] text-slate-400">{{ $trx->reference_id }}</span>
                            </div>
                            <p class="mt-1 truncate text-sm font-bold text-slate-900">{{ $trx->lowongan?->judul ?? 'Lowongan' }} ({{ $trx->durasi_hari }} Hari)</p>
                            <p class="text-xs text-slate-500">Total: <strong>Rp {{ number_format((float) ($trx->amount_bayar ?? $trx->amount_dasar), 0, ',', '.') }}</strong> &middot; {{ $trx->created_at->format('d M Y H:i') }}</p>
                        </div>
                        <a href="{{ route('perusahaan.ads.status', $trx) }}" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-[#007AFF] transition hover:bg-[#E0F2FE]">Detail Pembayaran</a>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <!-- Daftar Iklan Perusahaan -->
    <section class="rounded-3xl border-[1.5px] border-slate-200/90 bg-white/85 p-5 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.08)] backdrop-blur-xl sm:p-6">
        <div class="flex items-center justify-between gap-2">
            <div>
                <h2 class="text-base font-bold text-slate-900">Daftar Iklan Perusahaan Anda</h2>
                <p class="text-xs text-slate-500">Semua iklan aktif langsung tersinkronisasi dengan Admin Lokerku &amp; tampil paling atas.</p>
            </div>
            <span class="rounded-full bg-[#E0F2FE] px-3 py-1 text-xs font-bold text-[#007AFF]">{{ $riwayatIklan->count() }} Kampanye</span>
        </div>

        <div class="mt-4 grid gap-3">
            @forelse ($riwayatIklan as $item)
                @php($sedangAktif = $item->sedangDiiklankan())
                <div class="flex flex-col justify-between gap-3 rounded-2xl border border-slate-200/80 bg-white/95 p-4 sm:flex-row sm:items-center">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <span @class([
                                'rounded-full px-2.5 py-0.5 text-[11px] font-bold',
                                'bg-emerald-100 text-emerald-700' => $sedangAktif,
                                'bg-slate-200 text-slate-600' => ! $sedangAktif,
                            ])>{{ $sedangAktif ? 'Sedang Tayang' : 'Kedaluwarsa' }}</span>
                            @if ($item->iklan_paket)
                                <span class="rounded-full bg-[#E0F2FE] px-2.5 py-0.5 text-[11px] font-bold text-[#007AFF]">{{ $item->iklan_paket }}</span>
                            @endif
                            <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-[11px] font-semibold text-slate-600">{{ $item->lamarans_count }} Pelamar</span>
                        </div>
                        <h3 class="mt-1.5 truncate text-sm font-bold text-slate-900">{{ $item->judul }}</h3>
                        <p class="text-xs text-slate-500">Berakhir pada: <strong>{{ $item->iklan_sampai?->format('d M Y H:i') ?? '-' }}</strong></p>
                    </div>
                    @if ($sedangAktif)
                        <form method="POST" action="{{ route('perusahaan.ads.destroy', $item) }}" data-kirim>
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="rounded-full bg-rose-50 px-4 py-2 text-xs font-bold text-rose-600 transition hover:bg-rose-100">Hentikan Iklan</button>
                        </form>
                    @endif
                </div>
            @empty
                <p class="py-6 text-center text-xs text-slate-500">Belum ada riwayat iklan lowongan.</p>
            @endforelse
        </div>
    </section>

    <script>
        function formatRupiahAds(angka) {
            return 'Rp ' + Number(angka || 0).toLocaleString('id-ID');
        }

        function updateRingkasanAds() {
            const selectLoker = document.getElementById('ads-lowongan-id');
            const opsiLoker = selectLoker?.options[selectLoker.selectedIndex];
            const paketRadio = document.querySelector('input[name="paket"]:checked');
            const boostRadio = document.querySelector('input[name="budget_tambahan"]:checked');
            const kanalRadio = document.querySelector('input[name="channel_code"]:checked');

            const hari = Number(paketRadio?.dataset.hari || 7);
            const hargaPaket = Number(paketRadio?.dataset.harga || 50000);
            const boost = Number(boostRadio?.dataset.boost || 0);
            const total = hargaPaket + boost;

            if (document.getElementById('ringkasan-durasi')) {
                document.getElementById('ringkasan-durasi').textContent = hari + ' Hari';
                document.getElementById('ringkasan-harga-paket').textContent = formatRupiahAds(hargaPaket);
                document.getElementById('ringkasan-budget-boost').textContent = formatRupiahAds(boost);
                document.getElementById('ringkasan-total').textContent = formatRupiahAds(total);
                if (kanalRadio) {
                    document.getElementById('ringkasan-metode').textContent = kanalRadio.dataset.label || kanalRadio.value;
                }
            }

            const wadahOvo = document.getElementById('wadah-telepon-ovo');
            if (wadahOvo && kanalRadio) {
                wadahOvo.classList.toggle('hidden', kanalRadio.value.toLowerCase() !== 'ovo');
            }

            if (opsiLoker) {
                document.getElementById('preview-judul-loker').textContent = opsiLoker.dataset.judul || '';
                document.getElementById('preview-lokasi-loker').textContent = opsiLoker.dataset.lokasi || '';
                document.getElementById('preview-tipe-loker').textContent = opsiLoker.dataset.tipe || '';
            }
        }
        document.addEventListener('DOMContentLoaded', updateRingkasanAds);
    </script>
</x-panel-perusahaan>
