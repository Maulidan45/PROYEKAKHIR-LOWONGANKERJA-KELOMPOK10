<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class KategoriTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_admin_dapat_menambah_kategori(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post(route('kategori.store'), ['nama' => 'Teknologi Informasi'])
            ->assertRedirectToRoute('kategori.index')->assertSessionHasNoErrors();

        $this->assertDatabaseHas('kategoris', ['nama' => 'Teknologi Informasi', 'slug' => 'teknologi-informasi']);
    }

    public function test_perusahaan_tidak_dapat_mengelola_kategori(): void
    {
        $perusahaan = User::factory()->create(['role' => 'perusahaan']);

        $this->actingAs($perusahaan)->get(route('kategori.index'))->assertForbidden();
        $this->post(route('kategori.store'), ['nama' => 'Teknologi'])->assertForbidden();
    }
}
