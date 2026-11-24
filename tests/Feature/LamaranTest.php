<?php

namespace Tests\Feature;

use App\Models\Lamaran;
use App\Models\Lowongan;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class LamaranTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_pencari_kerja_dapat_melamar_lowongan_aktif_satu_kali(): void
    {
        $pencariKerja = User::factory()->create(['role' => 'user']);
        $lowongan = Lowongan::factory()->create();

        $this->actingAs($pencariKerja)->post(route('lamaran.store', $lowongan))
            ->assertRedirectToRoute('lamaran.index')->assertSessionHasNoErrors();

        $this->assertDatabaseHas('lamarans', ['lowongan_id' => $lowongan->id, 'pelamar_id' => $pencariKerja->id, 'status' => 'Dikirim']);
        $this->get(route('lamaran.index'))->assertOk()->assertSee($lowongan->judul);
    }

    public function test_pencari_kerja_tidak_dapat_melamar_lowongan_yang_sama_dua_kali(): void
    {
        $pencariKerja = User::factory()->create(['role' => 'user']);
        $lowongan = Lowongan::factory()->create();
        Lamaran::factory()->create(['pelamar_id' => $pencariKerja->id, 'lowongan_id' => $lowongan->id]);

        $this->actingAs($pencariKerja)->post(route('lamaran.store', $lowongan))
            ->assertSessionHasErrors(['lamaran' => 'Kamu sudah melamar lowongan ini.']);

        $this->assertDatabaseCount('lamarans', 1);
    }

    public function test_menu_pencari_kerja_mengarahkan_lamaran_dan_akun_ke_halaman_yang_diizinkan(): void
    {
        $pencariKerja = User::factory()->create(['role' => 'user']);

        $tautanAkun = route('akun.edit');
        $responsLamaran = $this->actingAs($pencariKerja)->get(route('lamaran.index'))
            ->assertOk()
            ->assertSee('Lamaran saya')
            ->assertDontSee('Pencari kerja')
            ->assertDontSee('Pantau status setiap lowongan yang sudah kamu lamar.')
            ->assertSee('class="rounded-lg border border-dashed border-slate-300', false)
            ->assertSee('>Dashboard</span>', false)
            ->assertSee('href="'.route('lamaran.index').'"', false)
            ->assertSee($tautanAkun, false)
            ->assertDontSee('>Cari lowongan</span>', false);

        $this->assertDoesNotMatchRegularExpression('/href="'.preg_quote($tautanAkun, '/').'"\s+aria-current="page"/', $responsLamaran->getContent());
        $this->assertMatchesRegularExpression('/href="'.preg_quote(route('lamaran.index'), '/').'"[^>]*aria-current="page"/', $responsLamaran->getContent());

        $responsDasbor = $this->get(route('dashboard.role', ['role' => 'user']))
            ->assertOk()
            ->assertSee('>Dashboard</span>', false)
            ->assertSee($tautanAkun, false)
            ->assertDontSee('Informasi akun');

        $responsDasbor->assertSee('aria-current="page"', false);

        $this->actingAs($pencariKerja)->get(route('akun.edit'))
            ->assertOk()
            ->assertSee('Akun saya')
            ->assertSee($pencariKerja->name)
            ->assertSee($pencariKerja->email)
            ->assertSee('href="'.route('akun.edit').'"', false)
            ->assertSee(route('akun.edit'), false)
            ->assertSee('aria-current="page"', false)
            ->assertDontSee(route('dashboard.role', ['role' => 'user']).'#akun-saya', false);
    }

    public function test_perusahaan_pemilik_dapat_memperbarui_status_pelamar(): void
    {
        $perusahaan = User::factory()->create(['role' => 'perusahaan']);
        $lowongan = Lowongan::factory()->create(['perusahaan_id' => $perusahaan->id]);
        $lamaran = Lamaran::factory()->create(['lowongan_id' => $lowongan->id]);

        $this->actingAs($perusahaan)->patch(route('lamaran.update', $lamaran), ['status' => 'Ditinjau'])
            ->assertRedirect()->assertSessionHasNoErrors();

        $this->assertDatabaseHas('lamarans', ['id' => $lamaran->id, 'status' => 'Ditinjau']);
    }
}
