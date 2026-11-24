<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['kategori_id', 'judul', 'lokasi', 'tipe_kerja', 'gaji_min', 'gaji_max', 'batas_lamaran', 'deskripsi', 'aktif'])]
class Lowongan extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return ['aktif' => 'boolean', 'batas_lamaran' => 'date', 'gaji_min' => 'integer', 'gaji_max' => 'integer'];
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class);
    }

    public function perusahaan(): BelongsTo
    {
        return $this->belongsTo(User::class, 'perusahaan_id');
    }

    public function lamarans(): HasMany
    {
        return $this->hasMany(Lamaran::class);
    }

    public function scopeTerbuka(Builder $kueri): Builder
    {
        return $kueri->where('aktif', true)->whereDate('batas_lamaran', '>=', today());
    }

    public function statusLowongan(): string
    {
        if (! $this->aktif) {
            return 'Ditutup';
        }

        return $this->batas_lamaran->gte(today()) ? 'Aktif' : 'Kedaluwarsa';
    }
}
