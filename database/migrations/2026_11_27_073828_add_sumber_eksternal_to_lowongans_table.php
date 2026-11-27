<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lowongans', function (Blueprint $tabel): void {
            $tabel->string('sumber', 30)->default('internal')->after('aktif');
            $tabel->string('eksternal_id', 100)->nullable()->unique()->after('sumber');
            $tabel->string('sumber_url', 500)->nullable()->after('eksternal_id');
            $tabel->unsignedBigInteger('gaji_min')->nullable()->change();
            $tabel->unsignedBigInteger('gaji_max')->nullable()->change();
            $tabel->date('batas_lamaran')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('lowongans', function (Blueprint $tabel): void {
            $tabel->dropUnique(['eksternal_id']);
            $tabel->dropColumn(['sumber', 'eksternal_id', 'sumber_url']);
        });
    }
};
