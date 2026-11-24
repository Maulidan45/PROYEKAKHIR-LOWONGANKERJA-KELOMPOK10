<?php

namespace Database\Factories;

use App\Models\Lamaran;
use App\Models\Lowongan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LamaranFactory extends Factory
{
    protected $model = Lamaran::class;

    public function definition(): array
    {
        return ['lowongan_id' => Lowongan::factory(), 'pelamar_id' => User::factory()->state(['role' => 'user']), 'status' => 'Dikirim'];
    }
}
