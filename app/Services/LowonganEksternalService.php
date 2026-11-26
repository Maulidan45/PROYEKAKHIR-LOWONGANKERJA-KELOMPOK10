<?php

namespace App\Services;

use App\Models\Kategori;
use App\Models\Lowongan;
use App\Models\User;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

class LowonganEksternalService
{
    /**
     * Tarik lowongan kerja nyata dari API publik Remotive dan simpan ke database lokal.
     */
    public function sinkronDariRemotive(int $batas = 20): int
    {
        $jumlah = max(1, min($batas, 50));

        $tanggapan = Http::baseUrl((string) config('services.remotive.base_url'))
            ->acceptJson()
            ->connectTimeout(5)
            ->timeout(10)
            ->retry([150, 400], 0, function (Throwable $pengecualian): bool {
                return $pengecualian instanceof ConnectionException
                    || ($pengecualian instanceof RequestException
                        && ($pengecualian->response->serverError() || $pengecualian->response->status() === 429));
            })
            ->get('/remote-jobs', ['limit' => $jumlah])
            ->throw()
            ->json();

        $daftarPekerjaan = $tanggapan['jobs'] ?? [];
        if (! is_array($daftarPekerjaan)) {
            return 0;
        }

        $tersimpan = 0;

        foreach ($daftarPekerjaan as $item) {
            if (! is_array($item) || empty($item['title']) || empty($item['company_name'])) {
                continue;
            }

            $namaKategori = $this->petakanKategori((string) ($item['category'] ?? 'Teknologi Informasi'));
            $kategori = Kategori::query()->firstOrCreate(
                ['slug' => Str::slug($namaKategori)],
                ['nama' => $namaKategori]
            );

            $namaPerusahaan = Str::limit(trim((string) $item['company_name']), 100, '');
            $slugPerusahaan = Str::slug($namaPerusahaan) ?: 'perusahaan-eksternal';
            $perusahaan = User::query()->firstOrCreate(
                ['email' => "{$slugPerusahaan}@eksternal.lokerku.local"],
                [
                    'name' => $namaPerusahaan,
                    'password' => Str::random(24),
                    'role' => 'perusahaan',
                    'email_verified_at' => now(),
                ]
            );

            $judul = Str::limit(trim((string) $item['title']), 150, '');
            $lokasi = Str::limit(trim((string) ($item['candidate_required_location'] ?? '')) ?: 'Jarak jauh (Global)', 100, '');
            $tipeKerja = $this->petakanTipeKerja((string) ($item['job_type'] ?? ''));
            $deskripsiTeks = $this->bersihkanDeskripsi(
                (string) ($item['description'] ?? ''),
                (string) ($item['url'] ?? '')
            );

            $perusahaan->lowongans()->firstOrCreate(
                ['judul' => $judul],
                [
                    'kategori_id' => $kategori->id,
                    'lokasi' => $lokasi,
                    'tipe_kerja' => $tipeKerja,
                    'gaji_min' => 8000000,
                    'gaji_max' => 15000000,
                    'batas_lamaran' => now()->addMonth()->toDateString(),
                    'deskripsi' => $deskripsiTeks,
                    'aktif' => true,
                ]
            );

            $tersimpan++;
        }

        return $tersimpan;
    }

    private function petakanKategori(string $kategori): string
    {
        $kecil = mb_strtolower(trim($kategori));

        return match (true) {
            str_contains($kecil, 'software'), str_contains($kecil, 'dev'), str_contains($kecil, 'data'), str_contains($kecil, 'qa'), str_contains($kecil, 'sysadmin') => 'Teknologi Informasi',
            str_contains($kecil, 'design') => 'Desain & Kreatif',
            str_contains($kecil, 'marketing'), str_contains($kecil, 'sales'), str_contains($kecil, 'writing') => 'Pemasaran & Penjualan',
            str_contains($kecil, 'finance'), str_contains($kecil, 'legal') => 'Akuntansi & Keuangan',
            str_contains($kecil, 'human resources'), str_contains($kecil, 'hr') => 'Sumber Daya Manusia',
            str_contains($kecil, 'customer'), str_contains($kecil, 'product'), str_contains($kecil, 'business') => 'Administrasi & Perkantoran',
            default => 'Teknologi Informasi',
        };
    }

    private function petakanTipeKerja(string $tipe): string
    {
        return match (mb_strtolower(trim($tipe))) {
            'full_time' => 'Penuh waktu',
            'part_time' => 'Paruh waktu',
            'internship' => 'Magang',
            default => 'Jarak jauh',
        };
    }

    private function bersihkanDeskripsi(string $html, string $urlSumber): string
    {
        $teks = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $teks = preg_replace('/\s+/', ' ', trim($teks)) ?? '';
        $ringkas = Str::limit($teks, 1500, '...');

        if ($urlSumber !== '') {
            $ringkas .= "\n\nSumber lowongan asli: {$urlSumber}";
        }

        return $ringkas !== '' ? $ringkas : 'Informasi detail tersedia pada tautan sumber lowongan.';
    }
}
