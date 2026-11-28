<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Lowongan;
use App\Models\User;
use App\Notifications\VerifikasiPerusahaanNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminController extends Controller
{
    public function verifikasiIndex(Request $permintaan): View
    {
        abort_unless($permintaan->user()->role === 'admin', 403);

        $statusFilter = $permintaan->query('status');
        $kueri = User::query()
            ->where('role', 'perusahaan')
            ->withCount('lowongans')
            ->latest('id');

        if (is_string($statusFilter) && in_array($statusFilter, User::STATUS_VERIFIKASI, true)) {
            $kueri->where('status_verifikasi', $statusFilter);
        }

        return view('admin.verifikasi', [
            'daftarPerusahaan' => $kueri->paginate(12)->withQueryString(),
            'statusAktif' => $statusFilter,
            'ringkasanVerifikasi' => User::query()
                ->where('role', 'perusahaan')
                ->selectRaw('status_verifikasi, count(*) as total')
                ->groupBy('status_verifikasi')
                ->pluck('total', 'status_verifikasi'),
        ]);
    }

    public function verifikasiUpdate(Request $permintaan, User $perusahaan): RedirectResponse
    {
        abort_unless($permintaan->user()->role === 'admin' && $perusahaan->role === 'perusahaan', 403);

        $valid = $permintaan->validate([
            'status_verifikasi' => ['required', Rule::in(['Terverifikasi', 'Ditolak', 'Menunggu Verifikasi'])],
            'catatan_verifikasi' => ['nullable', 'string', 'max:1000', 'not_regex:/\x00/'],
        ]);

        $perusahaan->status_verifikasi = $valid['status_verifikasi'];
        $perusahaan->catatan_verifikasi = $valid['catatan_verifikasi'] ?? null;
        $perusahaan->diverifikasi_pada = $valid['status_verifikasi'] === 'Terverifikasi' ? now() : null;
        $perusahaan->save();
        Cache::forget('admin:menunggu_verifikasi_count');

        $perusahaan->notify(new VerifikasiPerusahaanNotification($perusahaan));

        return back()->with('status', "Status verifikasi {$perusahaan->name} diperbarui menjadi {$perusahaan->status_verifikasi}.");
    }

    public function unduhLegalitas(Request $permintaan, User $perusahaan): StreamedResponse
    {
        $pengguna = $permintaan->user();
        $berhak = $pengguna->role === 'admin' || $pengguna->id === $perusahaan->id;
        abort_unless($berhak && $perusahaan->role === 'perusahaan', 403);
        abort_unless(
            is_string($perusahaan->dokumen_legalitas_path)
            && $perusahaan->dokumen_legalitas_path !== ''
            && Storage::disk('local')->exists($perusahaan->dokumen_legalitas_path),
            404
        );

        $ekstensi = pathinfo($perusahaan->dokumen_legalitas_path, PATHINFO_EXTENSION) ?: 'pdf';
        $namaUnduhan = 'Legalitas-'.Str::slug($perusahaan->name).'.'.$ekstensi;

        if ($permintaan->boolean('pratinjau')) {
            return Storage::disk('local')->response($perusahaan->dokumen_legalitas_path, $namaUnduhan);
        }

        return Storage::disk('local')->download(
            $perusahaan->dokumen_legalitas_path,
            $namaUnduhan
        );
    }

    public function penggunaIndex(Request $permintaan): View
    {
        abort_unless($permintaan->user()->role === 'admin', 403);

        $peranFilter = $permintaan->query('role');
        $cari = trim((string) $permintaan->query('kata_kunci', ''));

        $kueri = User::query()->latest('id');

        if (is_string($peranFilter) && array_key_exists($peranFilter, User::JENIS_AKUN)) {
            $kueri->where('role', $peranFilter);
        }

        if ($cari !== '') {
            $pola = '%'.mb_strtolower($cari).'%';
            $kueri->where(function ($q) use ($pola): void {
                $q->whereRaw('lower(name) like ?', [$pola])
                    ->orWhereRaw('lower(email) like ?', [$pola]);
            });
        }

        return view('admin.pengguna', [
            'daftarPengguna' => $kueri->paginate(15)->withQueryString(),
            'peranAktif' => $peranFilter,
            'cari' => $cari,
            'jenisAkunTersedia' => User::JENIS_AKUN,
        ]);
    }

    public function penggunaBan(Request $permintaan, User $pengguna): RedirectResponse
    {
        abort_unless($permintaan->user()->role === 'admin', 403);

        if ($pengguna->id === $permintaan->user()->id || $pengguna->role === 'admin') {
            return back()->withErrors(['ban' => 'Akun admin tidak dapat dibanned.']);
        }

        $valid = $permintaan->validate([
            'alasan_ban' => ['nullable', 'string', 'max:500'],
        ]);

        if ($pengguna->is_banned) {
            $pengguna->is_banned = false;
            $pengguna->dibanned_pada = null;
            $pengguna->alasan_ban = null;
            $pengguna->save();

            return back()->with('status', "Akun {$pengguna->name} telah diaktifkan kembali.");
        }

        $pengguna->is_banned = true;
        $pengguna->dibanned_pada = now();
        $pengguna->alasan_ban = $valid['alasan_ban'] ?? 'Melanggar ketentuan platform.';
        $pengguna->save();

        return back()->with('status', "Akun {$pengguna->name} berhasil dibanned.");
    }

    public function katalogIndex(Request $permintaan): View
    {
        abort_unless($permintaan->user()->role === 'admin', 403);

        $cari = trim((string) $permintaan->query('kata_kunci', ''));
        $kategoriId = (string) $permintaan->query('kategori', '');

        $kueri = Lowongan::terbuka()
            ->with(['kategori:id,nama', 'perusahaan:id,name'])
            ->latest();

        if ($cari !== '') {
            $pola = '%'.mb_strtolower($cari).'%';
            $kueri->where(function ($q) use ($pola): void {
                $q->whereRaw('lower(judul) like ?', [$pola])
                    ->orWhereRaw('lower(lokasi) like ?', [$pola])
                    ->orWhereHas('perusahaan', fn ($pq) => $pq->whereRaw('lower(name) like ?', [$pola]));
            });
        }

        if ($kategoriId !== '') {
            $kueri->where('kategori_id', $kategoriId);
        }

        return view('admin.katalog', [
            'daftarLowongan' => $kueri->paginate(9)->withQueryString(),
            'daftarKategori' => Kategori::orderBy('nama')->get(['id', 'nama']),
            'cari' => $cari,
            'kategoriAktif' => $kategoriId,
        ]);
    }

    public function adsIndex(Request $permintaan): View
    {
        abort_unless($permintaan->user()->role === 'admin', 403);

        $statusFilter = (string) $permintaan->query('status', 'semua');
        $cari = trim((string) $permintaan->query('kata_kunci', ''));

        $semuaAds = Lowongan::query()
            ->where(function ($q): void {
                $q->whereNotNull('iklan_sampai')->orWhereNotNull('iklan_paket');
            })
            ->with(['perusahaan:id,name,email', 'kategori:id,nama'])
            ->withCount('lamarans')
            ->get();

        $hargaPaket = static fn (?string $label): int => PerusahaanAdsController::ekstrakHargaDariLabel($label);

        $iklanAktif = $semuaAds->filter(fn (Lowongan $l): bool => $l->sedangDiiklankan());
        $iklanKedaluwarsa = $semuaAds->reject(fn (Lowongan $l): bool => $l->sedangDiiklankan());
        $totalPendapatan = $semuaAds->sum(fn (Lowongan $l): int => $hargaPaket($l->iklan_paket));

        $distribusiPaket = [
            '3 Hari' => $semuaAds->filter(fn (Lowongan $l): bool => str_contains((string) $l->iklan_paket, '3 Hari'))->count(),
            '7 Hari' => $semuaAds->filter(fn (Lowongan $l): bool => str_contains((string) $l->iklan_paket, '7 Hari'))->count(),
            '14 Hari' => $semuaAds->filter(fn (Lowongan $l): bool => str_contains((string) $l->iklan_paket, '14 Hari'))->count(),
            '30 Hari' => $semuaAds->filter(fn (Lowongan $l): bool => str_contains((string) $l->iklan_paket, '30 Hari'))->count(),
        ];

        $topPerusahaan = $semuaAds
            ->groupBy('perusahaan_id')
            ->map(function ($grup) use ($hargaPaket): array {
                $pt = $grup->first()->perusahaan;

                return [
                    'nama' => $pt?->name ?? 'Perusahaan',
                    'total_ads' => $grup->count(),
                    'aktif' => $grup->filter(fn (Lowongan $l): bool => $l->sedangDiiklankan())->count(),
                    'pendapatan' => $grup->sum(fn (Lowongan $l): int => $hargaPaket($l->iklan_paket)),
                ];
            })
            ->sortByDesc('pendapatan')
            ->take(5)
            ->values();

        $kueri = Lowongan::query()
            ->where(function ($q): void {
                $q->whereNotNull('iklan_sampai')->orWhereNotNull('iklan_paket');
            })
            ->with(['perusahaan:id,name,email', 'kategori:id,nama'])
            ->withCount('lamarans')
            ->orderByDesc('iklan_sampai');

        if ($statusFilter === 'aktif') {
            $kueri->whereNotNull('iklan_sampai')->where('iklan_sampai', '>', now());
        } elseif ($statusFilter === 'kedaluwarsa') {
            $kueri->where(function ($q): void {
                $q->whereNull('iklan_sampai')->orWhere('iklan_sampai', '<=', now());
            });
        }

        if ($cari !== '') {
            $pola = '%'.mb_strtolower($cari).'%';
            $kueri->where(function ($q) use ($pola): void {
                $q->whereRaw('lower(judul) like ?', [$pola])
                    ->orWhereHas('perusahaan', fn ($pq) => $pq->whereRaw('lower(name) like ?', [$pola]));
            });
        }

        return view('admin.ads', [
            'daftarAds' => $kueri->paginate(10)->withQueryString(),
            'statusAktif' => $statusFilter,
            'cari' => $cari,
            'statistikAds' => [
                'total' => $semuaAds->count(),
                'aktif' => $iklanAktif->count(),
                'kedaluwarsa' => $iklanKedaluwarsa->count(),
                'pendapatan' => $totalPendapatan,
                'perusahaan' => $semuaAds->pluck('perusahaan_id')->unique()->count(),
            ],
            'distribusiPaket' => $distribusiPaket,
            'topPerusahaan' => $topPerusahaan,
        ]);
    }

    public function adsUpdate(Request $permintaan, Lowongan $lowongan): RedirectResponse
    {
        abort_unless($permintaan->user()->role === 'admin', 403);

        $valid = $permintaan->validate([
            'aksi' => ['required', Rule::in(['perpanjang', 'hentikan'])],
            'paket' => ['nullable', Rule::in(['3_hari', '7_hari', '14_hari', '30_hari'])],
        ]);

        if ($valid['aksi'] === 'hentikan') {
            $lowongan->update([
                'iklan_sampai' => null,
                'iklan_paket' => null,
            ]);

            return back()->with('status', "Iklan untuk lowongan \"{$lowongan->judul}\" telah dihentikan.");
        }

        $paket = $valid['paket'] ?? '7_hari';
        $detail = match ($paket) {
            '3_hari' => ['hari' => 3, 'label' => '3 Hari (Rp 25.000)'],
            '14_hari' => ['hari' => 14, 'label' => '14 Hari (Rp 90.000)'],
            '30_hari' => ['hari' => 30, 'label' => '30 Hari (Rp 150.000)'],
            default => ['hari' => 7, 'label' => '7 Hari (Rp 50.000)'],
        };

        $basis = $lowongan->sedangDiiklankan() ? $lowongan->iklan_sampai : now();
        $lowongan->update([
            'iklan_sampai' => $basis->copy()->addDays($detail['hari']),
            'iklan_paket' => $detail['label'],
        ]);

        return back()->with('status', "Iklan \"{$lowongan->judul}\" diperpanjang {$detail['label']} hingga {$lowongan->iklan_sampai->format('d M Y')}.");
    }
}
