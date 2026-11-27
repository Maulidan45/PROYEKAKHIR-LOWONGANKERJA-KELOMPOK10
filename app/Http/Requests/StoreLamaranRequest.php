<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLamaranRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'user';
    }

    /**
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'nama_lengkap' => ['required', 'string', 'max:255', 'not_regex:/\x00/'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'telepon' => ['required', 'string', 'max:25', 'regex:/\A\+?[0-9][0-9 ()-]{7,24}\z/'],
            'tempat_lahir' => ['required', 'string', 'max:100', 'not_regex:/\x00/'],
            'tanggal_lahir' => ['required', 'date', 'before:today'],
            'jenis_kelamin' => ['required', Rule::in(['Laki-laki', 'Perempuan'])],
            'domisili' => ['required', 'string', 'max:255', 'not_regex:/\x00/'],
            'pendidikan_terakhir' => ['required', Rule::in(['SD', 'SMP', 'SMA/SMK', 'Diploma', 'S1', 'S2', 'S3'])],
            'pengalaman_kerja' => ['required', 'string', 'max:2000', 'not_regex:/\x00/'],
            'keahlian' => ['required', 'string', 'max:1000', 'not_regex:/\x00/'],
            'catatan' => ['nullable', 'string', 'max:1500', 'not_regex:/\x00/'],
            'cv' => ['required', 'file', 'mimes:pdf', 'max:5120'],
            'surat_lamaran' => ['required', 'file', 'mimes:pdf', 'max:5120'],
            'ijazah' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'portofolio' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'sertifikat' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi atau diunggah.',
            'file' => ':attribute harus berupa berkas yang valid.',
            'mimes' => 'Format :attribute harus berupa :values.',
            'max.file' => 'Ukuran :attribute maksimal 5 MB.',
            'email' => 'Masukkan alamat email yang valid.',
            'before' => ':attribute harus sebelum hari ini.',
            'telepon.regex' => 'Masukkan nomor telepon yang valid (8–25 karakter).',
            'in' => 'Pilih :attribute yang tersedia.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'nama_lengkap' => 'Nama lengkap',
            'email' => 'Email',
            'telepon' => 'Nomor telepon',
            'tempat_lahir' => 'Tempat lahir',
            'tanggal_lahir' => 'Tanggal lahir',
            'jenis_kelamin' => 'Jenis kelamin',
            'domisili' => 'Alamat atau domisili',
            'pendidikan_terakhir' => 'Pendidikan terakhir',
            'pengalaman_kerja' => 'Pengalaman kerja',
            'keahlian' => 'Keahlian',
            'catatan' => 'Pesan untuk perusahaan',
            'cv' => 'CV (Curriculum Vitae)',
            'surat_lamaran' => 'Surat lamaran kerja',
            'ijazah' => 'Ijazah terakhir',
            'portofolio' => 'Portofolio',
            'sertifikat' => 'Sertifikat pendukung',
        ];
    }
}
