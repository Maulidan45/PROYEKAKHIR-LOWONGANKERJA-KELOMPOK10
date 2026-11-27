<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lamarans', function (Blueprint $tabel): void {
            $tabel->string('status', 40)->default('Lamaran Terkirim')->change();
            $tabel->json('data_pelamar')->nullable()->after('catatan');
            $tabel->string('surat_lamaran_path')->nullable()->after('cv_path');
            $tabel->string('ijazah_path')->nullable()->after('surat_lamaran_path');
            $tabel->string('portofolio_path')->nullable()->after('ijazah_path');
            $tabel->string('sertifikat_path')->nullable()->after('portofolio_path');
        });

        DB::table('lamarans')->where('status', 'Dikirim')->update(['status' => 'Lamaran Terkirim']);
        DB::table('lamarans')->where('status', 'Ditinjau')->update(['status' => 'Seleksi Administrasi']);
    }

    public function down(): void
    {
        DB::table('lamarans')->where('status', 'Lamaran Terkirim')->update(['status' => 'Dikirim']);
        DB::table('lamarans')->whereIn('status', ['Seleksi Administrasi', 'Seleksi Berkas', 'Wawancara'])->update(['status' => 'Ditinjau']);

        Schema::table('lamarans', function (Blueprint $tabel): void {
            $tabel->dropColumn(['data_pelamar', 'surat_lamaran_path', 'ijazah_path', 'portofolio_path', 'sertifikat_path']);
        });
    }
};
