<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        foreach (['users', 'password_reset_tokens', 'sessions', 'cache', 'cache_locks', 'jobs', 'job_batches', 'failed_jobs', 'migrations'] as $table) {
            DB::statement('alter table "'.$table.'" enable row level security');
            DB::statement('revoke all on table "'.$table.'" from anon, authenticated');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            throw new LogicException('Security hardening is forward-only; review database access before changing RLS or grants.');
        }
    }
};
