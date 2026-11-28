<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Laporan extends Model
{
    public const array STATUS = [
        'baru' => 'Baru',
        'ditinjau' => 'Ditinjau',
        'selesai' => 'Selesai',
        'ditolak' => 'Ditolak',
    ];

    public const array KATEGORI_LOWONGAN = [
        'Penipuan / Scam',
        'Informasi Gaji / Syarat Palsu',
        'Meminta Biaya Pendaftaran',
        'Konten Tidak Pantas',
        'Lainnya',
    ];

    public const array KATEGORI_PENGGUNA = [
        'Data / Dokumen Palsu',
        'Spam Lamaran',
        'Perilaku Tidak Profesional',
        'Tidak Hadir Tanpa Kabar',
        'Lainnya',
    ];

    protected $fillable = [
        'pelapor_id',
        'terlapor_user_id',
        'lowongan_id',
        'jenis',
        'kategori_laporan',
        'deskripsi',
        'status',
        'catatan_admin',
    ];

    public function pelapor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pelapor_id');
    }

    public function terlaporUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'terlapor_user_id');
    }

    public function lowongan(): BelongsTo
    {
        return $this->belongsTo(Lowongan::class, 'lowongan_id');
    }
}
