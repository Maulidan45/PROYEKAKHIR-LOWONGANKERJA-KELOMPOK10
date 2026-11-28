<?php

namespace App\Http\Controllers;

use App\Models\User;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AkunController extends Controller
{
    public function edit(Request $permintaan): View
    {
        if ($permintaan->user()->role === 'user') {
            return view('akun.pencari-kerja', ['pengguna' => $permintaan->user()]);
        }

        return view('akun.edit', ['pengguna' => $this->perusahaan($permintaan)]);
    }

    public function perbaruiProfil(Request $permintaan): RedirectResponse
    {
        if ($permintaan->user()->role === 'user') {
            $pengguna = $permintaan->user();
            $data = $permintaan->validate([
                'name' => ['required', 'string', 'max:255', 'not_regex:/\x00/'],
                'telepon' => ['nullable', 'string', 'max:25', 'regex:/\A\+?[0-9][0-9 ()-]{7,24}\z/'],
                'tempat_lahir' => ['nullable', 'string', 'max:100', 'not_regex:/\x00/'],
                'tanggal_lahir' => ['nullable', 'date', 'before:today'],
                'jenis_kelamin' => ['nullable', Rule::in(['Laki-laki', 'Perempuan'])],
                'domisili' => ['nullable', 'string', 'max:255', 'not_regex:/\x00/'],
                'alamat_lengkap' => ['nullable', 'string', 'max:500', 'not_regex:/\x00/'],
                'pendidikan_terakhir' => ['nullable', Rule::in(['SD', 'SMP', 'SMA/SMK', 'Diploma', 'S1', 'S2', 'S3'])],
                'institusi_pendidikan' => ['nullable', 'string', 'max:255', 'not_regex:/\x00/'],
                'jurusan' => ['nullable', 'string', 'max:150', 'not_regex:/\x00/'],
                'tahun_lulus' => ['nullable', 'integer', 'min:1960', 'max:'.((int) date('Y') + 5)],
                'pengalaman_kerja' => ['nullable', 'string', 'max:2000', 'not_regex:/\x00/'],
                'keahlian' => ['nullable', 'string', 'max:1000', 'not_regex:/\x00/'],
                'ringkasan_pribadi' => ['nullable', 'string', 'max:1500', 'not_regex:/\x00/'],
                'portofolio_url' => ['nullable', 'string', 'max:255', 'url:http,https'],
                'ekspektasi_gaji' => ['nullable', 'string', 'max:100', 'not_regex:/\x00/'],
                'foto_profil' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
                'cv_default' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
                'surat_lamaran_default' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
                'ijazah_default' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            ], $this->pesanValidasi(), [
                'name' => 'Nama lengkap',
                'telepon' => 'Nomor telepon',
                'tempat_lahir' => 'Tempat lahir',
                'tanggal_lahir' => 'Tanggal lahir',
                'jenis_kelamin' => 'Jenis kelamin',
                'domisili' => 'Domisili',
                'alamat_lengkap' => 'Alamat lengkap',
                'pendidikan_terakhir' => 'Pendidikan terakhir',
                'institusi_pendidikan' => 'Institusi pendidikan',
                'jurusan' => 'Jurusan',
                'tahun_lulus' => 'Tahun lulus',
                'pengalaman_kerja' => 'Pengalaman kerja',
                'keahlian' => 'Keahlian',
                'ringkasan_pribadi' => 'Ringkasan diri',
                'portofolio_url' => 'Tautan portofolio',
                'ekspektasi_gaji' => 'Ekspektasi gaji',
                'foto_profil' => 'Foto profil',
                'cv_default' => 'CV (PDF)',
                'surat_lamaran_default' => 'Surat lamaran (PDF)',
                'ijazah_default' => 'Ijazah terakhir',
            ]);

            $pengguna->fill(['name' => $data['name']]);
            if ($permintaan->hasFile('foto_profil')) {
                if ($pengguna->foto_profil_path) {
                    Storage::disk('public')->delete($pengguna->foto_profil_path);
                }
                $pengguna->foto_profil_path = $permintaan->file('foto_profil')->store('foto-profil', 'public');
            }

            $profilLama = $pengguna->profil_pendaftaran ?? [];
            $profilBaru = array_merge($profilLama, Arr::except($data, ['name', 'foto_profil', 'cv_default', 'surat_lamaran_default', 'ijazah_default']));

            foreach (['cv_default' => 'cv_default_path', 'surat_lamaran_default' => 'surat_lamaran_default_path', 'ijazah_default' => 'ijazah_default_path'] as $inputBerkas => $kunciPath) {
                if ($permintaan->hasFile($inputBerkas)) {
                    if (! empty($profilLama[$kunciPath])) {
                        Storage::disk('local')->delete($profilLama[$kunciPath]);
                    }
                    $profilBaru[$kunciPath] = $permintaan->file($inputBerkas)->store("profil-dokumen/{$pengguna->id}", 'local');
                }
            }

            $pengguna->profil_pendaftaran = $profilBaru;
            $pengguna->save();

            return redirect()->route('akun.edit')->with('status', 'Profil pribadi berhasil diperbarui.');
        }

        $pengguna = $this->perusahaan($permintaan);
        $data = $permintaan->validate([
            'name' => ['required', 'string', 'max:255', 'not_regex:/\x00/'],
            'bentuk_badan_usaha' => ['required', 'string', Rule::in(['PT', 'CV', 'Firma', 'Koperasi', 'Yayasan', 'UMKM / Perseorangan'])],
            'tahun_berdiri' => ['required', 'integer', 'min:1900', 'max:'.date('Y')],
            'nib_npwp' => ['nullable', 'string', 'min:10', 'max:50', 'regex:/^[0-9A-Za-z.\-\/\s]+$/'],
            'telepon' => ['required', 'string', 'max:25', 'regex:/\A\+?[0-9][0-9 ()-]{7,24}\z/'],
            'email_hrd' => ['required', 'string', 'email', 'max:255'],
            'bidang_usaha' => ['required', 'string', 'max:100', 'not_regex:/\x00/'],
            'jumlah_karyawan' => ['required', 'string', Rule::in(['1–10', '11–50', '51–200', '201–500', '501+'])],
            'provinsi_perusahaan' => ['required', 'string', 'max:100', 'not_regex:/\x00/'],
            'kota_perusahaan' => ['required', 'string', 'max:100', 'not_regex:/\x00/'],
            'kode_pos' => ['required', 'string', 'regex:/^\d{5}$/'],
            'alamat_perusahaan' => ['required', 'string', 'max:500', 'not_regex:/\x00/'],
            'deskripsi_perusahaan' => ['required', 'string', 'min:30', 'max:2000', 'not_regex:/\x00/'],
            'visi_misi' => ['required', 'string', 'min:20', 'max:1500', 'not_regex:/\x00/'],
            'keunggulan_perusahaan' => ['nullable', 'string', 'max:1000', 'not_regex:/\x00/'],
            'situs_perusahaan' => ['nullable', 'string', 'max:255', 'url:http,https'],
            'linkedin_url' => ['nullable', 'string', 'max:255', 'url:http,https'],
            'nama_penanggung_jawab' => ['required', 'string', 'max:255', 'not_regex:/\x00/'],
            'jabatan_penanggung_jawab' => ['required', 'string', 'max:100', 'not_regex:/\x00/'],
            'foto_profil' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'dokumen_legalitas' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ], $this->pesanValidasi(), $this->atributProfil());

        $pengguna->fill(Arr::only($data, ['name']));
        $pengguna->profil_pendaftaran = array_merge(
            $pengguna->profil_pendaftaran ?? [],
            Arr::except($data, ['name', 'foto_profil', 'dokumen_legalitas'])
        );

        if ($permintaan->hasFile('foto_profil')) {
            if ($pengguna->foto_profil_path) {
                Storage::disk('public')->delete($pengguna->foto_profil_path);
            }
            $pengguna->foto_profil_path = $permintaan->file('foto_profil')->store('foto-profil', 'public');
        }

        if ($permintaan->hasFile('dokumen_legalitas')) {
            if ($pengguna->dokumen_legalitas_path) {
                Storage::disk('local')->delete($pengguna->dokumen_legalitas_path);
            }
            $pengguna->dokumen_legalitas_path = $permintaan->file('dokumen_legalitas')->store('legalitas-perusahaan', 'local');
            if ($pengguna->status_verifikasi === 'Ditolak') {
                $pengguna->status_verifikasi = 'Menunggu Verifikasi';
                $pengguna->catatan_verifikasi = null;
            }
        }

        $pengguna->save();

        return redirect()->route('akun.edit')->with('status', 'Profil perusahaan berhasil diperbarui.');
    }

    public function ajukanVerifikasi(Request $permintaan): RedirectResponse
    {
        $pengguna = $this->perusahaan($permintaan);

        if ($pengguna->status_verifikasi === 'Terverifikasi') {
            return redirect()->route('akun.edit')->with('status', 'Akun perusahaan Anda sudah terverifikasi.');
        }

        $valid = $permintaan->validate([
            'nib_npwp' => ['required', 'string', 'min:10', 'max:50', 'regex:/^[0-9A-Za-z.\-\/\s]+$/'],
            'dokumen_legalitas' => [$pengguna->dokumen_legalitas_path ? 'nullable' : 'required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'pernyataan_kebenaran' => ['accepted'],
        ], [
            'nib_npwp.required' => 'Nomor NIB atau NPWP perusahaan wajib diisi.',
            'nib_npwp.min' => 'Nomor NIB atau NPWP minimal :min karakter.',
            'nib_npwp.regex' => 'Format Nomor NIB / NPWP tidak valid.',
            'dokumen_legalitas.required' => 'Dokumen legalitas usaha (NIB / SIUP / Akta) wajib diunggah.',
            'pernyataan_kebenaran.accepted' => 'Anda wajib menyetujui pernyataan kebenaran data dan legalitas perusahaan.',
        ]);

        $profil = $pengguna->profil_pendaftaran ?? [];
        $kurang = [];

        foreach ([
            'bentuk_badan_usaha' => 'Bentuk badan usaha',
            'tahun_berdiri' => 'Tahun berdiri',
            'telepon' => 'Nomor telepon',
            'email_hrd' => 'Email HRD resmi',
            'bidang_usaha' => 'Bidang usaha',
            'jumlah_karyawan' => 'Jumlah karyawan',
            'provinsi_perusahaan' => 'Provinsi perusahaan',
            'kota_perusahaan' => 'Kota perusahaan',
            'kode_pos' => 'Kode pos',
            'alamat_perusahaan' => 'Alamat perusahaan',
            'deskripsi_perusahaan' => 'Deskripsi perusahaan',
            'visi_misi' => 'Visi & misi perusahaan',
            'nama_penanggung_jawab' => 'Nama penanggung jawab',
            'jabatan_penanggung_jawab' => 'Jabatan penanggung jawab',
        ] as $kunci => $label) {
            if (blank($profil[$kunci] ?? null)) {
                $kurang[] = $label;
            }
        }

        if (mb_strlen((string) ($profil['deskripsi_perusahaan'] ?? '')) < 30) {
            $kurang[] = 'Deskripsi perusahaan minimal 30 karakter';
        }

        if (blank($pengguna->foto_profil_path)) {
            $kurang[] = 'Logo / foto profil perusahaan';
        }

        if ($kurang !== []) {
            return back()
                ->withInput()
                ->withErrors(['verifikasi' => 'Lengkapi terlebih dahulu persyaratan berikut di menu Profil Perusahaan: '.implode(', ', $kurang).'.']);
        }

        if ($permintaan->hasFile('dokumen_legalitas')) {
            if ($pengguna->dokumen_legalitas_path) {
                Storage::disk('local')->delete($pengguna->dokumen_legalitas_path);
            }
            $pengguna->dokumen_legalitas_path = $permintaan->file('dokumen_legalitas')->store('legalitas-perusahaan', 'local');
        }

        $profil['nib_npwp'] = trim($valid['nib_npwp']);
        $pengguna->profil_pendaftaran = $profil;
        $pengguna->status_verifikasi = 'Menunggu Verifikasi';
        $pengguna->catatan_verifikasi = null;
        $pengguna->save();
        Cache::forget('admin:menunggu_verifikasi_count');

        return redirect()->route('akun.edit')->with('status', 'Pengajuan verifikasi perusahaan berhasil dikirim ke Admin. Silakan tunggu peninjauan.');
    }

    public function perbaruiEmail(Request $permintaan): RedirectResponse
    {
        $pengguna = $this->akunPengguna($permintaan);
        $data = $permintaan->validate([
            'email' => ['bail', 'required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($pengguna)],
            'current_password_email' => ['bail', 'required', 'string', 'max:72', 'not_regex:/\x00/', 'current_password'],
        ], $this->pesanValidasi(), ['email' => 'Email', 'current_password_email' => 'Kata sandi saat ini']);

        $pengguna->update(Arr::only($data, ['email']));

        return redirect()->route('akun.edit')->with('status', 'Email berhasil diperbarui.');
    }

    public function perbaruiKataSandi(Request $permintaan): RedirectResponse
    {
        $pengguna = $this->akunPengguna($permintaan);
        $data = $permintaan->validate([
            'current_password_password' => ['bail', 'required', 'string', 'max:72', 'not_regex:/\x00/', 'current_password'],
            'password' => ['bail', 'required', 'string', 'min:12', 'max:72', 'confirmed', function (string $atribut, mixed $nilai, Closure $gagal): void {
                if (strlen($nilai) > 72 || str_contains($nilai, "\0")) {
                    $gagal('Kata sandi maksimal 72 byte dan tidak boleh mengandung karakter null.');
                }
            }],
        ], $this->pesanValidasi(), ['current_password_password' => 'Kata sandi saat ini', 'password' => 'Kata sandi']);

        $pengguna->update(Arr::only($data, ['password']));
        $permintaan->session()->regenerate();

        return redirect()->route('akun.edit')->with('status', 'Kata sandi berhasil diperbarui.');
    }

    /** @return array<string, string> */
    private function pesanValidasi(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'string' => ':attribute harus berupa teks.',
            'max' => ':attribute maksimal :max karakter.',
            'max.file' => 'Ukuran :attribute maksimal 5 MB.',
            'min' => ':attribute minimal :min karakter.',
            'in' => 'Pilih :attribute yang tersedia.',
            'mimes' => 'Format :attribute harus berupa :values.',
            'before' => ':attribute harus sebelum hari ini.',
            'not_regex' => ':attribute tidak boleh mengandung karakter null.',
            'email.email' => 'Masukkan alamat email yang valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'current_password' => 'Kata sandi saat ini tidak cocok.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'telepon.regex' => 'Masukkan nomor telepon yang valid (8–25 karakter).',
            'situs_perusahaan.url' => 'Situs perusahaan harus berupa alamat HTTP atau HTTPS yang valid.',
        ];
    }

    /** @return array<string, string> */
    private function atributProfil(): array
    {
        return [
            'name' => 'Nama perusahaan', 'telepon' => 'Nomor telepon', 'bidang_usaha' => 'Bidang usaha',
            'jumlah_karyawan' => 'Jumlah karyawan', 'alamat_perusahaan' => 'Alamat perusahaan',
            'kota_perusahaan' => 'Kota perusahaan', 'deskripsi_perusahaan' => 'Deskripsi perusahaan',
            'situs_perusahaan' => 'Situs perusahaan', 'nama_penanggung_jawab' => 'Nama penanggung jawab',
            'jabatan_penanggung_jawab' => 'Jabatan penanggung jawab', 'dokumen_legalitas' => 'Dokumen legalitas',
        ];
    }

    private function perusahaan(Request $permintaan): User
    {
        $pengguna = $permintaan->user();

        abort_unless($pengguna instanceof User && $pengguna->role === 'perusahaan', 403);

        return $pengguna;
    }

    private function akunPengguna(Request $permintaan): User
    {
        $pengguna = $permintaan->user();

        abort_unless($pengguna instanceof User && in_array($pengguna->role, ['user', 'perusahaan'], true), 403);

        return $pengguna;
    }
}
