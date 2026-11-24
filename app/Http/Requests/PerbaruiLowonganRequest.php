<?php

namespace App\Http\Requests;

use App\Models\Lowongan;

class PerbaruiLowonganRequest extends SimpanLowonganRequest
{
    public function authorize(): bool
    {
        $lowongan = $this->route('lowongan');

        return $lowongan instanceof Lowongan && $this->user()?->can('update', $lowongan) === true;
    }
}
