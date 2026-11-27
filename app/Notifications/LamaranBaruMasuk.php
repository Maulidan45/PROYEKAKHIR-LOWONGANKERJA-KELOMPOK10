<?php

namespace App\Notifications;

use App\Models\Lamaran;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LamaranBaruMasuk extends Notification
{
    use Queueable;

    public function __construct(public Lamaran $lamaran) {}

    /**
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $this->lamaran->loadMissing(['lowongan:id,perusahaan_id,judul', 'pelamar:id,name']);

        return [
            'kategori_notif' => 'lamaran_baru',
            'lamaran_id' => $this->lamaran->id,
            'lowongan_id' => $this->lamaran->lowongan_id,
            'judul_lowongan' => $this->lamaran->lowongan->judul,
            'pelamar' => $this->lamaran->pelamar->name,
            'pesan' => "{$this->lamaran->pelamar->name} melamar posisi {$this->lamaran->lowongan->judul}.",
            'url' => route('lamaran.show', $this->lamaran),
        ];
    }
}
