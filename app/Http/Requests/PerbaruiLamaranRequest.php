<?php

namespace App\Http\Requests;

use App\Models\Lamaran;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PerbaruiLamaranRequest extends FormRequest
{
    public function authorize(): bool
    {
        $lamaran = $this->route('lamaran');

        return $lamaran instanceof Lamaran && $this->user()?->can('update', $lamaran) === true;
    }

    /**
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(Lamaran::STATUS)],
            'catatan' => ['nullable', 'string', 'max:1000', 'not_regex:/\x00/'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return ['status.in' => 'Tahapan seleksi lamaran tidak tersedia.'];
    }
}
