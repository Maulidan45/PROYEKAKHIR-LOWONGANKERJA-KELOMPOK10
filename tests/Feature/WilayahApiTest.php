<?php

namespace Tests\Feature;

use App\Models\Kategori;
use App\Models\Lowongan;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WilayahApiTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        Cache::flush();
    }

    public function test_api_wilayah_mengambil_dan_memformat_provinsi_dan_kabupaten(): void
    {
        Http::fake([
            '*/provinces.json' => Http::response([
                ['id' => '35', 'name' => 'JAWA TIMUR'],
                ['id' => '31', 'name' => 'DKI JAKARTA'],
            ]),
            '*/regencies/35.json' => Http::response([
                ['id' => '3578', 'name' => 'KOTA SURABAYA'],
                ['id' => '3515', 'name' => 'KABUPATEN SIDOARJO'],
            ]),
        ]);

        $this->getJson(route('api.v1.wilayah.provinsi'))
            ->assertOk()
            ->assertJsonPath('data.0.nama', 'DKI Jakarta')
            ->assertJsonPath('data.1.nama', 'Jawa Timur');

        $this->getJson(route('api.v1.wilayah.kabupaten', ['provinsi_id' => '35']))
            ->assertOk()
            ->assertJsonPath('data.0.nama', 'Kabupaten Sidoarjo')
            ->assertJsonPath('data.1.nama', 'Kota Surabaya');
    }

    public function test_api_wilayah_memvalidasi_provinsi_id_dan_punya_fallback_saat_offline(): void
    {
        $this->getJson(route('api.v1.wilayah.kabupaten', ['provinsi_id' => 'abc']))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('provinsi_id');

        Http::fake([
            '*' => Http::response(null, 500),
        ]);

        $this->getJson(route('api.v1.wilayah.provinsi'))
            ->assertOk()
            ->assertJsonFragment(['id' => '35', 'nama' => 'Jawa Timur']);
    }

    public function test_lowongan_dapat_disaring_berdasarkan_provinsi_dan_kota(): void
    {
        Http::fake([
            '*/provinces.json' => Http::response([
                ['id' => '35', 'name' => 'JAWA TIMUR'],
                ['id' => '31', 'name' => 'DKI JAKARTA'],
            ]),
            '*/regencies/35.json' => Http::response([
                ['id' => '3578', 'name' => 'KOTA SURABAYA'],
                ['id' => '3515', 'name' => 'KABUPATEN SIDOARJO'],
            ]),
        ]);

        $kategori = Kategori::factory()->create(['nama' => 'Teknologi']);
        $jatim = Lowongan::factory()->create([
            'kategori_id' => $kategori->id,
            'judul' => 'Staf Accounting',
            'lokasi' => 'Kota Surabaya',
        ]);
        $jakarta = Lowongan::factory()->create([
            'kategori_id' => $kategori->id,
            'judul' => 'Analis Logistik Jakarta',
            'lokasi' => 'Kota Jakarta Selatan',
        ]);

        $this->getJson(route('api.v1.lowongan.index', ['provinsi' => '35']))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $jatim->id);

        $this->getJson(route('api.v1.lowongan.index', ['lokasi' => 'Kota Surabaya']))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $jatim->id);

        $this->get('/?provinsi=35')
            ->assertOk()
            ->assertSee($jatim->judul)
            ->assertDontSee($jakarta->judul);
    }
}
