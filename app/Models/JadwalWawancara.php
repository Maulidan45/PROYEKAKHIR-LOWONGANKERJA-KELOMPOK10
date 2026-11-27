<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['lamaran_id', 'perusahaan_id', 'pelamar_id', 'tanggal', 'jam', 'tipe', 'lokasi_atau_tautan', 'catatan', 'status'])]
class JadwalWawancara extends Model
{
    public const array STATUS = ['Dijadwalkan', 'Diubah', 'Selesai', 'Dibatalkan'];

    public const array TIPE = ['Online', 'Tatap Muka'];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
        ];
    }

    public function lamaran(): BelongsTo
    {
        return $this->belongsTo(Lamaran::class);
    }

    public function perusahaan(): BelongsTo
    {
        return $this->belongsTo(User::class, 'perusahaan_id');
    }

    public function pelamar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pelamar_id');
    }
}
