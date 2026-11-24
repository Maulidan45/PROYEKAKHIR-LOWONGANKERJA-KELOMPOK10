<?php

namespace Tests\Feature;

use App\Models\Lowongan;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class LowonganAuthorizationTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_perusahaan_tidak_dapat_mengubah_atau_melihat_pelamar_milik_perusahaan_lain(): void
    {
        $pemilik = User::factory()->create(['role' => 'perusahaan']);
        $penyerang = User::factory()->create(['role' => 'perusahaan']);
        $lowongan = Lowongan::factory()->create(['perusahaan_id' => $pemilik->id]);

        $this->actingAs($penyerang)->get(route('lowongan.edit', $lowongan))->assertForbidden();
        $this->patch(route('lowongan.update', $lowongan), [])->assertForbidden();
        $this->get(route('lowongan.pelamar', $lowongan))->assertForbidden();
        $this->delete(route('lowongan.destroy', $lowongan))->assertForbidden();
        $this->assertModelExists($lowongan);
    }

    public function test_admin_dapat_melihat_dan_memperbarui_lowongan_perusahaan_manapun(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $lowongan = Lowongan::factory()->create();

        $this->actingAs($admin)->get(route('lowongan.edit', $lowongan))->assertOk();
    }
}
