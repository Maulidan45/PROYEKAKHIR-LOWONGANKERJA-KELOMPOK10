<?php

namespace Tests\Feature;

use App\Models\Kategori;
use App\Models\Lowongan;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_katalog_menampilkan_lowongan_aktif_dan_menyaring_kata_kunci(): void
    {
        $kategori = Kategori::factory()->create(['nama' => 'Teknologi']);
        $sesuai = Lowongan::factory()->create(['kategori_id' => $kategori->id, 'judul' => 'Backend Laravel Developer']);
        $lainnya = Lowongan::factory()->create(['judul' => 'Desainer Produk']);

        $this->get('/')->assertOk()->assertSee($sesuai->judul)->assertSee($lainnya->judul)->assertSee('2 lowongan tersedia');
        $this->get('/?kata_kunci=Laravel')->assertOk()->assertSee($sesuai->judul)->assertDontSee($lainnya->judul)->assertSee('1 lowongan tersedia');
    }

    public function test_katalog_menyaring_kategori_lokasi_dan_tipe_kerja(): void
    {
        $kategori = Kategori::factory()->create(['nama' => 'Teknologi']);
        $sesuai = Lowongan::factory()->create(['kategori_id' => $kategori->id, 'lokasi' => 'Surabaya', 'tipe_kerja' => 'Penuh waktu', 'judul' => 'Backend Laravel Developer']);
        $lainnya = Lowongan::factory()->create(['kategori_id' => $kategori->id, 'lokasi' => 'Jakarta', 'tipe_kerja' => 'Magang']);

        $this->get('/?kategori='.$kategori->id.'&lokasi=Surabaya&tipe=Penuh%20waktu')->assertOk()->assertSee($sesuai->judul)->assertDontSee($lainnya->judul)->assertSee('1 lowongan tersedia');
    }

    public function test_katalog_tidak_menampilkan_lowongan_tutup_atau_kedaluwarsa(): void
    {
        $ditutup = Lowongan::factory()->create(['aktif' => false]);
        $kedaluwarsa = Lowongan::factory()->create(['batas_lamaran' => now()->subDay()]);

        $this->get('/')->assertOk()->assertDontSee($ditutup->judul)->assertDontSee($kedaluwarsa->judul)->assertSee('Belum ada lowongan yang sesuai dengan pencarianmu.');
    }

    public function test_halaman_utama_tidak_memiliki_tautan_kosong_atau_teks_demo(): void
    {
        $this->get('/')->assertOk()
            ->assertSee('#CariKerja makin mudah')->assertSee('Pakai')->assertSee('lokerku')
            ->assertDontSee('LokerID')->assertDontSee('<strong>0</strong><br>lowongan tersedia', false)
            ->assertDontSee('>Semua</a>', false)
            ->assertSee('Lowongan Kerja Terbaru')->assertSee('career-team.jpg')
            ->assertSee('Masuk')->assertSee('Daftar')
            ->assertDontSee('demo', false)->assertDontSee('href="#"', false);
    }

    public function test_landing_page_search_and_popular_suggestions_keep_supported_filters(): void
    {
        $kategori = Kategori::factory()->create(['nama' => 'Teknologi']);
        $sesuai = Lowongan::factory()->create([
            'kategori_id' => $kategori->id,
            'judul' => 'Backend Laravel Developer',
            'lokasi' => 'Surabaya',
        ]);
        $lainnya = Lowongan::factory()->create(['judul' => 'Staf Admin']);

        $this->get('/')->assertOk()->assertSee(route('home', ['kata_kunci' => 'Staf Admin']), false);

        $this->get('/?kata_kunci=Laravel&lokasi=Surabaya&kategori='.$kategori->id)
            ->assertOk()->assertSee($sesuai->judul)->assertDontSee('<h3>'.$lainnya->judul.'</h3>', false)
            ->assertSee('name="kata_kunci"', false)
            ->assertSee('name="lokasi"', false)
            ->assertSee('name="kategori"', false)
            ->assertSee('name="tipe"', false);
    }

    public function test_halaman_utama_menolak_filter_array(): void
    {
        $this->getJson('/?kata_kunci[]=invalid')->assertUnprocessable()->assertJsonValidationErrors('kata_kunci');
        $this->getJson('/?lokasi='.str_repeat('a', 101))->assertUnprocessable()->assertJsonValidationErrors('lokasi');
    }
}
