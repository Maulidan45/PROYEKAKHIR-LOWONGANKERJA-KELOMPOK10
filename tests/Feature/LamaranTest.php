<?php

namespace Tests\Feature;

use App\Models\Lamaran;
use App\Models\Lowongan;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LamaranTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    /**
     * @return array<string, mixed>
     */
    private function payloadLamaran(User $pelamar): array
    {
        return [
            'nama_lengkap' => $pelamar->name,
            'email' => $pelamar->email,
            'telepon' => '081234567890',
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir' => '1999-05-10',
            'jenis_kelamin' => 'Laki-laki',
            'domisili' => 'Jakarta Selatan',
            'pendidikan_terakhir' => 'S1',
            'pengalaman_kerja' => '2 tahun sebagai Backend Developer',
            'keahlian' => 'PHP, Laravel, MySQL',
            'catatan' => 'Siap bergabung secepatnya.',
            'cv' => UploadedFile::fake()->create('cv.pdf', 200, 'application/pdf'),
            'surat_lamaran' => UploadedFile::fake()->create('surat.pdf', 150, 'application/pdf'),
            'ijazah' => UploadedFile::fake()->create('ijazah.pdf', 300, 'application/pdf'),
        ];
    }

    public function test_pencari_kerja_dapat_melamar_lowongan_aktif_satu_kali(): void
    {
        Storage::fake('local');
        $pencariKerja = User::factory()->create(['role' => 'user']);
        $lowongan = Lowongan::factory()->create();

        $tanggapan = $this->actingAs($pencariKerja)->post(route('lamaran.store', $lowongan), $this->payloadLamaran($pencariKerja));

        $lamaran = Lamaran::sole();
        $tanggapan->assertRedirectToRoute('lamaran.sukses', $lamaran)->assertSessionHasNoErrors();

        $this->assertDatabaseHas('lamarans', [
            'lowongan_id' => $lowongan->id,
            'pelamar_id' => $pencariKerja->id,
            'status' => 'Lamaran Terkirim',
        ]);
        $this->get(route('lamaran.index'))->assertOk()->assertSee($lowongan->judul);
    }

    public function test_pencari_kerja_tidak_dapat_melamar_lowongan_yang_sama_dua_kali(): void
    {
        Storage::fake('local');
        $pencariKerja = User::factory()->create(['role' => 'user']);
        $lowongan = Lowongan::factory()->create();
        Lamaran::factory()->create(['pelamar_id' => $pencariKerja->id, 'lowongan_id' => $lowongan->id]);

        $this->actingAs($pencariKerja)->post(route('lamaran.store', $lowongan), $this->payloadLamaran($pencariKerja))
            ->assertSessionHasErrors(['lamaran' => 'Anda sudah melamar lowongan ini.']);

        $this->assertDatabaseCount('lamarans', 1);
    }

    public function test_menu_pencari_kerja_mengarahkan_lamaran_dan_akun_ke_halaman_yang_diizinkan(): void
    {
        $pencariKerja = User::factory()->create(['role' => 'user']);

        $tautanAkun = route('akun.edit');
        $responsLamaran = $this->actingAs($pencariKerja)->get(route('lamaran.index'))
            ->assertOk()
            ->assertSee('Riwayat Lamaran')
            ->assertSee('class="rounded-2xl border border-dashed border-slate-300', false)
            ->assertSee('>Dashboard</span>', false)
            ->assertSee('href="'.route('lamaran.index').'"', false)
            ->assertSee($tautanAkun, false);

        $this->assertDoesNotMatchRegularExpression('/href="'.preg_quote($tautanAkun, '/').'"\s+aria-current="page"/', $responsLamaran->getContent());
        $this->assertMatchesRegularExpression('/href="'.preg_quote(route('lamaran.index'), '/').'"[^>]*aria-current="page"/', $responsLamaran->getContent());

        $responsDasbor = $this->get(route('dashboard.role', ['role' => 'user']))
            ->assertOk()
            ->assertSee('>Dashboard</span>', false)
            ->assertSee($tautanAkun, false);

        $responsDasbor->assertSee('aria-current="page"', false);

        $this->actingAs($pencariKerja)->get(route('akun.edit'))
            ->assertOk()
            ->assertSee('Profil Pencari Kerja')
            ->assertSee($pencariKerja->name)
            ->assertSee($pencariKerja->email)
            ->assertSee('href="'.route('akun.edit').'"', false)
            ->assertSee('aria-current="page"', false);
    }

    public function test_perusahaan_pemilik_dapat_memperbarui_status_pelamar(): void
    {
        $perusahaan = User::factory()->create(['role' => 'perusahaan']);
        $lowongan = Lowongan::factory()->create(['perusahaan_id' => $perusahaan->id]);
        $lamaran = Lamaran::factory()->create(['lowongan_id' => $lowongan->id, 'status' => 'Lamaran Terkirim']);

        $this->actingAs($perusahaan)->patch(route('lamaran.update', $lamaran), [
            'status' => 'Seleksi Administrasi',
            'catatan' => 'Berkas lengkap, lanjut administrasi.',
        ])->assertRedirect()->assertSessionHasNoErrors();

        $this->assertDatabaseHas('lamarans', ['id' => $lamaran->id, 'status' => 'Seleksi Administrasi']);
        $this->assertDatabaseHas('riwayat_lamarans', [
            'lamaran_id' => $lamaran->id,
            'status_sebelumnya' => 'Lamaran Terkirim',
            'status_baru' => 'Seleksi Administrasi',
            'diubah_oleh' => $perusahaan->id,
        ]);
    }
}
