<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $tabel): void {
            $tabel->json('profil_pendaftaran')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $tabel): void {
            $tabel->dropColumn('profil_pendaftaran');
        });
    }
};
