<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['lowongan_id', 'pelamar_id', 'status', 'catatan'])]
class Lamaran extends Model
{
    use HasFactory;

    public const array STATUS = ['Dikirim', 'Ditinjau', 'Diterima', 'Ditolak'];

    public function lowongan(): BelongsTo
    {
        return $this->belongsTo(Lowongan::class);
    }

    public function pelamar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pelamar_id');
    }
}
