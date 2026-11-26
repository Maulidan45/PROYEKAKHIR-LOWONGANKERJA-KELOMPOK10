<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lamarans', function (Blueprint $table): void {
            $table->string('cv_path')->nullable()->after('catatan');
        });
    }

    public function down(): void
    {
        Schema::table('lamarans', function (Blueprint $table): void {
            $table->dropColumn('cv_path');
        });
    }
};
