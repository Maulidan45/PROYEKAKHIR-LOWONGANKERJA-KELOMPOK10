<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class VerifikasiPerusahaanNotification extends Notification
{
    use Queueable;

    public function __construct(public User $perusahaan) {}

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
        $pesan = match ($this->perusahaan->status_verifikasi) {
            'Terverifikasi' => 'Selamat! Akun perusahaan Anda telah terverifikasi dan dapat mempublikasikan lowongan secara resmi.',
            'Ditolak' => 'Verifikasi perusahaan Anda ditolak. Periksa catatan admin dan unggah ulang dokumen legalitas.',
            default => 'Status verifikasi perusahaan Anda sedang ditinjau oleh admin.',
        };

        return [
            'kategori_notif' => 'verifikasi_perusahaan',
            'status' => $this->perusahaan->status_verifikasi,
            'catatan' => $this->perusahaan->catatan_verifikasi,
            'pesan' => $pesan,
            'url' => route('akun.edit'),
        ];
    }
}
