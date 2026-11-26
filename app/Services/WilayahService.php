<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

class WilayahService
{
    /**
     * Ambil daftar provinsi Indonesia dari API wilayah dengan cache dan fallback lokal.
     *
     * @return list<array{id: string, nama: string}>
     */
    public function daftarProvinsi(): array
    {
        $kunciCache = 'wilayah.provinsi.v1';

        $tersimpan = Cache::get($kunciCache);
        if (is_array($tersimpan) && $tersimpan !== []) {
            return $tersimpan;
        }

        try {
            $data = $this->klienHttp()
                ->get('/provinces.json')
                ->throw()
                ->json();

            $hasil = $this->normalisasiDaftar($data);

            if ($hasil !== []) {
                Cache::put($kunciCache, $hasil, now()->addDay());

                return $hasil;
            }
        } catch (Throwable) {
            // Gunakan data fallback lokal jika API wilayah tidak terjangkau.
        }

        return $this->provinsiFallback();
    }

    /**
     * Ambil daftar kabupaten/kota untuk provinsi tertentu.
     *
     * @return list<array{id: string, nama: string}>
     */
    public function daftarKabupaten(string $provinsiId): array
    {
        if (! preg_match('/^\d+$/', $provinsiId)) {
            return [];
        }

        $kunciCache = "wilayah.kabupaten.v1.{$provinsiId}";

        $tersimpan = Cache::get($kunciCache);
        if (is_array($tersimpan) && $tersimpan !== []) {
            return $tersimpan;
        }

        try {
            $data = $this->klienHttp()
                ->get("/regencies/{$provinsiId}.json")
                ->throw()
                ->json();

            $hasil = $this->normalisasiDaftar($data);

            if ($hasil !== []) {
                Cache::put($kunciCache, $hasil, now()->addDay());

                return $hasil;
            }
        } catch (Throwable) {
            // Gunakan data fallback lokal jika API wilayah tidak terjangkau.
        }

        return $this->kabupatenFallback($provinsiId);
    }

    /**
     * Ambil daftar kata kunci lokasi untuk semua kabupaten/kota dalam suatu provinsi.
     *
     * @return list<string>
     */
    public function kataKunciWilayahProvinsi(string $provinsiId): array
    {
        $daftar = [];

        foreach ($this->daftarKabupaten($provinsiId) as $kabupaten) {
            $daftar[] = $this->bersihkanAwalanWilayah($kabupaten['nama']);
        }

        foreach ($this->daftarProvinsi() as $provinsi) {
            if ($provinsi['id'] === $provinsiId) {
                $daftar[] = $this->bersihkanAwalanWilayah($provinsi['nama']);
            }
        }

        return array_values(array_unique(array_filter($daftar)));
    }

    public function bersihkanAwalanWilayah(string $nama): string
    {
        $bersih = preg_replace('/^(Kota|Kabupaten|Kab\.|DKI|DI)\s+/iu', '', trim($nama));

        return trim((string) $bersih);
    }

    private function klienHttp(): PendingRequest
    {
        return Http::baseUrl((string) config('services.wilayah.base_url'))
            ->acceptJson()
            ->connectTimeout(3)
            ->timeout(5)
            ->retry([100, 300], 0, function (Throwable $pengecualian): bool {
                return $pengecualian instanceof ConnectionException
                    || ($pengecualian instanceof RequestException
                        && ($pengecualian->response->serverError() || $pengecualian->response->status() === 429));
            });
    }

    /**
     * @return list<array{id: string, nama: string}>
     */
    private function normalisasiDaftar(mixed $data): array
    {
        if (! is_array($data)) {
            return [];
        }

        $hasil = [];

        foreach ($data as $baris) {
            if (! is_array($baris) || empty($baris['id']) || empty($baris['name'])) {
                continue;
            }

            $hasil[] = [
                'id' => (string) $baris['id'],
                'nama' => $this->formatNamaWilayah((string) $baris['name']),
            ];
        }

        usort($hasil, fn (array $a, array $b): int => strcmp($a['nama'], $b['nama']));

        return $hasil;
    }

    private function formatNamaWilayah(string $nama): string
    {
        $judul = Str::title(mb_strtolower(trim($nama)));

        return str_replace(['Dki ', 'Di ', 'Nad '], ['DKI ', 'DI ', 'NAD '], $judul);
    }

    /**
     * @return list<array{id: string, nama: string}>
     */
    private function provinsiFallback(): array
    {
        return [
            ['id' => '51', 'nama' => 'Bali'],
            ['id' => '36', 'nama' => 'Banten'],
            ['id' => '34', 'nama' => 'DI Yogyakarta'],
            ['id' => '31', 'nama' => 'DKI Jakarta'],
            ['id' => '32', 'nama' => 'Jawa Barat'],
            ['id' => '33', 'nama' => 'Jawa Tengah'],
            ['id' => '35', 'nama' => 'Jawa Timur'],
            ['id' => '73', 'nama' => 'Sulawesi Selatan'],
            ['id' => '12', 'nama' => 'Sumatera Utara'],
        ];
    }

    /**
     * @return list<array{id: string, nama: string}>
     */
    private function kabupatenFallback(string $provinsiId): array
    {
        $peta = [
            '31' => [
                ['id' => '3171', 'nama' => 'Kota Jakarta Selatan'],
                ['id' => '3172', 'nama' => 'Kota Jakarta Timur'],
                ['id' => '3173', 'nama' => 'Kota Jakarta Pusat'],
                ['id' => '3174', 'nama' => 'Kota Jakarta Barat'],
                ['id' => '3175', 'nama' => 'Kota Jakarta Utara'],
            ],
            '32' => [
                ['id' => '3273', 'nama' => 'Kota Bandung'],
                ['id' => '3275', 'nama' => 'Kota Bekasi'],
                ['id' => '3276', 'nama' => 'Kota Depok'],
                ['id' => '3271', 'nama' => 'Kota Bogor'],
            ],
            '33' => [
                ['id' => '3374', 'nama' => 'Kota Semarang'],
                ['id' => '3372', 'nama' => 'Kota Surakarta'],
            ],
            '34' => [
                ['id' => '3471', 'nama' => 'Kota Yogyakarta'],
                ['id' => '3404', 'nama' => 'Kabupaten Sleman'],
            ],
            '35' => [
                ['id' => '3578', 'nama' => 'Kota Surabaya'],
                ['id' => '3573', 'nama' => 'Kota Malang'],
                ['id' => '3515', 'nama' => 'Kabupaten Sidoarjo'],
            ],
            '36' => [
                ['id' => '3671', 'nama' => 'Kota Tangerang'],
                ['id' => '3674', 'nama' => 'Kota Tangerang Selatan'],
            ],
            '51' => [
                ['id' => '5171', 'nama' => 'Kota Denpasar'],
                ['id' => '5103', 'nama' => 'Kabupaten Badung'],
            ],
            '12' => [
                ['id' => '1275', 'nama' => 'Kota Medan'],
            ],
            '73' => [
                ['id' => '7371', 'nama' => 'Kota Makassar'],
            ],
        ];

        return $peta[$provinsiId] ?? [];
    }
}
