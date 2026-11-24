<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable(['nama', 'slug'])]
class Kategori extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::saving(function (Kategori $kategori): void {
            $kategori->slug = Str::slug($kategori->nama);
        });
    }

    public function lowongans(): HasMany
    {
        return $this->hasMany(Lowongan::class);
    }
}
