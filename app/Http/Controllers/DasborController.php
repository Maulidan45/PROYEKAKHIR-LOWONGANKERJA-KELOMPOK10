<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Lamaran;
use App\Models\Lowongan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DasborController extends Controller
{
    public function tampil(Request $permintaan, string $role): View
    {
        $permintaan->validate(['page' => ['sometimes', 'integer', 'min:1', 'max:1000000']]);
        abort_unless($permintaan->user()->role === $role, 403);

        $data = ['pengguna' => $permintaan->user(), 'jenisAkun' => $role, 'jenisAkunTersedia' => User::JENIS_AKUN];

        if ($role === 'perusahaan') {
            $lamaranPerusahaan = Lamaran::whereHas('lowongan', fn ($kueri) => $kueri->where('perusahaan_id', $permintaan->user()->id));
            $jumlahLamaranPerStatus = (clone $lamaranPerusahaan)
                ->selectRaw('status, COUNT(*) as jumlah')
                ->groupBy('status')
                ->pluck('jumlah', 'status');
            $data['ringkasan'] = [
                'Lowongan aktif' => $permintaan->user()->lowongans()->terbuka()->count(),
                'Total lamaran' => $jumlahLamaranPerStatus->sum(),
                'Perlu ditinjau' => $jumlahLamaranPerStatus->get('Dikirim', 0),
            ];
            $data['lamaranTerbaru'] = (clone $lamaranPerusahaan)
                ->select(['id', 'lowongan_id', 'pelamar_id', 'status', 'created_at'])
                ->with(['lowongan:id,perusahaan_id,judul', 'pelamar:id,name'])
                ->latest()
                ->limit(5)
                ->get();
        }

        if ($role === 'user') {
            $penyaring = $permintaan->validate([
                'kata_kunci' => ['nullable', 'string', 'max:150', 'not_regex:/<\s*script\b/i'],
                'kategori' => ['nullable', 'integer', 'exists:kategoris,id'],
                'lokasi' => ['nullable', 'string', 'max:100'],
            ]);
            $lowongan = Lowongan::terbuka()->with(['kategori', 'perusahaan']);
            $lowongan->when($penyaring['kata_kunci'] ?? null, function ($kueri, string $cari): void {
                $nilaiCari = '%'.mb_strtolower($cari).'%';
                $kueri->where(function ($bagian) use ($nilaiCari): void {
                    $bagian->whereRaw('lower(judul) like ?', [$nilaiCari])
                        ->orWhereRaw('lower(lokasi) like ?', [$nilaiCari])
                        ->orWhereHas('perusahaan', fn ($perusahaan) => $perusahaan->whereRaw('lower(name) like ?', [$nilaiCari]));
                });
            });
            $lowongan->when($penyaring['kategori'] ?? null, fn ($kueri, $kategori) => $kueri->where('kategori_id', $kategori));
            $lowongan->when($penyaring['lokasi'] ?? null, fn ($kueri, $lokasi) => $kueri->where('lokasi', $lokasi));
            $data['ringkasan'] = ['Lamaran terkirim' => $permintaan->user()->lamarans()->count(), 'Lamaran ditinjau' => $permintaan->user()->lamarans()->where('status', 'Ditinjau')->count()];
            $data['daftarLowongan'] = $lowongan->latest()->paginate(12)->withQueryString();
            $data['daftarKategori'] = Kategori::orderBy('nama')->get(['id', 'nama']);
            $data['daftarLokasi'] = Lowongan::terbuka()->distinct()->orderBy('lokasi')->pluck('lokasi');
            $data['penyaring'] = $penyaring;
        }

        if ($role === 'admin') {
            $data['ringkasan'] = ['Akun terdaftar' => User::count(), 'Lowongan aktif' => Lowongan::terbuka()->count(), 'Kategori' => Kategori::count(), 'Lamaran' => Lamaran::count()];
            $data['daftarAkun'] = User::latest('id')->paginate(10);
            $data['jumlahAkun'] = User::selectRaw('role, count(*) as total')->groupBy('role')->pluck('total', 'role');
        }

        return view('dashboard', $data);
    }
}
