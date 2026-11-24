<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AkunTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_perusahaan_can_open_account_page_and_navbar_menu_links_to_dashboard(): void
    {
        $perusahaan = User::factory()->create(['role' => 'perusahaan']);

        $tanggapan = $this->actingAs($perusahaan)->get(route('akun.edit'));

        $tanggapan->assertOk()
            ->assertSee('action="'.route('logout').'"', false)
            ->assertSee(route('akun.edit'), false)->assertSee('>Dashboard</span>', false)
            ->assertSee('>Akun</span>', false)->assertDontSee('>Email</span>', false)
            ->assertDontSee('Pengaturan perusahaan')
            ->assertDontSee('Kelola profil, email, dan keamanan akun perusahaan Anda.')
            ->assertDontSee('Kembali ke dasbor')
            ->assertSee('aria-label="Bagian pengaturan akun"', false)
            ->assertSee('href="#profil-perusahaan"', false)->assertSee('href="#email-akun"', false)
            ->assertSee('href="#keamanan-akun"', false)->assertSee('href="#keluar-akun"', false)
            ->assertSee('Profil perusahaan')->assertSee('Email akun')->assertSee('Kata sandi')->assertSee('Keluar dari akun')
            ->assertSee('id="profil-perusahaan"', false)
            ->assertDontSee('<h1 class="mb-6 text-3xl font-bold tracking-tight text-slate-900', false)
            ->assertDontSee('<span class="label-akun">Profil</span>', false);

        $this->assertMatchesRegularExpression('/<section id="email-akun"[^>]*hidden/', $tanggapan->getContent());
        $this->assertMatchesRegularExpression('/<section id="keamanan-akun"[^>]*hidden/', $tanggapan->getContent());
        $this->assertMatchesRegularExpression('/<section id="keluar-akun"[^>]*hidden/', $tanggapan->getContent());
    }

    public function test_perusahaan_can_update_company_profile(): void
    {
        $perusahaan = User::factory()->create(['role' => 'perusahaan', 'profil_pendaftaran' => []]);

        $this->actingAs($perusahaan)->patch(route('akun.profil.update'), $this->dataProfil())
            ->assertRedirect(route('akun.edit'))->assertSessionHas('status', 'Profil perusahaan berhasil diperbarui.');

        $perusahaan->refresh();
        $this->assertSame('Lokerku Teknologi', $perusahaan->name);
        $this->assertSame('081234567890', $perusahaan->profil_pendaftaran['telepon']);
        $this->assertSame('https://lokerku.example', $perusahaan->profil_pendaftaran['situs_perusahaan']);
    }

    public function test_company_profile_rejects_invalid_phone_without_saving(): void
    {
        $perusahaan = User::factory()->create(['role' => 'perusahaan', 'name' => 'Nama Lama', 'profil_pendaftaran' => []]);

        $this->actingAs($perusahaan)->patch(route('akun.profil.update'), [...$this->dataProfil(), 'telepon' => 'invalid'])
            ->assertSessionHasErrors(['telepon' => 'Masukkan nomor telepon yang valid (8–25 karakter).']);

        $this->assertDatabaseHas('users', ['id' => $perusahaan->id, 'name' => 'Nama Lama']);
    }

    public function test_perusahaan_can_update_email_after_confirming_current_password(): void
    {
        $perusahaan = User::factory()->create(['role' => 'perusahaan', 'email' => 'lama@example.com']);

        $this->actingAs($perusahaan)->patch(route('akun.email.update'), [
            'email' => ' Baru@Example.com ',
            'current_password_email' => 'password',
        ])->assertRedirect(route('akun.edit'))->assertSessionHas('status', 'Email berhasil diperbarui.');

        $this->assertDatabaseHas('users', ['id' => $perusahaan->id, 'email' => 'baru@example.com']);
    }

    public function test_email_change_rejects_incorrect_current_password(): void
    {
        $perusahaan = User::factory()->create(['role' => 'perusahaan', 'email' => 'lama@example.com']);

        $this->actingAs($perusahaan)->patch(route('akun.email.update'), [
            'email' => 'baru@example.com',
            'current_password_email' => 'tidak-benar',
        ])->assertSessionHasErrors(['current_password_email' => 'Kata sandi saat ini tidak cocok.']);

        $this->assertDatabaseHas('users', ['id' => $perusahaan->id, 'email' => 'lama@example.com']);
    }

    public function test_perusahaan_can_update_password_after_confirming_current_password(): void
    {
        $perusahaan = User::factory()->create(['role' => 'perusahaan']);

        $this->actingAs($perusahaan)->patch(route('akun.kata_sandi.update'), [
            'current_password_password' => 'password',
            'password' => 'kata-sandi-baru-2026',
            'password_confirmation' => 'kata-sandi-baru-2026',
        ])->assertRedirect(route('akun.edit'))->assertSessionHas('status', 'Kata sandi berhasil diperbarui.');

        $this->assertTrue(Hash::check('kata-sandi-baru-2026', $perusahaan->refresh()->password));
    }

    public function test_password_change_rejects_incorrect_current_password(): void
    {
        $perusahaan = User::factory()->create(['role' => 'perusahaan']);

        $this->actingAs($perusahaan)->patch(route('akun.kata_sandi.update'), [
            'current_password_password' => 'tidak-benar',
            'password' => 'kata-sandi-baru-2026',
            'password_confirmation' => 'kata-sandi-baru-2026',
        ])->assertSessionHasErrors(['current_password_password' => 'Kata sandi saat ini tidak cocok.']);

        $this->assertTrue(Hash::check('password', $perusahaan->fresh()->password));
    }

    public function test_job_seekers_can_open_their_account_but_not_company_account_settings(): void
    {
        $pengguna = User::factory()->create(['role' => 'user']);

        $this->actingAs($pengguna)->get(route('akun.edit'))
            ->assertOk()
            ->assertSee('Akun saya')
            ->assertSee($pengguna->name)
            ->assertSee($pengguna->email)
            ->assertSee('action="'.route('akun.profil.update').'"', false)
            ->assertSee('action="'.route('akun.email.update').'"', false)
            ->assertSee('action="'.route('akun.kata_sandi.update').'"', false)
            ->assertDontSee('Pencari kerja')
            ->assertDontSee('Informasi akun pencari kerja Anda.');

        $this->patch(route('akun.email.update'), [])->assertSessionHasErrors(['email', 'current_password_email']);
        $this->patch(route('akun.profil.update'), [])->assertSessionHasErrors(['name']);
    }

    public function test_job_seekers_can_update_name_email_and_password(): void
    {
        $pengguna = User::factory()->create(['role' => 'user', 'email' => 'lama@example.com']);

        $this->actingAs($pengguna)->patch(route('akun.profil.update'), ['name' => 'Nama Baru'])
            ->assertRedirect(route('akun.edit'))->assertSessionHas('status', 'Nama berhasil diperbarui.');
        $this->assertDatabaseHas('users', ['id' => $pengguna->id, 'name' => 'Nama Baru']);

        $this->patch(route('akun.email.update'), ['email' => 'baru@example.com', 'current_password_email' => 'password'])
            ->assertRedirect(route('akun.edit'))->assertSessionHas('status', 'Email berhasil diperbarui.');
        $this->assertDatabaseHas('users', ['id' => $pengguna->id, 'email' => 'baru@example.com']);

        $this->patch(route('akun.kata_sandi.update'), [
            'current_password_password' => 'password',
            'password' => 'kata-sandi-baru-2026',
            'password_confirmation' => 'kata-sandi-baru-2026',
        ])->assertRedirect(route('akun.edit'))->assertSessionHas('status', 'Kata sandi berhasil diperbarui.');
        $this->assertTrue(Hash::check('kata-sandi-baru-2026', $pengguna->refresh()->password));
    }

    public function test_job_seekers_cannot_change_email_with_incorrect_current_password(): void
    {
        $pengguna = User::factory()->create(['role' => 'user', 'email' => 'lama@example.com']);

        $this->actingAs($pengguna)->patch(route('akun.email.update'), [
            'email' => 'baru@example.com',
            'current_password_email' => 'tidak-benar',
        ])->assertSessionHasErrors(['current_password_email' => 'Kata sandi saat ini tidak cocok.']);

        $this->assertDatabaseHas('users', ['id' => $pengguna->id, 'email' => 'lama@example.com']);
    }

    public function test_perusahaan_can_logout_from_account_page(): void
    {
        $perusahaan = User::factory()->create(['role' => 'perusahaan']);

        $this->actingAs($perusahaan)->post(route('logout'))->assertRedirect(route('login'));

        $this->assertGuest();
    }

    /** @return array<string, string> */
    private function dataProfil(): array
    {
        return [
            'name' => 'Lokerku Teknologi',
            'telepon' => '081234567890',
            'bidang_usaha' => 'Teknologi informasi',
            'jumlah_karyawan' => '11–50',
            'alamat_perusahaan' => 'Jalan Merdeka Nomor 10',
            'kota_perusahaan' => 'Surabaya',
            'deskripsi_perusahaan' => 'Perusahaan pengembangan perangkat lunak untuk bisnis Indonesia.',
            'situs_perusahaan' => 'https://lokerku.example',
            'nama_penanggung_jawab' => 'Dewi Lestari',
            'jabatan_penanggung_jawab' => 'Manajer SDM',
        ];
    }
}
