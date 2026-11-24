<?php

namespace App\Http\Requests;

use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PermintaanPendaftaran extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->input('email'))) {
            $this->merge(['email' => strtolower(trim($this->input('email')))]);
        }
    }

    /**
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'not_regex:/\x00/'],
            'email' => ['bail', 'required', 'string', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', 'string', Rule::in(['user', 'perusahaan'])],
            'password' => ['bail', 'required', 'string', 'min:12', 'max:72', 'confirmed', function (string $atribut, mixed $nilai, Closure $gagal): void {
                if (strlen($nilai) > 72 || str_contains($nilai, "\0")) {
                    $gagal('Kata sandi maksimal 72 byte dan tidak boleh mengandung karakter null.');
                }
            }],
            'telepon' => ['required', 'string', 'max:25', 'regex:/\A\+?[0-9][0-9 ()-]{7,24}\z/'],
            'domisili' => ['exclude_unless:role,user', 'required', 'string', 'max:100', 'not_regex:/\x00/'],
            'pendidikan_terakhir' => ['exclude_unless:role,user', 'required', 'string', Rule::in(['SD', 'SMP', 'SMA/SMK', 'Diploma', 'S1', 'S2', 'S3'])],
            'bidang_usaha' => ['exclude_unless:role,perusahaan', 'required', 'string', 'max:100', 'not_regex:/\x00/'],
            'jumlah_karyawan' => ['exclude_unless:role,perusahaan', 'required', 'string', Rule::in(['1–10', '11–50', '51–200', '201–500', '501+'])],
            'alamat_perusahaan' => ['exclude_unless:role,perusahaan', 'required', 'string', 'max:500', 'not_regex:/\x00/'],
            'kota_perusahaan' => ['exclude_unless:role,perusahaan', 'required', 'string', 'max:100', 'not_regex:/\x00/'],
            'deskripsi_perusahaan' => ['exclude_unless:role,perusahaan', 'required', 'string', 'min:30', 'max:2000', 'not_regex:/\x00/'],
            'situs_perusahaan' => ['exclude_unless:role,perusahaan', 'nullable', 'string', 'max:255', 'url:http,https'],
            'nama_penanggung_jawab' => ['exclude_unless:role,perusahaan', 'required', 'string', 'max:255', 'not_regex:/\x00/'],
            'jabatan_penanggung_jawab' => ['exclude_unless:role,perusahaan', 'required', 'string', 'max:100', 'not_regex:/\x00/'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'string' => ':attribute harus berupa teks.',
            'max' => ':attribute maksimal :max karakter.',
            'min' => ':attribute minimal :min karakter.',
            'in' => 'Pilih :attribute yang tersedia.',
            'not_regex' => ':attribute tidak boleh mengandung karakter null.',
            'email.email' => 'Masukkan alamat email yang valid.',
            'email.unique' => 'Email sudah terdaftar. Silakan masuk.',
            'role.in' => 'Pilih akun pencari kerja atau perusahaan.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'telepon.regex' => 'Masukkan nomor telepon yang valid (8–25 karakter).',
            'situs_perusahaan.url' => 'Situs perusahaan harus berupa alamat HTTP atau HTTPS yang valid.',
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return [
            'name' => 'Nama', 'email' => 'Email', 'role' => 'Jenis akun', 'password' => 'Kata sandi',
            'telepon' => 'Nomor telepon', 'domisili' => 'Domisili', 'pendidikan_terakhir' => 'Pendidikan terakhir',
            'bidang_usaha' => 'Bidang usaha', 'jumlah_karyawan' => 'Jumlah karyawan',
            'alamat_perusahaan' => 'Alamat perusahaan', 'kota_perusahaan' => 'Kota perusahaan',
            'deskripsi_perusahaan' => 'Deskripsi perusahaan', 'situs_perusahaan' => 'Situs perusahaan',
            'nama_penanggung_jawab' => 'Nama penanggung jawab', 'jabatan_penanggung_jawab' => 'Jabatan penanggung jawab',
        ];
    }
}
