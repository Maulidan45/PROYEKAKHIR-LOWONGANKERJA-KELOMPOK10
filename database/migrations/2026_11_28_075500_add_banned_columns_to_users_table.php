<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->boolean('is_banned')->default(false)->after('foto_profil_path');
            $table->timestamp('dibanned_pada')->nullable()->after('is_banned');
            $table->text('alasan_ban')->nullable()->after('dibanned_pada');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn(['is_banned', 'dibanned_pada', 'alasan_ban']);
        });
    }
};
