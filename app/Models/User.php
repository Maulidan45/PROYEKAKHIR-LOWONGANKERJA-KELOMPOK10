<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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

    public const array STATUS_VERIFIKASI = ['Menunggu Verifikasi', 'Terverifikasi', 'Ditolak'];

    protected function email(): Attribute
    {
        return Attribute::make(set: fn (string $nilai): string => strtolower(trim($nilai)));
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'profil_pendaftaran' => 'array',
            'diverifikasi_pada' => 'datetime',
        ];
    }

    public function lowongans(): HasMany
    {
        return $this->hasMany(Lowongan::class, 'perusahaan_id');
    }

    public function lamarans(): HasMany
    {
        return $this->hasMany(Lamaran::class, 'pelamar_id');
    }

    public function lowonganTersimpan(): BelongsToMany
    {
        return $this->belongsToMany(Lowongan::class, 'simpan_lowongans')->withTimestamps();
    }

    public function jadwalWawancaraPerusahaan(): HasMany
    {
        return $this->hasMany(JadwalWawancara::class, 'perusahaan_id');
    }

    public function jadwalWawancaraPelamar(): HasMany
    {
        return $this->hasMany(JadwalWawancara::class, 'pelamar_id');
    }

    public function sudahTerverifikasi(): bool
    {
        return $this->status_verifikasi === 'Terverifikasi';
    }

    public function persentaseKelengkapanProfil(): int
    {
        if ($this->role !== 'user') {
            return 100;
        }

        $profil = $this->profil_pendaftaran ?? [];
        $bidang = [
            $this->name,
            $this->email,
            $profil['telepon'] ?? null,
            $profil['tempat_lahir'] ?? null,
            $profil['tanggal_lahir'] ?? null,
            $profil['jenis_kelamin'] ?? null,
            $profil['domisili'] ?? null,
            $profil['pendidikan_terakhir'] ?? null,
            $profil['pengalaman_kerja'] ?? null,
            $profil['keahlian'] ?? null,
        ];

        $terisi = collect($bidang)->filter(fn (mixed $nilai): bool => is_string($nilai) && trim($nilai) !== '')->count();

        return (int) round(($terisi / count($bidang)) * 100);
    }
}
