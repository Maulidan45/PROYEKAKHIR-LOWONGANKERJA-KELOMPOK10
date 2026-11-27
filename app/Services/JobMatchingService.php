<?php

namespace App\Services;

use App\Models\Lowongan;
use App\Models\User;
use Illuminate\Support\Collection;

class JobMatchingService
{
    /**
     * Hitung skor kecocokan berbasis bobot transparan (tanpa klaim AI):
     * - Keahlian: 35%
     * - Pengalaman kerja: 20%
     * - Pendidikan: 15%
     * - Lokasi: 15%
     * - Kategori pekerjaan: 15%
     *
     * @param  Collection<int, Lowongan>  $daftarLowongan
     * @return Collection<int, array{lowongan: Lowongan, skor: int, alasan: list<string>}>
     */
    public function hitungRekomendasi(User $pelamar, Collection $daftarLowongan): Collection
    {
        $profil = $pelamar->profil_pendaftaran ?? [];
        $keahlianPelamar = $this->pecahKataKunci((string) ($profil['keahlian'] ?? ''));
        $pengalamanPelamar = mb_strtolower(trim((string) ($profil['pengalaman_kerja'] ?? '')));
        $pendidikanPelamar = mb_strtolower(trim((string) ($profil['pendidikan_terakhir'] ?? '')));
        $domisiliPelamar = mb_strtolower(trim((string) ($profil['domisili'] ?? '')));

        $kategoriDisukaiIds = $pelamar->lamarans()
            ->with('lowongan:id,kategori_id')
            ->get()
            ->pluck('lowongan.kategori_id')
            ->merge(
                $pelamar->lowonganTersimpan()->pluck('kategori_id')
            )
            ->filter()
            ->unique()
            ->values();

        return $daftarLowongan->map(function (Lowongan $lowongan) use (
            $keahlianPelamar,
            $pengalamanPelamar,
            $pendidikanPelamar,
            $domisiliPelamar,
            $kategoriDisukaiIds
        ): array {
            $teksLowongan = mb_strtolower($lowongan->judul.' '.$lowongan->deskripsi);
            $lokasiLowongan = mb_strtolower($lowongan->lokasi);

            $skor = 0;
            $alasan = [];

            // 1. Keahlian (35 poin)
            if ($keahlianPelamar !== []) {
                $cocokKeahlian = collect($keahlianPelamar)->filter(
                    fn (string $kata): bool => str_contains($teksLowongan, $kata)
                );
                $rasioKeahlian = min(1, $cocokKeahlian->count() / max(1, min(3, count($keahlianPelamar))));
                $poinKeahlian = (int) round($rasioKeahlian * 35);
                $skor += $poinKeahlian;
                if ($poinKeahlian > 0) {
                    $alasan[] = 'Keahlian sesuai ('.$cocokKeahlian->take(2)->implode(', ').')';
                }
            }

            // 2. Pengalaman kerja (20 poin)
            if ($pengalamanPelamar !== '') {
                $kataPengalaman = $this->pecahKataKunci($pengalamanPelamar);
                $adaCocok = collect($kataPengalaman)->contains(
                    fn (string $kata): bool => mb_strlen($kata) >= 3 && str_contains($teksLowongan, $kata)
                );
                $poinPengalaman = $adaCocok ? 20 : 10;
                $skor += $poinPengalaman;
                $alasan[] = $adaCocok ? 'Pengalaman kerja relevan' : 'Memiliki riwayat pengalaman';
            }

            // 3. Pendidikan (15 poin)
            if ($pendidikanPelamar !== '') {
                $kataPendidikan = match ($pendidikanPelamar) {
                    's1', 's2', 's3' => ['s1', 'sarjana', 's2', 'magister', 'diploma', 'd3', 'sma'],
                    'diploma' => ['diploma', 'd3', 'd4', 'sma', 'smk'],
                    'sma/smk' => ['sma', 'smk', 'stm', 'slta'],
                    default => [$pendidikanPelamar],
                };
                $cocokPendidikan = collect($kataPendidikan)->contains(fn (string $k): bool => str_contains($teksLowongan, $k));
                $skor += $cocokPendidikan ? 15 : 8;
                if ($cocokPendidikan) {
                    $alasan[] = 'Pendidikan sesuai ('.strtoupper($pendidikanPelamar).')';
                }
            }

            // 4. Lokasi (15 poin)
            if ($lowongan->tipe_kerja === 'Jarak jauh' || str_contains($lokasiLowongan, 'remote') || str_contains($lokasiLowongan, 'jarak jauh')) {
                $skor += 15;
                $alasan[] = 'Mendukung kerja jarak jauh';
            } elseif ($domisiliPelamar !== '' && (str_contains($lokasiLowongan, $domisiliPelamar) || str_contains($domisiliPelamar, $lokasiLowongan))) {
                $skor += 15;
                $alasan[] = 'Lokasi dekat domisili';
            }

            // 5. Kategori pekerjaan (15 poin)
            if ($kategoriDisukaiIds->contains($lowongan->kategori_id)) {
                $skor += 15;
                $alasan[] = 'Kategori diminati ('.$lowongan->kategori->nama.')';
            } elseif ($kategoriDisukaiIds->isEmpty()) {
                $skor += 8;
            }

            if ($alasan === []) {
                $alasan[] = 'Lengkapi profil untuk skor lebih akurat';
            }

            return [
                'lowongan' => $lowongan,
                'skor' => min(100, $skor),
                'alasan' => $alasan,
            ];
        })->sortByDesc('skor')->values();
    }

    /**
     * @return list<string>
     */
    private function pecahKataKunci(string $teks): array
    {
        $bagian = preg_split('/[,;\n]+/', mb_strtolower($teks)) ?: [];

        return collect($bagian)
            ->map(fn (string $item): string => trim($item))
            ->filter(fn (string $item): bool => mb_strlen($item) >= 2)
            ->values()
            ->all();
    }
}
