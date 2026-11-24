<?php

namespace App\Http\Controllers;

use App\Models\User;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
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
            $data = $permintaan->validate(['name' => ['required', 'string', 'max:255', 'not_regex:/\x00/']], $this->pesanValidasi(), ['name' => 'Nama']);
            $pengguna->update($data);

            return redirect()->route('akun.edit')->with('status', 'Nama berhasil diperbarui.');
        }

        $pengguna = $this->perusahaan($permintaan);
        $data = $permintaan->validate([
            'name' => ['required', 'string', 'max:255', 'not_regex:/\x00/'],
            'telepon' => ['required', 'string', 'max:25', 'regex:/\A\+?[0-9][0-9 ()-]{7,24}\z/'],
            'bidang_usaha' => ['required', 'string', 'max:100', 'not_regex:/\x00/'],
            'jumlah_karyawan' => ['required', 'string', Rule::in(['1–10', '11–50', '51–200', '201–500', '501+'])],
            'alamat_perusahaan' => ['required', 'string', 'max:500', 'not_regex:/\x00/'],
            'kota_perusahaan' => ['required', 'string', 'max:100', 'not_regex:/\x00/'],
            'deskripsi_perusahaan' => ['required', 'string', 'min:30', 'max:2000', 'not_regex:/\x00/'],
            'situs_perusahaan' => ['nullable', 'string', 'max:255', 'url:http,https'],
            'nama_penanggung_jawab' => ['required', 'string', 'max:255', 'not_regex:/\x00/'],
            'jabatan_penanggung_jawab' => ['required', 'string', 'max:100', 'not_regex:/\x00/'],
        ], $this->pesanValidasi(), $this->atributProfil());

        $pengguna->fill(Arr::only($data, ['name']));
        $pengguna->profil_pendaftaran = Arr::except($data, ['name']);
        $pengguna->save();

        return redirect()->route('akun.edit')->with('status', 'Profil perusahaan berhasil diperbarui.');
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
            'min' => ':attribute minimal :min karakter.',
            'in' => 'Pilih :attribute yang tersedia.',
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
            'jabatan_penanggung_jawab' => 'Jabatan penanggung jawab',
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
