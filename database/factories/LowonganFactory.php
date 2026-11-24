<?php

namespace Database\Factories;

use App\Models\Kategori;
use App\Models\Lowongan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LowonganFactory extends Factory
{
    protected $model = Lowongan::class;

    public function definition(): array
    {
        return ['kategori_id' => Kategori::factory(), 'perusahaan_id' => User::factory()->state(['role' => 'perusahaan']), 'judul' => fake()->jobTitle(), 'lokasi' => fake()->city(), 'tipe_kerja' => 'Penuh waktu', 'gaji_min' => 5000000, 'gaji_max' => 8000000, 'batas_lamaran' => now()->addMonth(), 'deskripsi' => fake()->paragraph(4), 'aktif' => true];
    }
}
