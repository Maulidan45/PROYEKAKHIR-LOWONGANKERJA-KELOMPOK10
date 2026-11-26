<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class LowonganEksternalTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_perintah_sinkron_menyimpan_lowongan_dari_api_remotive_secara_idempoten(): void
    {
        Http::fake([
            '*/remote-jobs*' => Http::response([
                'jobs' => [
                    [
                        'title' => 'Senior Laravel Engineer',
                        'company_name' => 'Acme Global Tech',
                        'category' => 'Software Development',
                        'job_type' => 'full_time',
                        'candidate_required_location' => 'Worldwide',
                        'url' => 'https://remotive.com/remote-jobs/software-dev/senior-laravel-engineer-1',
                        'description' => '<p>Membangun layanan backend berskala besar.</p>',
                    ],
                    [
                        'title' => 'Product Designer',
                        'company_name' => 'Studio Kreatif',
                        'category' => 'Design',
                        'job_type' => 'part_time',
                        'candidate_required_location' => 'APAC',
                        'url' => 'https://remotive.com/remote-jobs/design/product-designer-2',
                        'description' => '<p>Merancang pengalaman pengguna aplikasi web.</p>',
                    ],
                ],
            ]),
        ]);

        $this->artisan('lowongan:sinkron', ['--limit' => 2])->assertExitCode(0);
        $this->artisan('lowongan:sinkron', ['--limit' => 2])->assertExitCode(0);

        $this->assertDatabaseCount('lowongans', 2);
        $this->assertDatabaseHas('lowongans', [
            'judul' => 'Senior Laravel Engineer',
            'lokasi' => 'Worldwide',
            'tipe_kerja' => 'Penuh waktu',
        ]);
        $this->assertDatabaseHas('lowongans', [
            'judul' => 'Product Designer',
            'tipe_kerja' => 'Paruh waktu',
        ]);
    }

    public function test_perintah_sinkron_menangani_kegagalan_api_dengan_aman(): void
    {
        Http::fake([
            '*' => Http::response(null, 500),
        ]);

        $this->artisan('lowongan:sinkron', ['--limit' => 5])->assertExitCode(1);
        $this->assertDatabaseCount('lowongans', 0);
    }
}
