<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lamarans', function (Blueprint $tabel): void {
            $tabel->id();
            $tabel->foreignId('lowongan_id')->constrained()->cascadeOnDelete();
            $tabel->foreignId('pelamar_id')->constrained('users')->cascadeOnDelete();
            $tabel->enum('status', ['Dikirim', 'Ditinjau', 'Diterima', 'Ditolak'])->default('Dikirim');
            $tabel->text('catatan')->nullable();
            $tabel->timestamps();
            $tabel->unique(['lowongan_id', 'pelamar_id']);
            $tabel->index(['pelamar_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lamarans');
    }
};
