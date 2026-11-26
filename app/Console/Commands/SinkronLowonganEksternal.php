<?php

namespace App\Console\Commands;

use App\Services\LowonganEksternalService;
use Illuminate\Console\Command;
use Throwable;

class SinkronLowonganEksternal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lowongan:sinkron {--limit=20 : Jumlah maksimal lowongan yang ditarik dari API Remotive}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tarik lowongan kerja nyata dari API publik Remotive ke database lokal';

    /**
     * Execute the console command.
     */
    public function handle(LowonganEksternalService $layanan): int
    {
        $batas = (int) $this->option('limit');

        try {
            $jumlah = $layanan->sinkronDariRemotive($batas);
            $this->info("Berhasil menyinkronkan {$jumlah} lowongan kerja dari API Remotive.");

            return self::SUCCESS;
        } catch (Throwable $pengecualian) {
            $this->error('Gagal menarik data dari API Remotive: '.$pengecualian->getMessage());

            return self::FAILURE;
        }
    }
}
