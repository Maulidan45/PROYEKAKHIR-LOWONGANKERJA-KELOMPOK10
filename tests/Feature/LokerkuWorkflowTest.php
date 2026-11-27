<?php

namespace Tests\Feature;

use App\Models\Kategori;
use App\Models\Lamaran;
use App\Models\Lowongan;
use App\Models\User;
use App\Services\JobMatchingService;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LokerkuWorkflowTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_pengguna_yang_sudah_login_diarahkan_keluar_dari_landing_page(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->get('/')->assertRedirect(route('dashboard.role', ['role' => 'admin']));

        $pelamar = User::factory()->create(['role' => 'user']);
        $this->actingAs($pelamar)->get('/')->assertRedirect(route('dashboard.role', ['role' => 'user']));
    }

    public function test_formulir_lamaran_menyimpan_snapshot_immutabel_dokumen_dan_notifikasi(): void
    {
        Storage::fake('local');
        $perusahaan = User::factory()->create(['role' => 'perusahaan']);
        $pelamar = User::factory()->create([
            'role' => 'user',
            'name' => 'Budi Santoso',
            'profil_pendaftaran' => ['telepon' => '081234567890', 'domisili' => 'Bandung'],
        ]);
        $lowongan = Lowongan::factory()->create(['perusahaan_id' => $perusahaan->id]);

        $this->actingAs($pelamar)->get(route('lamaran.create', $lowongan))
            ->assertOk()
            ->assertSee('Data Pribadi Pelamar')
            ->assertSee('Dokumen Persyaratan')
            ->assertSee('Budi Santoso');

        $this->post(route('lamaran.store', $lowongan), [
            'nama_lengkap' => 'Budi Santoso',
            'email' => $pelamar->email,
            'telepon' => '081234567890',
            'tempat_lahir' => 'Bandung',
            'tanggal_lahir' => '1998-04-12',
            'jenis_kelamin' => 'Laki-laki',
            'domisili' => 'Kota Bandung',
            'pendidikan_terakhir' => 'S1',
            'pengalaman_kerja' => '3 tahun sebagai QA Engineer',
            'keahlian' => 'PHPUnit, Selenium, Laravel',
            'catatan' => 'Siap wawancara kapan saja.',
            'cv' => UploadedFile::fake()->create('cv.pdf', 150, 'application/pdf'),
            'surat_lamaran' => UploadedFile::fake()->create('surat.pdf', 120, 'application/pdf'),
            'ijazah' => UploadedFile::fake()->create('ijazah.jpg', 200, 'image/jpeg'),
        ])->assertRedirect();

        $lamaran = Lamaran::sole();
        $this->assertSame('Budi Santoso', $lamaran->data_pelamar['nama_lengkap']);
        $this->assertSame('Kota Bandung', $lamaran->data_pelamar['domisili']);
        Storage::disk('local')->assertExists($lamaran->cv_path);
        Storage::disk('local')->assertExists($lamaran->surat_lamaran_path);
        Storage::disk('local')->assertExists($lamaran->ijazah_path);

        // Snapshot tidak ikut berubah ketika pelamar mengubah profilnya nanti
        $this->patch(route('akun.profil.update'), [
            'name' => 'Budi Berubah',
            'domisili' => 'Surabaya',
        ])->assertRedirect();

        $lamaran->refresh();
        $this->assertSame('Budi Santoso', $lamaran->data_pelamar['nama_lengkap']);
        $this->assertSame('Kota Bandung', $lamaran->data_pelamar['domisili']);

        // Notifikasi terkirim ke pelamar dan perusahaan
        $this->assertGreaterThanOrEqual(1, $pelamar->notifications()->count());
        $this->assertGreaterThanOrEqual(1, $perusahaan->notifications()->count());
    }

    public function test_akses_dokumen_lamaran_terbatas_hanya_pelamar_dan_perusahaan_pemilik(): void
    {
        Storage::fake('local');
        $perusahaanPemilik = User::factory()->create(['role' => 'perusahaan']);
        $perusahaanLain = User::factory()->create(['role' => 'perusahaan']);
        $admin = User::factory()->create(['role' => 'admin']);
        $pelamar = User::factory()->create(['role' => 'user']);
        $pelamarLain = User::factory()->create(['role' => 'user']);

        $lowongan = Lowongan::factory()->create(['perusahaan_id' => $perusahaanPemilik->id]);
        $pathCv = UploadedFile::fake()->create('cv.pdf', 100, 'application/pdf')->store('lamaran-dokumen/test', 'local');
        $lamaran = Lamaran::factory()->create([
            'lowongan_id' => $lowongan->id,
            'pelamar_id' => $pelamar->id,
            'cv_path' => $pathCv,
        ]);

        $this->actingAs($pelamar)->get(route('lamaran.dokumen', ['lamaran' => $lamaran, 'jenis' => 'cv']))->assertOk();
        $this->actingAs($perusahaanPemilik)->get(route('lamaran.dokumen', ['lamaran' => $lamaran, 'jenis' => 'cv']))->assertOk();
        $this->actingAs($pelamarLain)->get(route('lamaran.dokumen', ['lamaran' => $lamaran, 'jenis' => 'cv']))->assertForbidden();
        $this->actingAs($perusahaanLain)->get(route('lamaran.dokumen', ['lamaran' => $lamaran, 'jenis' => 'cv']))->assertForbidden();
        $this->actingAs($admin)->get(route('lamaran.dokumen', ['lamaran' => $lamaran, 'jenis' => 'cv']))->assertForbidden();
    }

    public function test_verifikasi_perusahaan_mengontrol_hak_publikasi_lowongan(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $perusahaan = User::factory()->create([
            'role' => 'perusahaan',
            'status_verifikasi' => 'Menunggu Verifikasi',
        ]);
        $kategori = Kategori::factory()->create();

        $this->actingAs($perusahaan)->post(route('lowongan.store'), [
            'kategori_id' => $kategori->id,
            'judul' => 'Frontend Engineer',
            'lokasi' => 'Jakarta',
            'tipe_kerja' => 'Penuh waktu',
            'gaji_min' => 6000000,
            'gaji_max' => 9000000,
            'batas_lamaran' => now()->addMonth()->toDateString(),
            'deskripsi' => 'Membangun antarmuka pengguna modern dan responsif dengan aksesibilitas tinggi.',
            'aktif' => '1',
        ])->assertForbidden();

        $this->actingAs($admin)->patch(route('admin.verifikasi.update', $perusahaan), [
            'status_verifikasi' => 'Terverifikasi',
            'catatan_verifikasi' => 'Dokumen NIB valid.',
        ])->assertRedirect();

        $perusahaan->refresh();
        $this->assertSame('Terverifikasi', $perusahaan->status_verifikasi);
        $this->assertSame(1, $perusahaan->notifications()->count());

        $this->actingAs($perusahaan)->post(route('lowongan.store'), [
            'kategori_id' => $kategori->id,
            'judul' => 'Frontend Engineer',
            'lokasi' => 'Jakarta',
            'tipe_kerja' => 'Penuh waktu',
            'gaji_min' => 6000000,
            'gaji_max' => 9000000,
            'batas_lamaran' => now()->addMonth()->toDateString(),
            'deskripsi' => 'Membangun antarmuka pengguna modern dan responsif dengan aksesibilitas tinggi.',
            'aktif' => '1',
        ])->assertRedirect(route('lowongan.index'));
    }

    public function test_perusahaan_dapat_membuat_jadwal_wawancara_dan_mengirim_notifikasi(): void
    {
        $perusahaan = User::factory()->create(['role' => 'perusahaan']);
        $pelamar = User::factory()->create(['role' => 'user']);
        $lowongan = Lowongan::factory()->create(['perusahaan_id' => $perusahaan->id]);
        $lamaran = Lamaran::factory()->create([
            'lowongan_id' => $lowongan->id,
            'pelamar_id' => $pelamar->id,
            'status' => 'Seleksi Berkas',
        ]);

        $this->actingAs($perusahaan)->post(route('wawancara.store', $lamaran), [
            'tanggal' => now()->addDays(3)->toDateString(),
            'jam' => '10:00',
            'tipe' => 'Online',
            'lokasi_atau_tautan' => 'https://meet.google.com/abc-defg-hij',
            'catatan' => 'Mohon hadir 10 menit lebih awal.',
        ])->assertRedirect()->assertSessionHasNoErrors();

        $lamaran->refresh();
        $this->assertSame('Wawancara', $lamaran->status);
        $this->assertDatabaseHas('jadwal_wawancaras', [
            'lamaran_id' => $lamaran->id,
            'jam' => '10:00',
            'tipe' => 'Online',
        ]);
        $this->assertGreaterThanOrEqual(1, $pelamar->notifications()->count());

        $this->actingAs($pelamar)->get(route('wawancara.index'))
            ->assertOk()
            ->assertSee('https://meet.google.com/abc-defg-hij');
    }

    public function test_job_matching_memberikan_skor_lebih_tinggi_pada_lowongan_yang_cocok(): void
    {
        $pelamar = User::factory()->create([
            'role' => 'user',
            'profil_pendaftaran' => [
                'keahlian' => 'Laravel, PHP, MySQL',
                'pengalaman_kerja' => '2 tahun backend developer',
                'pendidikan_terakhir' => 'S1',
                'domisili' => 'Bandung',
            ],
        ]);
        $kategori = Kategori::factory()->create(['nama' => 'Teknologi Informasi']);
        $cocok = Lowongan::factory()->create([
            'kategori_id' => $kategori->id,
            'judul' => 'Backend Laravel Developer',
            'lokasi' => 'Kota Bandung',
            'deskripsi' => 'Dicari lulusan S1 dengan keahlian Laravel, PHP, dan MySQL.',
        ]);
        $kurangCocok = Lowongan::factory()->create([
            'kategori_id' => $kategori->id,
            'judul' => 'Chef Pastry',
            'lokasi' => 'Medan',
            'deskripsi' => 'Membuat roti dan kue di dapur hotel.',
        ]);

        $hasil = app(JobMatchingService::class)->hitungRekomendasi($pelamar, collect([$kurangCocok, $cocok]));

        $this->assertSame($cocok->id, $hasil->first()['lowongan']->id);
        $this->assertGreaterThan($hasil->last()['skor'], $hasil->first()['skor']);
    }
}
