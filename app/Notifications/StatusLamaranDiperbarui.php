<?php

namespace App\Notifications;

use App\Models\Lamaran;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class StatusLamaranDiperbarui extends Notification
{
    use Queueable;

    public function __construct(public Lamaran $lamaran, public ?string $catatan = null) {}

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
        $this->lamaran->loadMissing(['lowongan:id,perusahaan_id,judul', 'lowongan.perusahaan:id,name']);

        $pesan = match ($this->lamaran->status) {
            'Lamaran Terkirim' => 'Lamaran Anda berhasil dikirim dan diterima oleh sistem.',
            'Seleksi Administrasi' => 'Lamaran Anda sedang dalam tahap Seleksi Administrasi.',
            'Seleksi Berkas' => 'Berkas lamaran Anda sedang ditinjau pada tahap Seleksi Berkas.',
            'Wawancara' => 'Selamat! Anda masuk ke tahap Wawancara.',
            'Diterima' => 'Selamat! Lamaran Anda telah DITERIMA oleh perusahaan.',
            'Ditolak' => 'Mohon maaf, lamaran Anda belum dapat dilanjutkan pada kesempatan ini.',
            default => "Status lamaran diperbarui menjadi {$this->lamaran->status}.",
        };

        return [
            'kategori_notif' => 'lamaran_status',
            'lamaran_id' => $this->lamaran->id,
            'lowongan_id' => $this->lamaran->lowongan_id,
            'judul_lowongan' => $this->lamaran->lowongan->judul,
            'perusahaan' => $this->lamaran->lowongan->perusahaan->name,
            'status' => $this->lamaran->status,
            'pesan' => $pesan,
            'catatan' => $this->catatan,
            'url' => route('lamaran.show', $this->lamaran),
        ];
    }
}
