<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Lamaran;
use App\Models\Lowongan;
use App\Models\User;
use App\Services\JobMatchingService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DasborController extends Controller
{
    public function tampil(Request $permintaan, string $role, JobMatchingService $pencocokan): View
    {
        $permintaan->validate(['page' => ['sometimes', 'integer', 'min:1', 'max:1000000']]);
        abort_unless($permintaan->user()->role === $role, 403);

        $pengguna = $permintaan->user();
        $data = ['pengguna' => $pengguna, 'jenisAkun' => $role, 'jenisAkunTersedia' => User::JENIS_AKUN];

        if ($role === 'perusahaan') {
            $lowonganPerusahaan = $pengguna->lowongans();
            $lamaranPerusahaan = Lamaran::whereHas('lowongan', fn ($kueri) => $kueri->where('perusahaan_id', $pengguna->id));
            $jumlahLamaranPerStatus = (clone $lamaranPerusahaan)
                ->selectRaw('status, COUNT(*) as jumlah')
                ->groupBy('status')
                ->pluck('jumlah', 'status');

            $totalLowongan = (clone $lowonganPerusahaan)->count();
            $lowonganAktif = (clone $lowonganPerusahaan)->terbuka()->count();
            $lowonganKedaluwarsa = (clone $lowonganPerusahaan)->where('aktif', true)->whereNotNull('batas_lamaran')->whereDate('batas_lamaran', '<', today())->count();
            $lowonganDitutup = (clone $lowonganPerusahaan)->where('aktif', false)->count();

            $pelamarBaru = $jumlahLamaranPerStatus->get('Lamaran Terkirim', 0);
            $pelamarDitinjau = $jumlahLamaranPerStatus->get('Seleksi Administrasi', 0)
                + $jumlahLamaranPerStatus->get('Seleksi Berkas', 0)
                + $jumlahLamaranPerStatus->get('Wawancara', 0);
            $pelamarDiterima = $jumlahLamaranPerStatus->get('Diterima', 0);

            $data['ringkasan'] = [
                'Lowongan aktif' => $lowonganAktif,
                'Total lamaran' => $jumlahLamaranPerStatus->sum(),
                'Perlu ditinjau' => $pelamarBaru,
            ];
            $data['statistikLengkap'] = [
                'total_lowongan' => $totalLowongan,
                'lowongan_aktif' => $lowonganAktif,
                'lowongan_kedaluwarsa' => $lowonganKedaluwarsa,
                'lowongan_ditutup' => $lowonganDitutup,
                'total_pelamar' => $jumlahLamaranPerStatus->sum(),
                'pelamar_baru' => $pelamarBaru,
                'pelamar_ditinjau' => $pelamarDitinjau,
                'pelamar_diterima' => $pelamarDiterima,
            ];
            $data['lamaranTerbaru'] = (clone $lamaranPerusahaan)
                ->select(['id', 'lowongan_id', 'pelamar_id', 'status', 'created_at'])
                ->with(['lowongan:id,perusahaan_id,judul', 'pelamar:id,name'])
                ->latest()
                ->limit(6)
                ->get();
            $data['jadwalTerdekat'] = $pengguna->jadwalWawancaraPerusahaan()
                ->with(['lamaran.lowongan:id,judul', 'pelamar:id,name'])
                ->whereDate('tanggal', '>=', today())
                ->whereIn('status', ['Dijadwalkan', 'Diubah'])
                ->orderBy('tanggal')
                ->orderBy('jam')
                ->limit(4)
                ->get();
            $data['notifikasiTerbaru'] = $pengguna->unreadNotifications()->latest()->limit(5)->get();
            $data['lowonganPerusahaanAktif'] = (clone $lowonganPerusahaan)
                ->terbuka()
                ->orderByDesc('id')
                ->get(['id', 'judul', 'iklan_sampai', 'iklan_paket']);
        }

        if ($role === 'user') {
            $penyaring = $permintaan->validate([
                'kata_kunci' => ['nullable', 'string', 'max:150', 'not_regex:/<\s*script\b/i'],
                'kategori' => ['nullable', 'integer', 'exists:kategoris,id'],
                'lokasi' => ['nullable', 'string', 'max:100'],
                'tipe' => ['nullable', 'in:Penuh waktu,Paruh waktu,Magang,Jarak jauh'],
            ]);

            $kueriLowongan = Lowongan::terbuka()->with(['kategori', 'perusahaan']);
            $kueriLowongan->when($penyaring['kata_kunci'] ?? null, function ($kueri, string $cari): void {
                $nilaiCari = '%'.mb_strtolower($cari).'%';
                $kueri->where(function ($bagian) use ($nilaiCari): void {
                    $bagian->whereRaw('lower(judul) like ?', [$nilaiCari])
                        ->orWhereRaw('lower(lokasi) like ?', [$nilaiCari])
                        ->orWhereHas('perusahaan', fn ($perusahaan) => $perusahaan->whereRaw('lower(name) like ?', [$nilaiCari]));
                });
            });
            $kueriLowongan->when($penyaring['kategori'] ?? null, fn ($kueri, $kategori) => $kueri->where('kategori_id', $kategori));
            $kueriLowongan->when($penyaring['lokasi'] ?? null, fn ($kueri, $lokasi) => $kueri->where('lokasi', $lokasi));
            $kueriLowongan->when($penyaring['tipe'] ?? null, fn ($kueri, $tipe) => $kueri->where('tipe_kerja', $tipe));
            $kueriLowongan->orderByRaw('CASE WHEN iklan_sampai IS NOT NULL AND iklan_sampai >= ? THEN 0 ELSE 1 END', [now()]);

            $lamaranStatusCount = $pengguna->lamarans()
                ->selectRaw('status, count(*) as jumlah')
                ->groupBy('status')
                ->pluck('jumlah', 'status');

            $totalLamaran = $lamaranStatusCount->sum();
            $lamaranDiproses = $lamaranStatusCount->get('Lamaran Terkirim', 0)
                + $lamaranStatusCount->get('Seleksi Administrasi', 0)
                + $lamaranStatusCount->get('Seleksi Berkas', 0)
                + $lamaranStatusCount->get('Wawancara', 0);
            $lamaranDiterima = $lamaranStatusCount->get('Diterima', 0);
            $lamaranDitolak = $lamaranStatusCount->get('Ditolak', 0);
            $jumlahFavorit = $pengguna->lowonganTersimpan()->count();

            $data['ringkasan'] = [
                'Lamaran terkirim' => $totalLamaran,
                'Sedang diproses' => $lamaranDiproses,
                'Diterima' => $lamaranDiterima,
                'Ditolak' => $lamaranDitolak,
                'Lowongan tersimpan' => $jumlahFavorit,
            ];
            $data['kelengkapanProfil'] = $pengguna->persentaseKelengkapanProfil();
            $data['daftarLowongan'] = $kueriLowongan->latest()->paginate(12)->withQueryString();
            $data['rekomendasiSkor'] = $pencocokan->hitungRekomendasi($pengguna, $data['daftarLowongan']->getCollection())
                ->keyBy(fn (array $item): int => $item['lowongan']->id);
            $data['lowonganTersimpan'] = $pengguna->lowonganTersimpan()->with(['kategori:id,nama', 'perusahaan:id,name'])->latest('simpan_lowongans.created_at')->limit(6)->get();
            $data['riwayatAktivitas'] = $pengguna->lamarans()->with(['lowongan:id,perusahaan_id,judul', 'lowongan.perusahaan:id,name'])->latest('updated_at')->limit(5)->get();
            $data['jadwalWawancara'] = $pengguna->jadwalWawancaraPelamar()
                ->with(['lamaran.lowongan:id,judul', 'perusahaan:id,name'])
                ->whereDate('tanggal', '>=', today())
                ->whereIn('status', ['Dijadwalkan', 'Diubah'])
                ->orderBy('tanggal')
                ->orderBy('jam')
                ->limit(3)
                ->get();
            $data['notifikasiTerbaru'] = $pengguna->unreadNotifications()->latest()->limit(5)->get();
            $data['daftarKategori'] = Kategori::orderBy('nama')->get(['id', 'nama']);
            $data['daftarLokasi'] = Lowongan::terbuka()->distinct()->orderBy('lokasi')->pluck('lokasi');
            $data['penyaring'] = $penyaring;
        }

        if ($role === 'admin') {
            $data['ringkasan'] = [
                'Akun terdaftar' => User::count(),
                'Lowongan aktif' => Lowongan::terbuka()->count(),
                'Kategori' => Kategori::count(),
                'Lamaran' => Lamaran::count(),
            ];
            $data['menungguVerifikasi'] = User::where('role', 'perusahaan')->where('status_verifikasi', 'Menunggu Verifikasi')->count();
            $data['iklanAktifCount'] = Lowongan::whereNotNull('iklan_sampai')->where('iklan_sampai', '>', now())->count();
            $data['daftarAkun'] = User::latest('id')->paginate(10);
            $data['jumlahAkun'] = User::selectRaw('role, count(*) as total')->groupBy('role')->pluck('total', 'role');
            $data['lowonganTerbaruAdmin'] = Lowongan::terbuka()->with(['kategori:id,nama', 'perusahaan:id,name'])->latest()->limit(6)->get();
        }

        return view('dashboard', $data);
    }
}
