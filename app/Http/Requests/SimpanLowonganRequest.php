<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SimpanLowonganRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'perusahaan';
    }

    public function rules(): array
    {
        return ['kategori_id' => ['required', 'exists:kategoris,id'], 'judul' => ['required', 'string', 'min:5', 'max:120'], 'lokasi' => ['required', 'string', 'max:100'], 'tipe_kerja' => ['required', 'in:Penuh waktu,Paruh waktu,Magang,Jarak jauh'], 'gaji_min' => ['required', 'integer', 'min:0'], 'gaji_max' => ['required', 'integer', 'gte:gaji_min'], 'batas_lamaran' => ['required', 'date', 'after_or_equal:today'], 'deskripsi' => ['required', 'string', 'min:30', 'max:5000'], 'aktif' => ['nullable', 'boolean']];
    }

    public function messages(): array
    {
        return ['required' => ':attribute wajib diisi.', 'exists' => 'Kategori tidak tersedia.', 'gte' => 'Gaji maksimal harus sama atau lebih besar dari gaji minimal.', 'after_or_equal' => 'Batas lamaran tidak boleh sebelum hari ini.', 'min.string' => ':attribute minimal :min karakter.'];
    }

    public function attributes(): array
    {
        return ['kategori_id' => 'Kategori', 'judul' => 'Judul posisi', 'lokasi' => 'Lokasi', 'tipe_kerja' => 'Tipe kerja', 'gaji_min' => 'Gaji minimal', 'gaji_max' => 'Gaji maksimal', 'batas_lamaran' => 'Batas lamaran', 'deskripsi' => 'Deskripsi'];
    }
}
