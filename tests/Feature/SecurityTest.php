<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Vite;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_responses_prevent_framing_referrer_leaks_and_private_page_caching(): void
    {
        $this->withVite();
        Vite::clearResolvedInstance();
        Vite::useHotFile(storage_path('framework/disabled-vite.hot'));

        $this->get('/masuk')->assertOk()
            ->assertHeader('X-Frame-Options', 'DENY')
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('Referrer-Policy', 'no-referrer')
            ->assertHeaderContains('Cache-Control', 'no-store')
            ->assertHeaderContains('Content-Security-Policy', "form-action 'self'")
            ->assertHeaderMissing('Strict-Transport-Security');
    }

    public function test_https_responses_enable_hsts(): void
    {
        $this->get('https://localhost/masuk')->assertOk()
            ->assertHeader('Strict-Transport-Security', 'max-age=31536000');
    }

    public function test_development_assets_do_not_get_a_production_csp(): void
    {
        $this->withVite();
        Vite::clearResolvedInstance();
        Vite::useHotFile(base_path('package.json'));

        $this->get('/masuk')->assertOk()->assertHeaderMissing('Content-Security-Policy');
    }

    #[TestWith(['evil.example'])]
    #[TestWith(['sub.localhost'])]
    public function test_production_rejects_untrusted_hosts(string $namaHost): void
    {
        $this->app->instance('env', 'production');

        try {
            $this->get('http://'.$namaHost.'/masuk')->assertBadRequest();
        } finally {
            Request::setTrustedHosts([]);
        }
    }

    public function test_production_accepts_the_configured_host(): void
    {
        $this->app->instance('env', 'production');

        try {
            $this->get('/masuk')->assertOk();
        } finally {
            Request::setTrustedHosts([]);
        }
    }

    public function test_debug_output_is_disabled_in_production_even_when_environment_enables_it(): void
    {
        $lingkunganSebelumnya = getenv('APP_ENV');
        $debugSebelumnya = getenv('APP_DEBUG');
        $lingkunganServerSebelumnya = $_SERVER['APP_ENV'] ?? null;
        $debugServerSebelumnya = $_SERVER['APP_DEBUG'] ?? null;
        $lingkunganEnvSebelumnya = $_ENV['APP_ENV'] ?? null;
        $debugEnvSebelumnya = $_ENV['APP_DEBUG'] ?? null;
        putenv('APP_ENV=production');
        putenv('APP_DEBUG=true');
        $_SERVER['APP_ENV'] = $_ENV['APP_ENV'] = 'production';
        $_SERVER['APP_DEBUG'] = $_ENV['APP_DEBUG'] = 'true';

        try {
            $konfigurasi = require config_path('app.php');
            $this->assertFalse($konfigurasi['debug']);
        } finally {
            $lingkunganSebelumnya === false ? putenv('APP_ENV') : putenv('APP_ENV='.$lingkunganSebelumnya);
            $debugSebelumnya === false ? putenv('APP_DEBUG') : putenv('APP_DEBUG='.$debugSebelumnya);
            if ($lingkunganServerSebelumnya === null) {
                unset($_SERVER['APP_ENV']);
            } else {
                $_SERVER['APP_ENV'] = $lingkunganServerSebelumnya;
            }
            if ($debugServerSebelumnya === null) {
                unset($_SERVER['APP_DEBUG']);
            } else {
                $_SERVER['APP_DEBUG'] = $debugServerSebelumnya;
            }
            if ($lingkunganEnvSebelumnya === null) {
                unset($_ENV['APP_ENV']);
            } else {
                $_ENV['APP_ENV'] = $lingkunganEnvSebelumnya;
            }
            if ($debugEnvSebelumnya === null) {
                unset($_ENV['APP_DEBUG']);
            } else {
                $_ENV['APP_DEBUG'] = $debugEnvSebelumnya;
            }
        }
    }

    #[TestWith(['/masuk', 'login'])]
    #[TestWith(['/daftar', 'register'])]
    public function test_invalid_auth_input_never_redirects_to_an_external_referer(string $alamat, string $rute): void
    {
        $this->from('https://evil.example/')->post($alamat, [
            'email' => 'invalid', 'password' => 'secret-password', 'unexpected_secret' => 'do-not-flash',
        ])->assertRedirectToRoute($rute)->assertSessionHasErrors('email')
            ->assertSessionMissing('_old_input.password')->assertSessionMissing('_old_input.unexpected_secret');

        $this->assertDatabaseCount('users', 0);
        $this->assertGuest();
    }

    public function test_invalid_html_search_recovers_without_a_redirect_loop(): void
    {
        $this->followingRedirects()->from('http://localhost/?kata_kunci[]=bad')->get('/?kata_kunci[]=bad')
            ->assertOk()->assertSee('Lowongan kerja');
    }

    public function test_searches_reject_script_tags_without_rendering_them(): void
    {
        $skrip = "<script>alert('250441100121')</script>";
        $pencariKerja = User::factory()->create(['role' => 'user']);
        $perusahaan = User::factory()->create(['role' => 'perusahaan']);

        $this->get(route('home', ['kata_kunci' => $skrip]))
            ->assertRedirectToRoute('home')->assertSessionHasErrors('kata_kunci')->assertDontSee($skrip, false);
        $this->actingAs($pencariKerja)->get(route('dashboard.role', ['role' => 'user', 'kata_kunci' => $skrip]))
            ->assertRedirectToRoute('dashboard')->assertSessionHasErrors('kata_kunci')->assertDontSee($skrip, false);
        $this->actingAs($perusahaan)->get(route('lowongan.index', ['kata_kunci' => $skrip]))
            ->assertRedirectToRoute('lowongan.index')->assertSessionHasErrors('kata_kunci')->assertDontSee($skrip, false);
    }

    public function test_search_fields_disable_browser_autocomplete(): void
    {
        $pencariKerja = User::factory()->create(['role' => 'user']);
        $perusahaan = User::factory()->create(['role' => 'perusahaan']);

        $this->get('/')->assertOk()->assertSee('name="kata_kunci"', false)->assertDontSee('name="cari"', false)->assertSee('autocomplete="off"', false);
        $this->actingAs($pencariKerja)->get('/user/dashboard')->assertOk()->assertSee('name="kata_kunci"', false)->assertDontSee('name="cari"', false)->assertSee('autocomplete="off"', false);
        $this->actingAs($perusahaan)->get(route('lowongan.index'))->assertOk()->assertSee('name="kata_kunci"', false)->assertDontSee('name="cari"', false)->assertSee('autocomplete="off"', false);
    }

    #[TestWith(['kategori'])]
    #[TestWith(['tipe'])]
    public function test_filter_tidak_valid_ditolak(string $penyaring): void
    {
        $this->get('/?'.$penyaring.'=0')->assertRedirectToRoute('home')->assertSessionHasErrors($penyaring);
    }

    public function test_null_characters_in_names_are_rejected_before_postgres_insert(): void
    {
        $this->post('/daftar', [
            'name' => "Nama\0Akun", 'email' => 'safe@example.com', 'role' => 'user',
            'password' => 'kata-sandi-aman-2026', 'password_confirmation' => 'kata-sandi-aman-2026',
        ])->assertRedirectToRoute('register')->assertSessionHasErrors(['name' => 'Nama tidak boleh mengandung karakter null.']);

        $this->assertDatabaseCount('users', 0);
        $this->assertGuest();
    }

    public function test_registration_handles_an_email_taken_between_validation_and_insert(): void
    {
        $akunTersedia = User::factory()->make(['email' => 'racing@example.com']);
        Event::listen('eloquent.creating: '.User::class, function () use ($akunTersedia): void {
            DB::table('users')->insert($akunTersedia->getAttributes());
        });

        $this->post('/daftar', [
            'name' => 'New Account', 'email' => 'racing@example.com', 'role' => 'user',
            'telepon' => '081234567890', 'domisili' => 'Surabaya', 'pendidikan_terakhir' => 'S1',
            'password' => 'kata-sandi-aman-2026', 'password_confirmation' => 'kata-sandi-aman-2026',
        ])->assertRedirectToRoute('register')->assertSessionHasErrors(['email' => 'Email sudah terdaftar. Silakan masuk.']);

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('users', ['email' => 'racing@example.com', 'name' => $akunTersedia->name]);
        $this->assertGuest();
    }

    #[TestWith(['page[]=2'])]
    #[TestWith(['page=-1'])]
    #[TestWith(['page=99999999999999999999999999999999'])]
    public function test_invalid_admin_pagination_does_not_trigger_server_errors(string $kueri): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get('/admin/dashboard?'.$kueri)
            ->assertRedirectToRoute('dashboard')->assertSessionHasErrors('page');
    }

    #[TestWith(['/masuk'])]
    #[TestWith(['/daftar'])]
    #[TestWith(['/keluar'])]
    public function test_cross_site_posts_without_csrf_tokens_are_rejected(string $alamat): void
    {
        $this->app->instance('env', 'production');

        try {
            $this->post($alamat)->assertStatus(419);
        } finally {
            Request::setTrustedHosts([]);
        }
    }
}
