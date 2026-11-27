<?php

namespace App\Notifications;

use App\Models\JadwalWawancara;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class JadwalWawancaraNotification extends Notification
{
    use Queueable;

    public function __construct(public JadwalWawancara $jadwal, public bool $adalahPerubahan = false) {}

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
        $this->jadwal->loadMissing(['lamaran.lowongan:id,judul', 'perusahaan:id,name']);
        $judulLowongan = $this->jadwal->lamaran->lowongan->judul;
        $namaPerusahaan = $this->jadwal->perusahaan->name;

        $pesan = $this->adalahPerubahan
            ? "Jadwal wawancara untuk {$judulLowongan} di {$namaPerusahaan} telah diperbarui menjadi {$this->jadwal->tanggal->format('d M Y')} pukul {$this->jadwal->jam}."
            : "Anda dijadwalkan wawancara untuk {$judulLowongan} di {$namaPerusahaan} pada {$this->jadwal->tanggal->format('d M Y')} pukul {$this->jadwal->jam}.";

        return [
            'kategori_notif' => 'wawancara',
            'jadwal_id' => $this->jadwal->id,
            'lamaran_id' => $this->jadwal->lamaran_id,
            'judul_lowongan' => $judulLowongan,
            'perusahaan' => $namaPerusahaan,
            'tanggal' => $this->jadwal->tanggal->format('d M Y'),
            'jam' => $this->jadwal->jam,
            'tipe' => $this->jadwal->tipe,
            'pesan' => $pesan,
            'url' => route('wawancara.index'),
        ];
    }
}
