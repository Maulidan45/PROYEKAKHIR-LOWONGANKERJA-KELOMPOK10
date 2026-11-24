<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PerbaruiLamaranRequest extends FormRequest
{
    public function authorize(): bool
    {
        $lamaran = $this->route('lamaran');

        return $lamaran && $this->user()?->can('updateStatus', $lamaran->lowongan) === true;
    }

    public function rules(): array
    {
        return ['status' => ['required', Rule::in(['Dikirim', 'Ditinjau', 'Diterima', 'Ditolak'])]];
    }

    public function messages(): array
    {
        return ['status.in' => 'Status lamaran tidak tersedia.'];
    }
}
