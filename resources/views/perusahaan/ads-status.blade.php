<x-panel-perusahaan judul="Pembayaran Ads Paymenku" aktif="dashboard">
    @php($info = $transaksi->payment_info ?? [])

    <div class="flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('perusahaan.ads.index') }}" class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-700 transition hover:bg-[#E0F2FE] hover:text-[#007AFF]">
            <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="m15 18-6-6 6-6"/></svg>
            Kembali ke Lokerku Ads
        </a>
        <a href="{{ route('perusahaan.ads.status', ['transaksi' => $transaksi, 'cek' => 1]) }}" class="inline-flex items-center gap-1.5 rounded-full bg-[#007AFF] px-4 py-2 text-xs font-bold !text-white shadow-sm transition hover:bg-[#0062CC]">
            Cek Status Pembayaran
        </a>
    </div>

    @if ($pesanStatus)
        <div class="rounded-3xl border-[1.5px] border-emerald-300 bg-emerald-50/90 px-5 py-4 text-sm font-semibold text-emerald-800 backdrop-blur-xl">{{ $pesanStatus }}</div>
    @endif

    <div class="grid grid-cols-1 gap-5 lg:grid-cols-12">
        <section class="rounded-3xl border-[1.5px] border-slate-200/90 bg-white/85 p-6 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.08)] backdrop-blur-xl lg:col-span-7">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200/70 pb-4">
                <div>
                    <span class="text-[11px] font-bold tracking-wider text-slate-400 uppercase">{{ $transaksi->reference_id }}</span>
                    <h1 class="mt-0.5 !text-lg font-extrabold text-slate-900">{{ $transaksi->lowongan?->judul ?? 'Iklan Lowongan' }}</h1>
                    <p class="text-xs text-slate-500">Kanal: <strong>{{ $transaksi->channel_name ?? $transaksi->channel_code }}</strong> &middot; Durasi {{ $transaksi->durasi_hari }} Hari</p>
                </div>
                <span @class([
                    'rounded-full px-3.5 py-1.5 text-xs font-bold uppercase',
                    'bg-emerald-100 text-emerald-700' => $transaksi->status === 'paid',
                    'bg-amber-100 text-amber-800' => $transaksi->status === 'pending',
                    'bg-rose-100 text-rose-700' => in_array($transaksi->status, ['expired', 'cancelled', 'failed'], true),
                ])>{{ $transaksi->status }}</span>
            </div>

            <div class="mt-5 space-y-4">
                @if ($transaksi->status === 'paid')
                    <div class="rounded-2xl border border-emerald-200 bg-emerald-50/80 p-5 text-center">
                        <p class="text-base font-extrabold text-emerald-800">Pembayaran Lunas!</p>
                        <p class="mt-1 text-xs text-emerald-700">Lowongan Anda aktif di posisi teratas hingga <strong>{{ $transaksi->lowongan?->iklan_sampai?->format('d M Y H:i') ?? '-' }}</strong>.</p>
                    </div>
                @else
                    @if (! empty($info['qr_url']))
                        <div class="flex flex-col items-center rounded-2xl border border-slate-200 bg-white p-5 text-center">
                            <p class="text-xs font-bold text-slate-700">Pindai QRIS untuk Membayar</p>
                            <img src="{{ $info['qr_url'] }}" alt="QRIS Paymenku" class="mt-3 size-52 rounded-xl border border-slate-200 object-contain p-2">
                            @if (! empty($info['merchant_name']))
                                <p class="mt-2 text-xs font-semibold text-slate-600">Merchant: {{ $info['merchant_name'] }}</p>
                            @endif
                        </div>
                    @endif

                    @if (! empty($info['va_number']))
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                            <p class="text-xs font-bold text-slate-500 uppercase">Nomor Virtual Account ({{ $info['bank'] ?? $transaksi->channel_name }})</p>
                            <p class="mt-1.5 text-2xl font-extrabold tracking-wider text-[#007AFF] tabular-nums">{{ $info['va_number'] }}</p>
                        </div>
                    @endif

                    @if (! empty($info['payment_code']))
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                            <p class="text-xs font-bold text-slate-500 uppercase">Kode Pembayaran {{ $info['store'] ?? $transaksi->channel_name }}</p>
                            <p class="mt-1.5 text-2xl font-extrabold tracking-wider text-[#007AFF] tabular-nums">{{ $info['payment_code'] }}</p>
                        </div>
                    @endif

                    <div class="flex flex-wrap gap-2.5 pt-2">
                        @if ($transaksi->pay_url)
                            <a href="{{ $transaksi->pay_url }}" target="_blank" rel="noopener noreferrer" class="inline-flex min-h-10 items-center justify-center rounded-full bg-[#007AFF] px-5 text-xs font-bold !text-white shadow-sm transition hover:bg-[#0062CC]">Buka Halaman Bayar Paymenku</a>
                        @endif
                        @if (! empty($info['checkout_url']))
                            <a href="{{ $info['checkout_url'] }}" target="_blank" rel="noopener noreferrer" class="inline-flex min-h-10 items-center justify-center rounded-full bg-[#007AFF] px-5 text-xs font-bold !text-white shadow-sm transition hover:bg-[#0062CC]">Lanjutkan ke E-Wallet</a>
                        @endif
                        @if ($transaksi->simulator_url)
                            <a href="{{ $transaksi->simulator_url }}" target="_blank" rel="noopener noreferrer" class="inline-flex min-h-10 items-center justify-center rounded-full border border-amber-300 bg-amber-50 px-5 text-xs font-bold text-amber-800 transition hover:bg-amber-100">Simulasi Bayar (Sandbox)</a>
                        @endif
                    </div>
                @endif
            </div>
        </section>

        <section class="rounded-3xl border-[1.5px] border-slate-200/90 bg-white/85 p-6 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.08)] backdrop-blur-xl lg:col-span-5">
            <h2 class="text-sm font-bold text-slate-900">Rincian Transaksi</h2>
            <dl class="mt-4 space-y-3 text-xs">
                <div class="flex justify-between border-b border-slate-100 pb-2">
                    <dt class="text-slate-500">ID Paymenku</dt>
                    <dd class="font-bold text-slate-800">{{ $transaksi->trx_id ?? '-' }}</dd>
                </div>
                <div class="flex justify-between border-b border-slate-100 pb-2">
                    <dt class="text-slate-500">Nominal Tagihan</dt>
                    <dd class="font-extrabold text-[#007AFF] tabular-nums">Rp {{ number_format((float) ($transaksi->amount_bayar ?? $transaksi->amount_dasar), 0, ',', '.') }}</dd>
                </div>
                <div class="flex justify-between border-b border-slate-100 pb-2">
                    <dt class="text-slate-500">Mode</dt>
                    <dd class="font-bold text-slate-800">{{ $transaksi->is_sandbox ? 'Sandbox (Uji Coba)' : 'Produksi (Live)' }}</dd>
                </div>
                @if (! empty($info['expiration_date']))
                    <div class="flex justify-between">
                        <dt class="text-slate-500">Kedaluwarsa</dt>
                        <dd class="font-bold text-slate-800">{{ \Illuminate\Support\Carbon::parse($info['expiration_date'])->format('d M Y H:i') }}</dd>
                    </div>
                @endif
            </dl>
        </section>
    </div>

    @if ($transaksi->status === 'pending')
        <script>
            (function () {
                const statusUrl = @json(route('perusahaan.ads.status', ['transaksi' => $transaksi, 'cek' => 1, 'json' => 1]));
                let sedangMengecek = false;

                async function periksaStatus() {
                    if (sedangMengecek) {
                        return;
                    }
                    sedangMengecek = true;
                    try {
                        const res = await fetch(statusUrl, {
                            headers: { 'Accept': 'application/json' },
                            credentials: 'same-origin',
                        });
                        if (! res.ok) {
                            return;
                        }
                        const data = await res.json();
                        if (data.paid || (data.status && data.status !== 'pending')) {
                            window.location.reload();
                        }
                    } catch (e) {
                        // Abaikan error jaringan sementara; polling berikutnya akan mencoba lagi.
                    } finally {
                        sedangMengecek = false;
                    }
                }

                const intervalId = window.setInterval(periksaStatus, 3500);
                document.addEventListener('visibilitychange', function () {
                    if (document.visibilityState === 'visible') {
                        periksaStatus();
                    }
                });
                window.addEventListener('beforeunload', function () {
                    window.clearInterval(intervalId);
                });
            })();
        </script>
    @endif
</x-panel-perusahaan>
