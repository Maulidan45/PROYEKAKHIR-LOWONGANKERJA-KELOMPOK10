<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_wawancaras', function (Blueprint $tabel): void {
            $tabel->id();
            $tabel->foreignId('lamaran_id')->constrained('lamarans')->cascadeOnDelete();
            $tabel->foreignId('perusahaan_id')->constrained('users')->cascadeOnDelete();
            $tabel->foreignId('pelamar_id')->constrained('users')->cascadeOnDelete();
            $tabel->date('tanggal');
            $tabel->string('jam', 10);
            $tabel->string('tipe', 30)->default('Online');
            $tabel->string('lokasi_atau_tautan', 500);
            $tabel->text('catatan')->nullable();
            $tabel->string('status', 30)->default('Dijadwalkan');
            $tabel->timestamps();
            $tabel->index(['perusahaan_id', 'tanggal']);
            $tabel->index(['pelamar_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_wawancaras');
    }
};
