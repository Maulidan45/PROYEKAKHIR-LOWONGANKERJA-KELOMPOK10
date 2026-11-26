<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LowonganResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'kategori' => $this->whenLoaded('kategori', fn (): array => [
                'id' => $this->kategori->id,
                'nama' => $this->kategori->nama,
            ]),
            'perusahaan' => $this->whenLoaded('perusahaan', fn (): array => [
                'id' => $this->perusahaan->id,
                'nama' => $this->perusahaan->name,
            ]),
            'judul' => $this->judul,
            'lokasi' => $this->lokasi,
            'tipe_kerja' => $this->tipe_kerja,
            'gaji_min' => $this->gaji_min,
            'gaji_max' => $this->gaji_max,
            'batas_lamaran' => $this->batas_lamaran->toDateString(),
            'batas_lamaran_format' => $this->batas_lamaran->format('d M Y'),
            'diposting_relatif' => $this->created_at?->locale('id')->diffForHumans() ?? 'Baru saja',
            'detail_url' => route('lowongan.show', $this->id),
            'lamar_url' => route('lamaran.store', $this->id),
            'deskripsi' => $this->deskripsi,
            'aktif' => $this->aktif,
            'status' => $this->statusLowongan(),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
