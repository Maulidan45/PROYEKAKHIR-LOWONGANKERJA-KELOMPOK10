<?php

namespace Tests\Feature;

use App\Models\Kategori;
use App\Models\Lowongan;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class LowonganTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_perusahaan_dapat_membuat_lowongan_yang_tampil_di_katalog(): void
    {
        $perusahaan = User::factory()->create(['role' => 'perusahaan', 'name' => 'PT Vora Media Group']);
        $kategori = Kategori::factory()->create(['nama' => 'Teknologi']);

        $this->actingAs($perusahaan)->post(route('lowongan.store'), $this->dataLowongan($kategori))
            ->assertRedirectToRoute('lowongan.index')->assertSessionHasNoErrors();

        $lowongan = Lowongan::sole();
        $this->assertSame($perusahaan->id, $lowongan->perusahaan_id);
        $this->assertTrue($lowongan->aktif);
        $this->post(route('logout'));
        $this->get('/')->assertOk()->assertSee('Backend Laravel Developer')->assertSee('PT Vora Media Group');
    }

    public function test_lowongan_ditutup_tidak_tampil_di_katalog_publik(): void
    {
        $lowongan = Lowongan::factory()->create(['aktif' => false]);

        $this->get('/')->assertOk()->assertDontSee($lowongan->judul);
    }

    public function test_perusahaan_wajib_mengisi_data_lowongan_yang_valid(): void
    {
        $perusahaan = User::factory()->create(['role' => 'perusahaan']);

        $this->actingAs($perusahaan)->post(route('lowongan.store'))
            ->assertSessionHasErrors(['kategori_id', 'judul', 'lokasi', 'tipe_kerja', 'gaji_min', 'gaji_max', 'batas_lamaran', 'deskripsi']);

        $this->assertDatabaseCount('lowongans', 0);
    }

    public function test_pencari_kerja_tidak_dapat_membuat_lowongan(): void
    {
        $pencariKerja = User::factory()->create(['role' => 'user']);
        $kategori = Kategori::factory()->create();

        $this->actingAs($pencariKerja)->post(route('lowongan.store'), $this->dataLowongan($kategori))->assertForbidden();
        $this->assertDatabaseCount('lowongans', 0);
    }

    private function dataLowongan(Kategori $kategori): array
    {
        return ['kategori_id' => $kategori->id, 'judul' => 'Backend Laravel Developer', 'lokasi' => 'Surabaya', 'tipe_kerja' => 'Penuh waktu', 'gaji_min' => 7000000, 'gaji_max' => 10000000, 'batas_lamaran' => now()->addWeek()->toDateString(), 'deskripsi' => 'Mengembangkan dan memelihara aplikasi Laravel untuk kebutuhan perusahaan.', 'aktif' => '1'];
    }
}
