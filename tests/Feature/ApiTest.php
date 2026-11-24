<?php

namespace Tests\Feature;

use App\Models\Kategori;
use App\Models\Lowongan;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_pengguna_dapat_masuk_menggunakan_token_dan_mengakses_profil(): void
    {
        $pengguna = User::factory()->create([
            'email' => 'akun@example.com',
            'password' => 'kata-sandi-aman-2026',
            'role' => 'perusahaan',
        ]);

        $tanggapan = $this->postJson(route('api.v1.auth.login'), [
            'email' => ' AKUN@EXAMPLE.COM ',
            'password' => 'kata-sandi-aman-2026',
            'device_name' => 'android',
        ])->assertOk()
            ->assertJsonPath('data.id', $pengguna->id)
            ->assertJsonPath('data.email', 'akun@example.com')
            ->assertJsonMissingPath('data.password')
            ->assertJsonMissingPath('data.profil_pendaftaran');

        $token = $tanggapan->json('token');
        $this->assertIsString($token);
        $this->assertDatabaseCount('personal_access_tokens', 1);

        $this->withToken($token)->getJson(route('api.v1.auth.me'))
            ->assertOk()
            ->assertJsonPath('data.email', 'akun@example.com');

        $this->withToken($token)->postJson(route('api.v1.auth.logout'))->assertNoContent();
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_api_menolak_token_tidak_valid_dan_login_tidak_valid(): void
    {
        $this->withToken('token-tidak-valid')->getJson(route('api.v1.auth.me'))->assertUnauthorized();
        $this->postJson(route('api.v1.lowongan.store'))->assertUnauthorized();

        $this->postJson(route('api.v1.auth.login'), [
            'email' => 'akun@example.com',
            'password' => 'kata-sandi-salah',
            'device_name' => 'web',
        ])->assertUnprocessable()->assertJsonValidationErrors('email');
    }

    public function test_katalog_publik_hanya_menampilkan_lowongan_terbuka_dan_dapat_disaring(): void
    {
        $kategori = Kategori::factory()->create(['nama' => 'Teknologi']);
        $lowongan = Lowongan::factory()->create([
            'kategori_id' => $kategori->id,
            'judul' => 'Backend Laravel Developer',
            'lokasi' => 'Surabaya',
        ]);
        $ditutup = Lowongan::factory()->create(['judul' => 'Lowongan Ditutup', 'aktif' => false]);

        $this->getJson(route('api.v1.lowongan.index', ['kata_kunci' => 'Laravel', 'kategori' => $kategori->id]))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $lowongan->id)
            ->assertJsonPath('data.0.kategori.nama', 'Teknologi')
            ->assertJsonMissing(['id' => $ditutup->id]);

        $this->getJson(route('api.v1.lowongan.show', $ditutup))->assertNotFound();
    }

    public function test_perusahaan_dapat_membuat_lowongan_melalui_api(): void
    {
        $perusahaan = User::factory()->create(['role' => 'perusahaan']);
        $kategori = Kategori::factory()->create();

        Sanctum::actingAs($perusahaan);

        $this->postJson(route('api.v1.lowongan.store'), $this->dataLowongan($kategori))
            ->assertCreated()
            ->assertJsonPath('data.judul', 'Backend Laravel Developer')
            ->assertJsonPath('data.perusahaan.id', $perusahaan->id);

        $this->assertDatabaseHas('lowongans', [
            'perusahaan_id' => $perusahaan->id,
            'kategori_id' => $kategori->id,
            'judul' => 'Backend Laravel Developer',
            'aktif' => 1,
        ]);
    }

    public function test_pengguna_tanpa_izin_tidak_dapat_membuat_atau_memperbarui_lowongan_perusahaan_lain(): void
    {
        $pencariKerja = User::factory()->create(['role' => 'user']);
        $perusahaanLain = User::factory()->create(['role' => 'perusahaan']);
        $lowongan = Lowongan::factory()->create(['perusahaan_id' => $perusahaanLain->id]);
        $kategori = Kategori::factory()->create();

        Sanctum::actingAs($pencariKerja);
        $this->postJson(route('api.v1.lowongan.store'), $this->dataLowongan($kategori))->assertForbidden();

        Sanctum::actingAs(User::factory()->create(['role' => 'perusahaan']));
        $this->patchJson(route('api.v1.lowongan.update', $lowongan), $this->dataLowongan($kategori))
            ->assertForbidden();

        $this->assertModelExists($lowongan);
    }

    public function test_api_memvalidasi_data_lowongan_dan_admin_dapat_menghapusnya(): void
    {
        $perusahaan = User::factory()->create(['role' => 'perusahaan']);
        $lowongan = Lowongan::factory()->create(['perusahaan_id' => $perusahaan->id]);

        Sanctum::actingAs($perusahaan);
        $this->patchJson(route('api.v1.lowongan.update', $lowongan), [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['kategori_id', 'judul', 'lokasi', 'tipe_kerja', 'gaji_min', 'gaji_max', 'batas_lamaran', 'deskripsi']);

        Sanctum::actingAs(User::factory()->create(['role' => 'admin']));
        $this->deleteJson(route('api.v1.lowongan.destroy', $lowongan))->assertNoContent();

        $this->assertModelMissing($lowongan);
    }

    private function dataLowongan(Kategori $kategori): array
    {
        return [
            'kategori_id' => $kategori->id,
            'judul' => 'Backend Laravel Developer',
            'lokasi' => 'Surabaya',
            'tipe_kerja' => 'Penuh waktu',
            'gaji_min' => 7000000,
            'gaji_max' => 10000000,
            'batas_lamaran' => now()->addWeek()->toDateString(),
            'deskripsi' => 'Mengembangkan dan memelihara aplikasi Laravel untuk kebutuhan perusahaan.',
            'aktif' => true,
        ];
    }
}
