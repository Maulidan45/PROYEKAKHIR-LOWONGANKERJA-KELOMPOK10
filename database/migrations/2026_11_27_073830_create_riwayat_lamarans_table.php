<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_lamarans', function (Blueprint $tabel): void {
            $tabel->id();
            $tabel->foreignId('lamaran_id')->constrained('lamarans')->cascadeOnDelete();
            $tabel->string('status_sebelumnya', 40)->nullable();
            $tabel->string('status_baru', 40);
            $tabel->text('catatan')->nullable();
            $tabel->foreignId('diubah_oleh')->nullable()->constrained('users')->nullOnDelete();
            $tabel->timestamps();
            $tabel->index(['lamaran_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_lamarans');
    }
};
