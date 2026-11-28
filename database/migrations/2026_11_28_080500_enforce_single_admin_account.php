<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        User::where('role', 'admin')
            ->where('email', '!=', 'admin@lokerku.id')
            ->delete();

        $admin = User::where('email', 'admin@lokerku.id')->first();
        if (! $admin) {
            User::create([
                'name' => 'Admin Lokerku',
                'email' => 'admin@lokerku.id',
                'password' => Hash::make('admin12345678'),
                'role' => 'admin',
                'status_verifikasi' => 'Terverifikasi',
                'email_verified_at' => now(),
            ]);
        } elseif ($admin->role !== 'admin') {
            $admin->role = 'admin';
            $admin->save();
        }

        if (DB::getDriverName() === 'pgsql') {
            DB::statement("CREATE UNIQUE INDEX IF NOT EXISTS users_single_admin_unique ON users (role) WHERE role = 'admin'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('DROP INDEX IF EXISTS users_single_admin_unique');
        }
    }
};
