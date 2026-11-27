<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'lowongan_id',
    'pelamar_id',
    'status',
    'catatan',
    'data_pelamar',
    'cv_path',
    'surat_lamaran_path',
    'ijazah_path',
    'portofolio_path',
    'sertifikat_path',
])]
class Lamaran extends Model
{
    use HasFactory;

    public const array STATUS = [
        'Lamaran Terkirim',
        'Seleksi Administrasi',
        'Seleksi Berkas',
        'Wawancara',
        'Diterima',
        'Ditolak',
    ];

    public const array STATUS_PROSES = [
        'Lamaran Terkirim',
        'Seleksi Administrasi',
        'Seleksi Berkas',
        'Wawancara',
    ];

    protected function casts(): array
    {
        return [
            'data_pelamar' => 'array',
        ];
    }

    public function lowongan(): BelongsTo
    {
        return $this->belongsTo(Lowongan::class);
    }

    public function pelamar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pelamar_id');
    }

    public function riwayats(): HasMany
    {
        return $this->hasMany(RiwayatLamaran::class)->orderBy('created_at')->orderBy('id');
    }

    public function jadwalWawancaras(): HasMany
    {
        return $this->hasMany(JadwalWawancara::class)->latest();
    }

    public function jadwalWawancaraTerbaru(): HasOne
    {
        return $this->hasOne(JadwalWawancara::class)->latestOfMany();
    }

    /**
     * @return array<string, array{label: string, path: ?string}>
     */
    public function daftarDokumen(): array
    {
        return [
            'cv' => ['label' => 'Curriculum Vitae (CV)', 'path' => $this->cv_path],
            'surat_lamaran' => ['label' => 'Surat Lamaran Kerja', 'path' => $this->surat_lamaran_path],
            'ijazah' => ['label' => 'Ijazah Terakhir', 'path' => $this->ijazah_path],
            'portofolio' => ['label' => 'Portofolio', 'path' => $this->portofolio_path],
            'sertifikat' => ['label' => 'Sertifikat Pendukung', 'path' => $this->sertifikat_path],
        ];
    }
}
