<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class PendaftaranTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    #[TestWith(['/masuk'])]
    #[TestWith(['/daftar'])]
    public function test_navbar_autentikasi_hanya_memuat_tautan_beranda(string $alamat): void
    {
        $halaman = $this->get($alamat)->assertOk()->assertDontSee('demo', false);
        $dokumen = new \DOMDocument;
        @$dokumen->loadHTML('<?xml encoding="UTF-8">'.$halaman->getContent());
        $navigasi = (new \DOMXPath($dokumen))->query('//header/nav//a');

        $this->assertCount(1, $navigasi);
        $this->assertSame(route('home'), $navigasi->item(0)->getAttribute('href'));
    }

    public function test_profil_pencari_kerja_disimpan_tanpa_data_perusahaan_atau_hak_admin(): void
    {
        $this->post(route('register.store'), [
            ...$this->dataPendaftaran(),
            'domisili' => 'Surabaya', 'pendidikan_terakhir' => 'S1',
            'bidang_usaha' => 'Tidak disimpan',
            'profil_pendaftaran' => ['role' => 'admin'],
        ])->assertRedirectToRoute('dashboard')->assertSessionHasNoErrors();

        $pengguna = User::sole();
        $this->assertSame('user', $pengguna->role);
        $this->assertSame([
            'telepon' => '081234567890', 'domisili' => 'Surabaya', 'pendidikan_terakhir' => 'S1',
        ], $pengguna->profil_pendaftaran);
        $this->assertArrayNotHasKey('profil_pendaftaran', $pengguna->toArray());
    }

    public function test_profil_perusahaan_disimpan_dan_data_pencari_kerja_diabaikan(): void
    {
        $this->post(route('register.store'), [
            ...$this->dataPerusahaan(), 'domisili' => ['tidak valid'], 'pendidikan_terakhir' => 'S1',
        ])->assertRedirectToRoute('dashboard')->assertSessionHasNoErrors();

        $pengguna = User::sole();
        $this->assertSame('perusahaan', $pengguna->role);
        $this->assertSame('Manajer SDM', $pengguna->profil_pendaftaran['jabatan_penanggung_jawab']);
        $this->assertSame('https://contoh.co.id', $pengguna->profil_pendaftaran['situs_perusahaan']);
        $this->assertArrayNotHasKey('domisili', $pengguna->profil_pendaftaran);
        $this->assertArrayNotHasKey('pendidikan_terakhir', $pengguna->profil_pendaftaran);
        $this->assertArrayNotHasKey('password', $pengguna->profil_pendaftaran);
        $this->assertAuthenticatedAs($pengguna);
        $this->get('/perusahaan/dashboard')->assertOk()->assertDontSee('Profil pendaftaran');
        $this->get('/akun')->assertOk()
            ->assertSee('Profil perusahaan')->assertSee('Teknologi informasi')
            ->assertSee('Dewi Lestari')->assertSee('Manajer SDM');
    }

    public function test_perusahaan_tidak_wajib_memiliki_situs(): void
    {
        $data = $this->dataPerusahaan();
        unset($data['situs_perusahaan']);

        $this->post(route('register.store'), $data)->assertRedirectToRoute('dashboard')->assertSessionHasNoErrors();

        $this->assertDatabaseCount('users', 1);
    }

    #[TestWith(['user', ['domisili', 'pendidikan_terakhir']])]
    #[TestWith(['perusahaan', ['bidang_usaha', 'jumlah_karyawan', 'alamat_perusahaan', 'kota_perusahaan', 'deskripsi_perusahaan', 'nama_penanggung_jawab', 'jabatan_penanggung_jawab']])]
    public function test_isian_profil_wajib_sesuai_jenis_akun(string $jenisAkun, array $isianWajib): void
    {
        $this->post(route('register.store'), [...$this->dataPendaftaran(), 'role' => $jenisAkun])
            ->assertSessionHasErrors($isianWajib);

        $this->assertDatabaseCount('users', 0);
        $this->assertGuest();
    }

    #[TestWith(['telepon', 'bukan-telepon', 'Masukkan nomor telepon yang valid (8–25 karakter).'])]
    #[TestWith(['jumlah_karyawan', 'jutaan', 'Pilih Jumlah karyawan yang tersedia.'])]
    #[TestWith(['deskripsi_perusahaan', 'pendek', 'Deskripsi perusahaan minimal 30 karakter.'])]
    #[TestWith(['situs_perusahaan', 'javascript:alert(1)', 'Situs perusahaan harus berupa alamat HTTP atau HTTPS yang valid.'])]
    #[TestWith(['bidang_usaha', ['teknologi'], 'Bidang usaha harus berupa teks.'])]
    #[TestWith(['nama_penanggung_jawab', "Nama\0Berbahaya", 'Nama penanggung jawab tidak boleh mengandung karakter null.'])]
    #[TestWith(['name', ['tidak valid'], 'Nama harus berupa teks.'])]
    public function test_data_perusahaan_tidak_valid_ditolak(string $isian, mixed $nilai, string $pesan): void
    {
        $this->post(route('register.store'), [...$this->dataPerusahaan(), $isian => $nilai])
            ->assertSessionHasErrors([$isian => $pesan]);

        $this->assertDatabaseCount('users', 0);
        $this->assertGuest();
    }

    public function test_pendidikan_yang_tidak_tersedia_ditolak(): void
    {
        $this->post(route('register.store'), [
            ...$this->dataPendaftaran(), 'domisili' => 'Surabaya', 'pendidikan_terakhir' => 'Lainnya',
        ])->assertSessionHasErrors(['pendidikan_terakhir' => 'Pilih Pendidikan terakhir yang tersedia.']);

        $this->assertDatabaseCount('users', 0);
    }

    #[TestWith(['bidang_usaha', 101, 'Bidang usaha maksimal 100 karakter.'])]
    #[TestWith(['alamat_perusahaan', 501, 'Alamat perusahaan maksimal 500 karakter.'])]
    #[TestWith(['kota_perusahaan', 101, 'Kota perusahaan maksimal 100 karakter.'])]
    #[TestWith(['deskripsi_perusahaan', 2001, 'Deskripsi perusahaan maksimal 2000 karakter.'])]
    #[TestWith(['situs_perusahaan', 256, 'Situs perusahaan maksimal 255 karakter.'])]
    #[TestWith(['nama_penanggung_jawab', 256, 'Nama penanggung jawab maksimal 255 karakter.'])]
    #[TestWith(['jabatan_penanggung_jawab', 101, 'Jabatan penanggung jawab maksimal 100 karakter.'])]
    public function test_isian_perusahaan_yang_terlalu_panjang_ditolak(string $isian, int $panjang, string $pesan): void
    {
        $this->post(route('register.store'), [...$this->dataPerusahaan(), $isian => str_repeat('a', $panjang)])
            ->assertSessionHasErrors([$isian => $pesan]);

        $this->assertDatabaseCount('users', 0);
    }

    public function test_kesalahan_mempertahankan_isian_perusahaan_dengan_aman_tanpa_kata_sandi(): void
    {
        $this->followingRedirects()->post(route('register.store'), [
            ...$this->dataPerusahaan(),
            'email' => 'tidak-valid', 'nama_penanggung_jawab' => '<script>alert(1)</script>',
        ])->assertOk()->assertSee('<script>alert(1)</script>')
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertSee('Jalan Merdeka Nomor 10')->assertSee('Manajer SDM')
            ->assertSee('value="perusahaan" required checked', false)
            ->assertDontSee('kata-sandi-aman-2026');

        $this->assertDatabaseCount('users', 0);
    }

    /** @return array<string, string> */
    private function dataPendaftaran(): array
    {
        return [
            'name' => 'Akun Lokerku', 'email' => 'akun@example.com', 'role' => 'user',
            'password' => 'kata-sandi-aman-2026', 'password_confirmation' => 'kata-sandi-aman-2026',
            'telepon' => '081234567890',
        ];
    }

    /** @return array<string, string> */
    private function dataPerusahaan(): array
    {
        return [
            ...$this->dataPendaftaran(), 'role' => 'perusahaan',
            'bidang_usaha' => 'Teknologi informasi', 'jumlah_karyawan' => '11–50',
            'alamat_perusahaan' => 'Jalan Merdeka Nomor 10', 'kota_perusahaan' => 'Surabaya',
            'deskripsi_perusahaan' => 'Perusahaan pengembangan perangkat lunak untuk bisnis Indonesia.',
            'situs_perusahaan' => 'https://contoh.co.id', 'nama_penanggung_jawab' => 'Dewi Lestari',
            'jabatan_penanggung_jawab' => 'Manajer SDM',
        ];
    }
}
