<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lowongans', function (Blueprint $table): void {
            $table->timestamp('iklan_sampai')->nullable()->after('sumber_url');
            $table->string('iklan_paket', 50)->nullable()->after('iklan_sampai');
        });
    }

    public function down(): void
    {
        Schema::table('lowongans', function (Blueprint $table): void {
            $table->dropColumn(['iklan_sampai', 'iklan_paket']);
        });
    }
};
