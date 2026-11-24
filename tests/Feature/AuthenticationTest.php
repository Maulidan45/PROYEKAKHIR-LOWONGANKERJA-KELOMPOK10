<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    #[TestWith(['/masuk', 'Selamat datang kembali'])]
    #[TestWith(['/daftar', 'Buat akun'])]
    public function test_guests_can_open_auth_forms(string $alamat, string $judul): void
    {
        $tanggapan = $this->get($alamat)->assertOk()->assertSee($judul);

        $tanggapan
            ->assertSee('halaman-autentikasi-form', false)
            ->assertDontSee('AKUN LOKERKU')->assertDontSee('SELAMAT DATANG KEMBALI')
            ->assertDontSee('Ruang untuk langkah berikutnya.')
            ->assertDontSee('Satu tempat untuk akun kariermu.')
            ->assertDontSee('Masuk ke akunmu.')
            ->assertDontSee('Lanjutkan ke ruang akunmu')
            ->assertDontSee('career-team.jpg');

        $this->assertSame($alamat === '/daftar' ? 2 : 1, substr_count($tanggapan->getContent(), 'data-lihat-sandi'));
    }

    public function test_company_registration_url_redirects_to_shared_registration(): void
    {
        $this->get('/daftar?role=perusahaan')->assertRedirectToRoute('register');
    }

    #[TestWith(['user'])]
    #[TestWith(['perusahaan'])]
    public function test_public_registration_stores_normalized_email_and_logs_in(string $jenisAkun): void
    {
        $this->withSession(['previous-data' => 'retained']);
        $idSesiLama = session()->getId();

        $this->post(route('register.store'), [
            'name' => 'Akun Lokerku',
            'email' => '  Akun@Example.com ',
            'role' => $jenisAkun,
            'telepon' => '081234567890',
            'domisili' => 'Surabaya',
            'pendidikan_terakhir' => 'S1',
            'bidang_usaha' => 'Teknologi informasi',
            'jumlah_karyawan' => '11–50',
            'alamat_perusahaan' => 'Jalan Merdeka Nomor 10',
            'kota_perusahaan' => 'Surabaya',
            'deskripsi_perusahaan' => 'Perusahaan pengembangan perangkat lunak untuk bisnis Indonesia.',
            'nama_penanggung_jawab' => 'Dewi Lestari',
            'jabatan_penanggung_jawab' => 'Manajer SDM',
            'password' => 'kata-sandi-aman-2026',
            'password_confirmation' => 'kata-sandi-aman-2026',
            'email_verified_at' => now()->toDateTimeString(),
        ])->assertRedirectToRoute('dashboard')->assertSessionHasNoErrors();

        $pengguna = User::sole();
        $this->assertAuthenticatedAs($pengguna);
        $this->assertDatabaseHas('users', ['email' => 'akun@example.com', 'role' => $jenisAkun, 'email_verified_at' => null]);
        $this->assertTrue(Hash::check('kata-sandi-aman-2026', $pengguna->password));
        $this->assertNotSame($idSesiLama, session()->getId());
    }

    #[TestWith(['admin'])]
    #[TestWith(['recruiter'])]
    #[TestWith([['admin']])]
    #[TestWith([['user']])]
    public function test_registration_rejects_non_public_roles(mixed $jenisAkun): void
    {
        $this->post(route('register.store'), [
            'name' => 'Akun Lokerku', 'email' => 'akun@example.com', 'role' => $jenisAkun,
            'password' => 'kata-sandi-aman-2026', 'password_confirmation' => 'kata-sandi-aman-2026',
        ])->assertSessionHasErrors('role');

        $this->assertDatabaseCount('users', 0);
        $this->assertGuest();
    }

    public function test_registration_checks_email_uniqueness_after_normalization(): void
    {
        User::factory()->create(['email' => 'akun@example.com']);

        $this->post(route('register.store'), [
            'name' => 'Akun Lain', 'email' => ' AKUN@EXAMPLE.COM ', 'role' => 'user',
            'password' => 'kata-sandi-aman-2026', 'password_confirmation' => 'kata-sandi-aman-2026',
        ])->assertSessionHasErrors(['email' => 'Email sudah terdaftar. Silakan masuk.']);

        $this->assertDatabaseCount('users', 1);
        $this->assertGuest();
    }

    public function test_registration_requires_fields(): void
    {
        $this->post(route('register.store'))->assertSessionHasErrors([
            'name' => 'Nama wajib diisi.', 'email' => 'Email wajib diisi.',
            'role' => 'Jenis akun wajib diisi.', 'password' => 'Kata sandi wajib diisi.',
        ]);

        $this->assertDatabaseCount('users', 0);
    }

    #[TestWith(['pendek', 'pendek', 'Kata sandi minimal 12 karakter.'])]
    #[TestWith(['kata-sandi-aman', 'tidak-sama', 'Konfirmasi kata sandi tidak cocok.'])]
    #[TestWith(['ééééééééééééééééééééééééééééééééééééé', 'ééééééééééééééééééééééééééééééééééééé', 'Kata sandi maksimal 72 byte dan tidak boleh mengandung karakter null.'])]
    #[TestWith(["kata-sandi-aman\0", "kata-sandi-aman\0", 'Kata sandi maksimal 72 byte dan tidak boleh mengandung karakter null.'])]
    public function test_registration_rejects_unsafe_passwords(string $kataSandi, string $konfirmasi, string $pesan): void
    {
        $this->post(route('register.store'), [
            'name' => 'Akun Lokerku', 'email' => 'akun@example.com', 'role' => 'user',
            'password' => $kataSandi, 'password_confirmation' => $konfirmasi,
        ])->assertSessionHasErrors(['password' => $pesan]);

        $this->assertDatabaseCount('users', 0);
        $this->assertGuest();
    }

    #[TestWith(['user'])]
    #[TestWith(['perusahaan'])]
    #[TestWith(['admin'])]
    public function test_all_roles_can_login_and_are_redirected_to_their_dashboard(string $jenisAkun): void
    {
        $pengguna = User::factory()->create(['role' => $jenisAkun, 'email' => 'akun@example.com']);
        $this->withSession(['previous-data' => true]);
        $idSesiLama = session()->getId();

        $this->post(route('login.store'), ['email' => ' AKUN@Example.com ', 'password' => 'password'])
            ->assertRedirectToRoute('dashboard');

        $this->assertAuthenticatedAs($pengguna);
        $this->assertNotSame($idSesiLama, session()->getId());
        $this->get(route('dashboard'))->assertRedirectToRoute('dashboard.role', ['role' => $jenisAkun]);
        $this->get(route('dashboard.role', ['role' => $jenisAkun]))->assertOk();
        $halamanIdentitas = $jenisAkun === 'admin' ? route('dashboard.role', ['role' => $jenisAkun]) : route('akun.edit');
        $this->get($halamanIdentitas)->assertOk()->assertSee($pengguna->email);
    }

    #[TestWith(['wrong-password'])]
    #[TestWith(["password\0"])]
    public function test_login_rejects_invalid_credentials(string $kataSandi): void
    {
        User::factory()->create(['email' => 'akun@example.com']);

        $this->post(route('login.store'), ['email' => 'akun@example.com', 'password' => $kataSandi])
            ->assertSessionHasErrors(['email' => 'Email atau kata sandi tidak cocok.']);

        $this->assertGuest();
    }

    public function test_login_rejects_bcrypt_truncation_suffixes(): void
    {
        User::factory()->create(['email' => 'akun@example.com', 'password' => str_repeat('a', 72)]);

        $this->post(route('login.store'), ['email' => 'akun@example.com', 'password' => str_repeat('a', 73)])
            ->assertSessionHasErrors(['email' => 'Email atau kata sandi tidak cocok.']);

        $this->assertGuest();
    }

    #[TestWith(['/masuk'])]
    #[TestWith(['/daftar'])]
    public function test_auth_rejects_array_inputs_without_creating_a_session(string $alamat): void
    {
        $this->post($alamat, ['name' => ['invalid'], 'email' => ['invalid'], 'role' => ['user'], 'password' => ['invalid']])
            ->assertSessionHasErrors(['email' => 'Email harus berupa teks.', 'password' => 'Kata sandi harus berupa teks.']);

        $this->assertDatabaseCount('users', 0);
        $this->assertGuest();
    }

    public function test_login_requires_email_and_password(): void
    {
        $this->post(route('login.store'))->assertSessionHasErrors([
            'email' => 'Email wajib diisi.', 'password' => 'Kata sandi wajib diisi.',
        ]);

        $this->assertGuest();
    }

    public function test_registration_errors_render_accessibly_and_keep_only_safe_input(): void
    {
        $this->followingRedirects()->from(route('register'))->post(route('register.store'), [
            'name' => '<script>alert(1)</script>', 'email' => 'invalid-email', 'role' => 'user',
            'password' => 'short-secret', 'password_confirmation' => 'mismatch',
        ])->assertOk()->assertSee('Masukkan alamat email yang valid.')
            ->assertSee('Konfirmasi kata sandi tidak cocok.')->assertSee('aria-invalid="true"', false)
            ->assertSee('data-kesalahan-formulir', false)->assertSee('<script>alert(1)</script>')
            ->assertDontSee('<script>alert(1)</script>', false)->assertDontSee('short-secret');
        $this->assertDatabaseCount('users', 0);
    }

    public function test_login_ip_limit_cannot_be_bypassed_by_changing_email(): void
    {
        $this->freezeTime();

        for ($percobaan = 0; $percobaan < 30; $percobaan++) {
            $this->post(route('login.store'), ['email' => 'account'.$percobaan.'@example.com', 'password' => 'wrong-password'])
                ->assertSessionHasErrors('email');
        }

        $this->post(route('login.store'), ['email' => 'another@example.com', 'password' => 'wrong-password'])
            ->assertTooManyRequests()->assertHeader('Retry-After');
        $this->assertGuest();
    }

    public function test_admin_account_list_is_paginated_and_counts_real_accounts(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        User::factory()->count(10)->create(['role' => 'user']);

        $this->actingAs($admin)->get('/admin/dashboard')->assertOk()
            ->assertViewHas('daftarAkun', fn ($daftarAkun): bool => $daftarAkun->total() === 11 && $daftarAkun->count() === 10)
            ->assertViewHas('jumlahAkun', fn ($jumlah): bool => (int) $jumlah['user'] === 10 && (int) $jumlah['admin'] === 1);
        $this->get('/admin/dashboard?page=2')->assertOk()->assertSee($admin->email);
    }

    public function test_logout_invalidates_session_and_protects_dashboard(): void
    {
        $pengguna = User::factory()->create();
        $this->actingAs($pengguna)->withSession(['private-data' => 'secret']);
        $tokenLama = session()->token();

        $this->post(route('logout'))->assertRedirectToRoute('login')->assertSessionMissing('private-data');

        $this->assertGuest();
        $this->assertNotSame($tokenLama, session()->token());
        $this->get(route('dashboard'))->assertRedirectToRoute('login');
    }

    #[TestWith(['/dashboard'])]
    #[TestWith(['/admin/dashboard'])]
    #[TestWith(['/perusahaan/dashboard'])]
    #[TestWith(['/user/dashboard'])]
    public function test_guests_are_redirected_from_protected_dashboards(string $alamat): void
    {
        $this->get($alamat)->assertRedirectToRoute('login');
    }

    #[TestWith(['user', 'admin'])]
    #[TestWith(['user', 'perusahaan'])]
    #[TestWith(['perusahaan', 'admin'])]
    #[TestWith(['perusahaan', 'user'])]
    #[TestWith(['admin', 'user'])]
    #[TestWith(['admin', 'perusahaan'])]
    public function test_dashboard_forbids_other_roles(string $jenisAkun, string $tujuan): void
    {
        $pengguna = User::factory()->create(['role' => $jenisAkun]);

        $this->actingAs($pengguna)->get(route('dashboard.role', ['role' => $tujuan]))->assertForbidden();
    }

    public function test_authenticated_users_cannot_register_or_login_again(): void
    {
        $pengguna = User::factory()->create();

        $this->actingAs($pengguna)->get(route('login'))->assertRedirectToRoute('dashboard');
        $this->get(route('register'))->assertRedirectToRoute('dashboard');
        $this->post(route('register.store'), ['role' => 'admin'])->assertRedirectToRoute('dashboard');
        $this->post(route('login.store'))->assertRedirectToRoute('dashboard');
        $this->assertDatabaseCount('users', 1);
    }

    public function test_dashboard_escapes_account_names(): void
    {
        $pengguna = User::factory()->create(['name' => '<script>alert(1)</script>', 'role' => 'admin']);

        $this->actingAs($pengguna)->get(route('dashboard.role', ['role' => 'admin']))
            ->assertOk()->assertSee('<script>alert(1)</script>')->assertDontSee('<script>alert(1)</script>', false);
    }

    public function test_login_limits_normalized_email_attempts_and_recovers_after_a_minute(): void
    {
        $this->freezeTime();

        foreach (['AKUN@example.com', 'akun@example.com', ' akun@example.com ', 'AKUN@EXAMPLE.COM', 'akun@Example.com'] as $email) {
            $this->post(route('login.store'), ['email' => $email, 'password' => 'wrong-password'])->assertSessionHasErrors('email');
        }

        $this->post(route('login.store'), ['email' => 'akun@example.com', 'password' => 'wrong-password'])->assertTooManyRequests();
        $this->travel(61)->seconds();
        $this->post(route('login.store'), ['email' => 'akun@example.com', 'password' => 'wrong-password'])->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_registration_is_rate_limited(): void
    {
        $this->freezeTime();

        for ($percobaan = 0; $percobaan < 5; $percobaan++) {
            $this->post(route('register.store'))->assertSessionHasErrors('email');
        }

        $this->post(route('register.store'))->assertTooManyRequests();
        $this->assertDatabaseCount('users', 0);
    }

    public function test_admin_can_be_provisioned_only_via_server_command(): void
    {
        $pengguna = User::factory()->create(['email' => 'akun@example.com']);

        $this->artisan('app:promote-admin', ['email' => ' AKUN@EXAMPLE.COM '])->assertSuccessful();

        $this->assertDatabaseHas('users', ['id' => $pengguna->id, 'role' => 'admin']);
        $this->assertDatabaseCount('users', 1);
    }

    public function test_admin_command_does_not_create_missing_accounts(): void
    {
        $this->artisan('app:promote-admin', ['email' => 'missing@example.com'])->assertFailed();

        $this->assertDatabaseCount('users', 0);
    }
}
