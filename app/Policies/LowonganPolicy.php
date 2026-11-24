<?php

namespace App\Policies;

use App\Models\Lowongan;
use App\Models\User;

class LowonganPolicy
{
    public function viewAny(User $pengguna): bool
    {
        return in_array($pengguna->role, ['admin', 'perusahaan'], true);
    }

    public function view(User $pengguna, Lowongan $lowongan): bool
    {
        return $pengguna->role === 'admin' || $lowongan->perusahaan_id === $pengguna->id;
    }

    public function create(User $pengguna): bool
    {
        return $pengguna->role === 'perusahaan';
    }

    public function update(User $pengguna, Lowongan $lowongan): bool
    {
        return $pengguna->role === 'admin' || ($pengguna->role === 'perusahaan' && $lowongan->perusahaan_id === $pengguna->id);
    }

    public function delete(User $pengguna, Lowongan $lowongan): bool
    {
        return $this->update($pengguna, $lowongan);
    }

    public function updateStatus(User $pengguna, Lowongan $lowongan): bool
    {
        return $this->update($pengguna, $lowongan);
    }
}
