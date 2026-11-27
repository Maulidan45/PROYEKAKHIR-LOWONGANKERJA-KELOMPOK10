<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $tabel): void {
            $tabel->string('status_verifikasi', 30)->default('Terverifikasi')->after('role');
            $tabel->string('dokumen_legalitas_path')->nullable()->after('status_verifikasi');
            $tabel->text('catatan_verifikasi')->nullable()->after('dokumen_legalitas_path');
            $tabel->timestamp('diverifikasi_pada')->nullable()->after('catatan_verifikasi');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $tabel): void {
            $tabel->dropColumn(['status_verifikasi', 'dokumen_legalitas_path', 'catatan_verifikasi', 'diverifikasi_pada']);
        });
    }
};
