<?php

namespace Database\Factories;

use App\Models\Kategori;
use Illuminate\Database\Eloquent\Factories\Factory;

class KategoriFactory extends Factory
{
    protected $model = Kategori::class;

    public function definition(): array
    {
        $nama = fake()->unique()->words(2, true);

        return ['nama' => ucwords($nama), 'slug' => fake()->unique()->slug(2)];
    }
}
