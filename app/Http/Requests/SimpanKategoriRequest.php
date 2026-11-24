<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SimpanKategoriRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        return ['nama' => ['required', 'string', 'min:3', 'max:100', 'unique:kategoris,nama']];
    }

    public function messages(): array
    {
        return ['required' => 'Nama kategori wajib diisi.', 'unique' => 'Kategori sudah tersedia.', 'min.string' => 'Nama kategori minimal :min karakter.'];
    }
}
