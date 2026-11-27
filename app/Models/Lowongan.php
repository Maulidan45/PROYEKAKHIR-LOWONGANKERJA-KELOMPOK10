<?php

namespace App\Models;

use App\Services\WilayahService;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['kategori_id', 'judul', 'lokasi', 'tipe_kerja', 'gaji_min', 'gaji_max', 'batas_lamaran', 'deskripsi', 'aktif', 'sumber', 'eksternal_id', 'sumber_url', 'iklan_sampai', 'iklan_paket'])]
class Lowongan extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return ['aktif' => 'boolean', 'batas_lamaran' => 'date', 'gaji_min' => 'integer', 'gaji_max' => 'integer', 'iklan_sampai' => 'datetime'];
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
        return $kueri->where('aktif', true)->where(function (Builder $bagian): void {
            $bagian->whereNull('batas_lamaran')->orWhereDate('batas_lamaran', '>=', today());
        });
    }

    /**
     * Saring lowongan berdasarkan kata kunci, kategori, tipe kerja, provinsi, dan lokasi.
     *
     * @param  array<string, mixed>  $penyaring
     */
    public function scopeSaring(Builder $kueri, array $penyaring, ?WilayahService $wilayah = null): Builder
    {
        $kueri->when($penyaring['kata_kunci'] ?? null, function (Builder $kueri, string $cari): void {
            $nilaiCari = '%'.mb_strtolower($cari).'%';
            $kueri->where(function (Builder $bagian) use ($nilaiCari): void {
                $bagian->whereRaw('lower(judul) like ?', [$nilaiCari])
                    ->orWhereRaw('lower(lokasi) like ?', [$nilaiCari])
                    ->orWhereHas('perusahaan', fn (Builder $perusahaan) => $perusahaan->whereRaw('lower(name) like ?', [$nilaiCari]));
            });
        });

        $kueri->when($penyaring['kategori'] ?? null, fn (Builder $kueri, mixed $kategori) => $kueri->where('kategori_id', $kategori));
        $kueri->when($penyaring['tipe'] ?? null, fn (Builder $kueri, string $tipe) => $kueri->where('tipe_kerja', $tipe));
        $kueri->when($penyaring['gaji_min'] ?? null, fn (Builder $kueri, mixed $gajiMin) => $kueri->where('gaji_max', '>=', (int) $gajiMin));

        $lokasi = trim((string) ($penyaring['lokasi'] ?? ''));
        $provinsi = trim((string) ($penyaring['provinsi'] ?? ''));

        if ($lokasi !== '') {
            $wilayahLayanan = $wilayah ?? app(WilayahService::class);
            $kataLokasi = $wilayahLayanan->bersihkanAwalanWilayah($lokasi);
            $kueri->whereRaw('lower(lokasi) like ?', ['%'.mb_strtolower($kataLokasi).'%']);
        } elseif ($provinsi !== '') {
            $wilayahLayanan = $wilayah ?? app(WilayahService::class);
            $kataWilayah = $wilayahLayanan->kataKunciWilayahProvinsi($provinsi);

            if ($kataWilayah !== []) {
                $kueri->where(function (Builder $bagian) use ($kataWilayah): void {
                    foreach ($kataWilayah as $indeks => $namaWilayah) {
                        $pola = '%'.mb_strtolower($namaWilayah).'%';
                        if ($indeks === 0) {
                            $bagian->whereRaw('lower(lokasi) like ?', [$pola]);
                        } else {
                            $bagian->orWhereRaw('lower(lokasi) like ?', [$pola]);
                        }
                    }
                });
            }
        }

        $kueri->orderByRaw('CASE WHEN iklan_sampai IS NOT NULL AND iklan_sampai >= ? THEN 0 ELSE 1 END', [now()]);

        match ($penyaring['urutkan'] ?? 'terbaru') {
            'gaji_tertinggi' => $kueri->orderByDesc('gaji_max')->orderByDesc('id'),
            'batas_terdekat' => $kueri->orderByRaw('batas_lamaran IS NULL')->orderBy('batas_lamaran')->orderByDesc('id'),
            default => $kueri->latest(),
        };

        return $kueri;
    }

    public function sedangDiiklankan(): bool
    {
        return $this->iklan_sampai !== null && $this->iklan_sampai->isFuture();
    }

    public function masihMenerimaLamaran(): bool
    {
        return $this->aktif && ($this->batas_lamaran === null || $this->batas_lamaran->gte(today()));
    }

    public function adalahEksternal(): bool
    {
        return $this->sumber !== null && $this->sumber !== 'internal';
    }

    public function statusLowongan(): string
    {
        if (! $this->aktif) {
            return 'Ditutup';
        }

        if ($this->batas_lamaran === null || $this->batas_lamaran->gte(today())) {
            return 'Aktif';
        }

        return 'Kedaluwarsa';
    }
}
