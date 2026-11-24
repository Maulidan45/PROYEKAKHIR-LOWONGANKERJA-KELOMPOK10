<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token', 'profil_pendaftaran'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    public const array JENIS_AKUN = ['admin' => 'Admin', 'perusahaan' => 'Perusahaan', 'user' => 'Pencari kerja'];

    protected function email(): Attribute
    {
        return Attribute::make(set: fn (string $nilai): string => strtolower(trim($nilai)));
    }

    protected function casts(): array
    {
        return ['email_verified_at' => 'datetime', 'password' => 'hashed', 'profil_pendaftaran' => 'array'];
    }

    public function lowongans(): HasMany
    {
        return $this->hasMany(Lowongan::class, 'perusahaan_id');
    }

    public function lamarans(): HasMany
    {
        return $this->hasMany(Lamaran::class, 'pelamar_id');
    }
}
