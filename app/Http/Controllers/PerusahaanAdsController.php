<?php

namespace App\Http\Controllers;

use App\Models\Lowongan;
use App\Models\TransaksiAd;
use App\Services\PaymenkuService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class PerusahaanAdsController extends Controller
{
    public const array PAKET_IKLAN = [
        '3_hari' => [
            'hari' => 3,
            'harga' => 25000,
            'nama' => 'Starter Boost',
            'per_hari' => 'Rp 8.333 / hari',
            'fitur' => 'Tampil paling atas & badge Rekomendasi',
        ],
        '7_hari' => [
            'hari' => 7,
            'harga' => 50000,
            'nama' => 'Populer',
            'per_hari' => 'Rp 7.143 / hari',
            'fitur' => 'Prioritas teratas + Rekomendasi pencari kerja',
        ],
        '14_hari' => [
            'hari' => 14,
            'harga' => 90000,
            'nama' => 'Hemat 10%',
            'per_hari' => 'Rp 6.429 / hari',
            'fitur' => 'Eksposur 2 minggu + Rekomendasi utama',
        ],
        '30_hari' => [
            'hari' => 30,
            'harga' => 150000,
            'nama' => 'Maksimal',
            'per_hari' => 'Rp 5.000 / hari',
            'fitur' => 'Dominasi posisi #1 sebulan penuh',
        ],
    ];

    public const array BUDGET_BOOST = [
        0 => 'Standar (Rp 0)',
        25000 => 'Boost +Rp 25.000',
        50000 => 'Ekstra +Rp 50.000',
        100000 => 'Maksimal +Rp 100.000',
    ];

    public static function ekstrakHargaDariLabel(?string $label): int
    {
        if (! is_string($label) || $label === '') {
            return 0;
        }

        if (preg_match('/Rp\s*([0-9\.]+)/i', $label, $cocok)) {
            return (int) str_replace('.', '', $cocok[1]);
        }

        if (str_contains($label, '30')) {
            return 150000;
        }
        if (str_contains($label, '14')) {
            return 90000;
        }
        if (str_contains($label, '3 Hari')) {
            return 25000;
        }

        return 50000;
    }

    public function index(Request $permintaan, PaymenkuService $paymenku): View
    {
        $pengguna = $permintaan->user();
        abort_unless($pengguna->role === 'perusahaan', 403);

        $lowonganAktif = $pengguna->lowongans()
            ->terbuka()
            ->with('kategori:id,nama')
            ->latest()
            ->get();

        $riwayatIklan = $pengguna->lowongans()
            ->where(function ($q): void {
                $q->whereNotNull('iklan_sampai')->orWhereNotNull('iklan_paket');
            })
            ->with('kategori:id,nama')
            ->withCount('lamarans')
            ->orderByDesc('iklan_sampai')
            ->get();

        $riwayatTransaksi = TransaksiAd::where('perusahaan_id', $pengguna->id)
            ->with('lowongan:id,judul')
            ->latest('id')
            ->limit(10)
            ->get();

        $kanalInfo = $paymenku->daftarKanalAktif();
        $iklanAktifCount = $riwayatIklan->filter(fn (Lowongan $l): bool => $l->sedangDiiklankan())->count();
        $totalInvestasi = $riwayatIklan->sum(fn (Lowongan $l): int => self::ekstrakHargaDariLabel($l->iklan_paket));
        $totalPelamarIklan = $riwayatIklan->sum('lamarans_count');

        return view('perusahaan.ads', [
            'pengguna' => $pengguna,
            'lowonganAktif' => $lowonganAktif,
            'riwayatIklan' => $riwayatIklan,
            'riwayatTransaksi' => $riwayatTransaksi,
            'paketIklan' => self::PAKET_IKLAN,
            'budgetBoost' => self::BUDGET_BOOST,
            'iklanAktifCount' => $iklanAktifCount,
            'totalInvestasi' => $totalInvestasi,
            'totalPelamarIklan' => $totalPelamarIklan,
            'paymenkuConfigured' => $kanalInfo['configured'],
            'kanalPembayaran' => $kanalInfo['channels'],
            'paymenkuError' => $kanalInfo['error'],
        ]);
    }

    public function store(Request $permintaan, PaymenkuService $paymenku): RedirectResponse
    {
        $pengguna = $permintaan->user();
        abort_unless($pengguna->role === 'perusahaan', 403);

        if (! $paymenku->sudahDikonfigurasi()) {
            return back()->withErrors(['paymenku' => 'PAYMENKU_API_KEY belum dipasang di file .env. Pasang API key Paymenku terlebih dahulu.']);
        }

        $valid = $permintaan->validate([
            'lowongan_id' => ['required', 'integer'],
            'paket' => ['required', Rule::in(array_keys(self::PAKET_IKLAN))],
            'budget_tambahan' => ['nullable', 'integer', Rule::in(array_keys(self::BUDGET_BOOST))],
            'channel_code' => ['required', 'string', 'max:50'],
            'customer_phone' => ['nullable', 'string', 'max:25'],
        ]);

        if (strtolower($valid['channel_code']) === 'ovo' && blank($valid['customer_phone'] ?? null)) {
            return back()->withInput()->withErrors(['customer_phone' => 'Nomor HP wajib diisi untuk pembayaran melalui OVO.']);
        }

        $lowongan = Lowongan::where('id', $valid['lowongan_id'])
            ->where('perusahaan_id', $pengguna->id)
            ->firstOrFail();

        $kanalInfo = $paymenku->daftarKanalAktif();
        $kanalDipilih = collect($kanalInfo['channels'])->firstWhere('code', $valid['channel_code']);

        if (! $kanalDipilih) {
            return back()->withInput()->withErrors(['channel_code' => 'Kanal pembayaran tidak aktif atau tidak ditemukan di merchant Paymenku Anda.']);
        }

        $paketDetail = self::PAKET_IKLAN[$valid['paket']];
        $budgetTambahan = (int) ($valid['budget_tambahan'] ?? 0);
        $totalDasar = $paketDetail['harga'] + $budgetTambahan;

        $minAmount = (int) ($kanalDipilih['min_amount'] ?? 1000);
        $maxAmount = (int) ($kanalDipilih['max_amount'] ?? 250000000);
        if ($totalDasar < $minAmount || $totalDasar > $maxAmount) {
            return back()->withInput()->withErrors([
                'channel_code' => 'Nominal Rp '.number_format($totalDasar, 0, ',', '.')." berada di luar batas kanal {$kanalDipilih['name']} (Rp ".number_format($minAmount, 0, ',', '.').' – Rp '.number_format($maxAmount, 0, ',', '.').').',
            ]);
        }

        $referenceId = 'ADS-'.now()->format('YmdHis').'-'.Str::upper(Str::random(5));

        $transaksi = TransaksiAd::create([
            'perusahaan_id' => $pengguna->id,
            'lowongan_id' => $lowongan->id,
            'reference_id' => $referenceId,
            'idempotency_key' => $referenceId,
            'paket' => $valid['paket'],
            'durasi_hari' => $paketDetail['hari'],
            'budget_tambahan' => $budgetTambahan,
            'amount_dasar' => $totalDasar,
            'channel_code' => $kanalDipilih['code'],
            'channel_name' => $kanalDipilih['name'] ?? $kanalDipilih['code'],
            'status' => 'pending',
        ]);

        $payload = [
            'channel_code' => $kanalDipilih['code'],
            'amount' => $totalDasar,
            'reference_id' => $referenceId,
            'customer_name' => $pengguna->name,
            'customer_email' => $pengguna->email,
            'return_url' => route('perusahaan.ads.status', $transaksi),
            'order_items' => [
                [
                    'name' => "Iklan Loker {$lowongan->judul} ({$paketDetail['hari']} Hari)",
                    'quantity' => 1,
                ],
            ],
        ];

        if (filled($valid['customer_phone'] ?? null)) {
            $payload['customer_phone'] = trim((string) $valid['customer_phone']);
        }

        try {
            $respons = $paymenku->buatTransaksi($payload, $referenceId);
            $data = $respons['data'] ?? [];
            $paymentInfo = is_array($data['payment_info'] ?? null) ? $data['payment_info'] : [];

            $transaksi->update([
                'trx_id' => $data['trx_id'] ?? null,
                'amount_bayar' => $data['amount'] ?? $totalDasar,
                'pay_url' => $data['pay_url'] ?? null,
                'simulator_url' => $paymentInfo['simulator_url'] ?? null,
                'payment_info' => $paymentInfo,
                'is_sandbox' => (bool) ($respons['sandbox_mode'] ?? ($paymentInfo['sandbox'] ?? str_starts_with((string) ($data['trx_id'] ?? ''), 'SBOX'))),
            ]);
        } catch (Throwable $e) {
            $transaksi->update(['status' => 'failed']);

            return back()->withInput()->withErrors(['paymenku' => $e->getMessage()]);
        }

        return redirect()->route('perusahaan.ads.status', $transaksi);
    }

    public function status(Request $permintaan, TransaksiAd $transaksi, PaymenkuService $paymenku): View|JsonResponse
    {
        $pengguna = $permintaan->user();
        abort_unless($pengguna->role === 'perusahaan' && $transaksi->perusahaan_id === $pengguna->id, 403);

        $pesanStatus = null;

        if (($permintaan->boolean('cek') || $transaksi->status === 'pending') && $paymenku->sudahDikonfigurasi()) {
            try {
                $dataStatus = $paymenku->cekStatus($transaksi->reference_id);
                $statusPaymenku = strtolower((string) ($dataStatus['status'] ?? ''));

                if (in_array($statusPaymenku, ['paid', 'expired', 'cancelled', 'failed', 'refunded'], true)) {
                    $transaksi->aktifkanIklanJikaLunas(
                        $statusPaymenku,
                        is_string($dataStatus['paid_at'] ?? null) ? $dataStatus['paid_at'] : null
                    );
                    $transaksi->refresh();

                    if ($statusPaymenku === 'paid') {
                        $pesanStatus = 'Pembayaran terkonfirmasi lunas! Iklan lowongan Anda sekarang aktif dan tampil paling atas.';
                    }
                }
            } catch (Throwable) {
                // Tetap tampilkan halaman instruksi pembayaran jika pengecekan sementara gagal.
            }
        }

        if ($permintaan->boolean('json')) {
            return response()->json([
                'status' => $transaksi->status,
                'paid' => $transaksi->status === 'paid',
            ]);
        }

        $transaksi->load('lowongan:id,judul,iklan_sampai,iklan_paket');

        return view('perusahaan.ads-status', [
            'transaksi' => $transaksi,
            'pesanStatus' => $pesanStatus,
        ]);
    }

    public function destroy(Request $permintaan, Lowongan $lowongan): RedirectResponse
    {
        abort_unless($permintaan->user()->role === 'perusahaan' && $lowongan->perusahaan_id === $permintaan->user()->id, 403);

        $lowongan->update([
            'iklan_sampai' => null,
            'iklan_paket' => null,
        ]);

        return redirect()->route('perusahaan.ads.index')->with('status', "Iklan untuk lowongan \"{$lowongan->judul}\" telah dihentikan.");
    }
}
