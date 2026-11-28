<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporans', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('pelapor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('terlapor_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('lowongan_id')->nullable()->constrained('lowongans')->nullOnDelete();
            $table->string('jenis', 20);
            $table->string('kategori_laporan', 100);
            $table->text('deskripsi');
            $table->string('status', 30)->default('baru');
            $table->text('catatan_admin')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporans');
    }
};
