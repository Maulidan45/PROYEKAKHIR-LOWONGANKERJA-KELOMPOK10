<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        foreach (['kategoris', 'lowongans', 'lamarans'] as $tabel) {
            DB::statement('alter table "'.$tabel.'" enable row level security');
            DB::statement('revoke all on table "'.$tabel.'" from anon, authenticated');
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            throw new LogicException('Perubahan keamanan hanya dapat diperbaiki dengan migrasi baru.');
        }
    }
};
