<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class TransaksiAd extends Model
{
    protected $table = 'transaksi_ads';

    protected $fillable = [
        'perusahaan_id',
        'lowongan_id',
        'reference_id',
        'trx_id',
        'idempotency_key',
        'paket',
        'durasi_hari',
        'budget_tambahan',
        'amount_dasar',
        'amount_bayar',
        'channel_code',
        'channel_name',
        'status',
        'is_sandbox',
        'pay_url',
        'simulator_url',
        'payment_info',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'payment_info' => 'array',
            'is_sandbox' => 'boolean',
            'paid_at' => 'datetime',
        ];
    }

    public function perusahaan(): BelongsTo
    {
        return $this->belongsTo(User::class, 'perusahaan_id');
    }

    public function lowongan(): BelongsTo
    {
        return $this->belongsTo(Lowongan::class, 'lowongan_id');
    }

    public function aktifkanIklanJikaLunas(string $statusBaru, ?string $paidAt = null): void
    {
        DB::transaction(function () use ($statusBaru, $paidAt): void {
            $terkunci = self::where('id', $this->id)->lockForUpdate()->first();
            if (! $terkunci) {
                return;
            }

            $sebelumnyaLunas = $terkunci->status === 'paid';
            $terkunci->status = $statusBaru;

            if ($statusBaru === 'paid' && ! $terkunci->paid_at) {
                $terkunci->paid_at = $paidAt ? now()->parse($paidAt) : now();
            }

            $terkunci->save();

            if ($statusBaru === 'paid' && ! $sebelumnyaLunas) {
                $lowongan = Lowongan::where('id', $terkunci->lowongan_id)->lockForUpdate()->first();
                if ($lowongan) {
                    $basisWaktu = $lowongan->sedangDiiklankan() ? $lowongan->iklan_sampai : now();
                    $labelPaket = $terkunci->durasi_hari.' Hari (Rp '.number_format((int) $terkunci->amount_dasar, 0, ',', '.').')';

                    $lowongan->update([
                        'iklan_sampai' => $basisWaktu->copy()->addDays((int) $terkunci->durasi_hari),
                        'iklan_paket' => $labelPaket,
                    ]);
                }
            }
        });

        $this->refresh();
    }
}
