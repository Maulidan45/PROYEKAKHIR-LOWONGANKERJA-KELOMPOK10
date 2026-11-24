<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lowongans', function (Blueprint $tabel): void {
            $tabel->id();
            $tabel->foreignId('kategori_id')->constrained('kategoris')->restrictOnDelete();
            $tabel->foreignId('perusahaan_id')->constrained('users')->cascadeOnDelete();
            $tabel->string('judul', 120);
            $tabel->string('lokasi', 100);
            $tabel->enum('tipe_kerja', ['Penuh waktu', 'Paruh waktu', 'Magang', 'Jarak jauh']);
            $tabel->unsignedBigInteger('gaji_min');
            $tabel->unsignedBigInteger('gaji_max');
            $tabel->date('batas_lamaran');
            $tabel->text('deskripsi');
            $tabel->boolean('aktif')->default(true);
            $tabel->timestamps();
            $tabel->index(['perusahaan_id', 'aktif']);
            $tabel->index(['aktif', 'batas_lamaran']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lowongans');
    }
};
