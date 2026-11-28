<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;

return new class extends Migration
{
    public function up(): void
    {
        $wajib = [
            'bentuk_badan_usaha',
            'tahun_berdiri',
            'nib_npwp',
            'telepon',
            'email_hrd',
            'bidang_usaha',
            'jumlah_karyawan',
            'provinsi_perusahaan',
            'kota_perusahaan',
            'kode_pos',
            'alamat_perusahaan',
            'deskripsi_perusahaan',
            'visi_misi',
            'nama_penanggung_jawab',
            'jabatan_penanggung_jawab',
        ];

        User::where('role', 'perusahaan')->chunkById(50, function ($perusahaans) use ($wajib): void {
            foreach ($perusahaans as $pt) {
                $profil = $pt->profil_pendaftaran ?? [];
                $lengkap = filled($pt->foto_profil_path) && filled($pt->dokumen_legalitas_path);

                foreach ($wajib as $bidang) {
                    if (blank($profil[$bidang] ?? null)) {
                        $lengkap = false;
                        break;
                    }
                }

                if (! $lengkap) {
                    $pt->status_verifikasi = 'Belum Diajukan';
                    $pt->diverifikasi_pada = null;
                    $pt->save();
                }
            }
        });

        Cache::forget('admin:menunggu_verifikasi_count');
    }

    public function down(): void
    {
        // Tidak ada rollback otomatis untuk status verifikasi perusahaan yang belum lengkap.
    }
};
