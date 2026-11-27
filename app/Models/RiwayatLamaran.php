<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['lamaran_id', 'status_sebelumnya', 'status_baru', 'catatan', 'diubah_oleh'])]
class RiwayatLamaran extends Model
{
    public function lamaran(): BelongsTo
    {
        return $this->belongsTo(Lamaran::class);
    }

    public function pengubah(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diubah_oleh');
    }
}
