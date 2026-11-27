<?php

namespace App\Http\Controllers;

use App\Http\Requests\PermintaanPendaftaran;
use App\Models\Kategori;
use App\Models\Lowongan;
use App\Models\User;
use App\Services\WilayahService;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AutentikasiController extends Controller
{
    public function daftar(PermintaanPendaftaran $permintaan): RedirectResponse
    {
        $data = $permintaan->validated();
        $pengguna = new User;
        $pengguna->fill(Arr::only($data, ['name', 'email', 'password']));
        $pengguna->role = $data['role'];
        $pengguna->status_verifikasi = $data['role'] === 'perusahaan' ? 'Menunggu Verifikasi' : 'Terverifikasi';
        $pengguna->profil_pendaftaran = Arr::except($data, ['name', 'email', 'password', 'role']);
        try {
            $pengguna->save();
        } catch (UniqueConstraintViolationException) {
            throw ValidationException::withMessages(['email' => 'Email sudah terdaftar. Silakan masuk.']);
        }

        Auth::login($pengguna);
        $permintaan->session()->regenerate();

        return redirect()->route('dashboard');
    }

    public function masuk(Request $permintaan): RedirectResponse
    {
        if (is_string($permintaan->input('email'))) {
            $permintaan->merge(['email' => strtolower(trim($permintaan->input('email')))]);
        }

        $kredensial = $permintaan->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'max:1000'],
        ], ['required' => ':attribute wajib diisi.', 'string' => ':attribute harus berupa teks.', 'max.string' => ':attribute maksimal :max karakter.', 'email.email' => 'Masukkan alamat email yang valid.'],
            ['email' => 'Email', 'password' => 'Kata sandi']);

        if (strlen($kredensial['password']) > 72 || str_contains($kredensial['password'], "\0") || ! Auth::attempt($kredensial)) {
            throw ValidationException::withMessages(['email' => 'Email atau kata sandi tidak cocok.']);
        }

        $permintaan->session()->regenerate();

        return redirect()->route('dashboard');
    }

    /**
     * @return array<string, mixed>
     */
    public static function dataKatalog(Request $permintaan, WilayahService $wilayah): array
    {
        $penyaring = $permintaan->validate([
            'kata_kunci' => ['nullable', 'string', 'max:150', 'not_regex:/<\s*script\b/i'],
            'kategori' => ['nullable', 'integer', 'exists:kategoris,id'],
            'tipe' => ['nullable', 'in:Penuh waktu,Paruh waktu,Magang,Jarak jauh'],
            'provinsi' => ['nullable', 'string', 'max:10', 'regex:/^\d+$/'],
            'lokasi' => ['nullable', 'string', 'max:100'],
            'gaji_min' => ['nullable', 'integer', 'min:0', 'max:1000000000'],
            'urutkan' => ['nullable', 'in:terbaru,gaji_tertinggi,batas_terdekat'],
        ]);

        $kueri = Lowongan::terbuka()
            ->with(['kategori', 'perusahaan'])
            ->withCount('lamarans')
            ->saring($penyaring, $wilayah);

        $provinsiId = (string) ($penyaring['provinsi'] ?? '');
        $lowonganTersimpanIds = $permintaan->user()?->role === 'user'
            ? $permintaan->user()->lowonganTersimpan()->pluck('lowongan_id')->all()
            : [];

        return [
            'daftarLowongan' => $kueri->paginate(12)->withQueryString(),
            'daftarKategori' => Kategori::orderBy('nama')->get(),
            'daftarProvinsi' => $wilayah->daftarProvinsi(),
            'daftarKabupaten' => $provinsiId !== '' ? $wilayah->daftarKabupaten($provinsiId) : [],
            'daftarLokasi' => Lowongan::terbuka()->distinct()->orderBy('lokasi')->pluck('lokasi'),
            'lowonganTersimpanIds' => $lowonganTersimpanIds,
            'cari' => $penyaring['kata_kunci'] ?? '',
            'kategori' => (string) ($penyaring['kategori'] ?? ''),
            'tipe' => $penyaring['tipe'] ?? '',
            'provinsi' => $provinsiId,
            'lokasi' => $penyaring['lokasi'] ?? '',
            'gajiMin' => (string) ($penyaring['gaji_min'] ?? ''),
            'urutkan' => (string) ($penyaring['urutkan'] ?? 'terbaru'),
        ];
    }

    public function keluar(Request $permintaan): RedirectResponse
    {
        Auth::logout();
        $permintaan->session()->invalidate();
        $permintaan->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Kamu sudah keluar dari akun.');
    }
}
