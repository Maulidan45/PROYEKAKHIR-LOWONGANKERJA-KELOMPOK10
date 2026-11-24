<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:promote-admin {email : Email akun terdaftar yang akan dijadikan admin}')]
#[Description('Jadikan akun yang sudah ada sebagai admin melalui akses server')]
class PromoteAdmin extends Command
{
    /**
     * Jalankan perintah server.
     */
    public function handle(): int
    {
        $pengguna = User::where('email', strtolower(trim($this->argument('email'))))->first();

        if (! $pengguna) {
            $this->error('Akun tidak ditemukan. Daftarkan akun terlebih dahulu.');

            return self::FAILURE;
        }

        $pengguna->role = 'admin';
        $pengguna->save();
        $this->info('Akun berhasil dijadikan admin.');

        return self::SUCCESS;
    }
}
