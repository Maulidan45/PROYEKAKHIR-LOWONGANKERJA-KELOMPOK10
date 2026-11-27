<?php

namespace App\Policies;

use App\Models\Lamaran;
use App\Models\User;

class LamaranPolicy
{
    public function view(User $pengguna, Lamaran $lamaran): bool
    {
        return $lamaran->pelamar_id === $pengguna->id
            || ($pengguna->role === 'perusahaan' && $lamaran->lowongan->perusahaan_id === $pengguna->id);
    }

    public function update(User $pengguna, Lamaran $lamaran): bool
    {
        return $pengguna->role === 'perusahaan' && $lamaran->lowongan->perusahaan_id === $pengguna->id;
    }

    public function unduhDokumen(User $pengguna, Lamaran $lamaran): bool
    {
        return $this->view($pengguna, $lamaran);
    }
}
