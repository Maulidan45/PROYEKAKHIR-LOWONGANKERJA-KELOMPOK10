<?php

namespace Tests\Feature;

use App\Models\Kategori;
use App\Models\Lamaran;
use App\Models\Lowongan;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class DasborPeranTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_dashboard_perusahaan_shows_relevant_navigation_and_hiring_activity_only(): void
    {
        $perusahaan = User::factory()->create(['role' => 'perusahaan']);

        $this->actingAs($perusahaan)->get('/perusahaan/dashboard')->assertOk()
            ->assertSeeInOrder(['class="kepala-situs', 'panel-menu--perusahaan', '<main id="main"'], false)
            ->assertSee('Dashboard')->assertSee(route('dashboard.role', ['role' => 'perusahaan']), false)
            ->assertSee('Pelamar')->assertSee(route('perusahaan.pelamar.index'), false)
            ->assertSee('Profil Perusahaan')->assertSee(route('akun.edit'), false)
            ->assertSee('aria-label="Menu perusahaan"', false)
            ->assertSee('data-toggle-menu', false)->assertSee('data-menu-dropdown', false)
            ->assertSee('id="menu-peran"', false)->assertSee('action="'.route('logout').'"', false)
            ->assertSee('Lowongan Aktif')->assertSee('Total Pelamar')->assertSee('Pelamar Baru')->assertSee('Aktivitas terbaru')
            ->assertDontSee('@tailwindcss/browser', false);
    }

    public function test_halaman_lowongan_perusahaan_menyatukan_aksi_pasang_lowongan_di_halaman_itu(): void
    {
        $perusahaan = User::factory()->create(['role' => 'perusahaan']);
        Lowongan::factory()->create(['perusahaan_id' => $perusahaan->id, 'judul' => 'Desainer Produk', 'aktif' => true]);
        Lowongan::factory()->create(['perusahaan_id' => $perusahaan->id, 'judul' => 'Arsip Lama', 'aktif' => false]);
        Lowongan::factory()->create(['perusahaan_id' => $perusahaan->id, 'judul' => 'Batas Lewat', 'aktif' => true, 'batas_lamaran' => now()->subDay()]);

        $this->actingAs($perusahaan)->get(route('lowongan.index'))->assertOk()
            ->assertSee('Lowongan saya')->assertSee('Pasang lowongan')->assertSee(route('lowongan.create'), false)
            ->assertSeeInOrder(['Desainer Produk', '>Aktif</span>'], false)
            ->assertSeeInOrder(['Arsip Lama', '>Ditutup</span>'], false)
            ->assertSeeInOrder(['Batas Lewat', '>Kedaluwarsa</span>'], false)
            ->assertDontSee('title="Pasang lowongan"', false);
    }

    public function test_halaman_lowongan_perusahaan_menyediakan_pencarian_saringan_dan_ringkasan(): void
    {
        $perusahaan = User::factory()->create(['role' => 'perusahaan']);
        Lowongan::factory()->create(['perusahaan_id' => $perusahaan->id, 'judul' => 'Frontend Developer', 'lokasi' => 'Surabaya']);
        Lowongan::factory()->create(['perusahaan_id' => $perusahaan->id, 'judul' => 'Backend Laravel', 'lokasi' => 'Bandung']);

        $this->actingAs($perusahaan)->get(route('lowongan.index', ['kata_kunci' => 'laravel']))->assertOk()
            ->assertSee('Backend Laravel')->assertDontSee('Frontend Developer');

        $this->get(route('lowongan.index', ['kata_kunci' => 'Surabaya']))->assertOk()
            ->assertSee('Frontend Developer')->assertDontSee('Backend Laravel');

        $this->get(route('lowongan.index', ['status' => 'kedaluwarsa']))->assertOk()
            ->assertSee('Lowongan tidak ditemukan')->assertSee('Tidak ada lowongan yang cocok');

        $this->get(route('lowongan.index'))->assertOk()
            ->assertSee('Total lowongan')->assertSee('Aktif')->assertSee('Kedaluwarsa')->assertSee('Lamaran masuk')
            ->assertSee('Lowongan saya')->assertSee('type="search"', false)->assertSee('name="kata_kunci"', false)
            ->assertSee('Ketik posisi atau lokasi…')
            ->assertDontSee('>Rekrutmen<', false)
            ->assertDontSee('Kelola posisi dan pantau lamaran yang masuk dari satu tempat.')
            ->assertSee('modal-lowongan', false)->assertSee('data-buka-modal', false)
            ->assertSee('<dialog', false);
    }

    public function test_halaman_lowongan_perusahaan_menampilkan_aksi_yang_sesuai_pada_keadaan_kosong(): void
    {
        $perusahaan = User::factory()->create(['role' => 'perusahaan']);

        $this->actingAs($perusahaan)->get(route('lowongan.index'))->assertOk()
            ->assertSee('Belum ada lowongan')
            ->assertSee('Lowongan yang Anda pasang akan muncul di sini bersama jumlah pelamarnya.')
            ->assertSee('Pasang lowongan pertama')->assertSee('data-buka-modal', false);

        $this->get(route('lowongan.index', ['kata_kunci' => 'tidak-ada']))->assertOk()
            ->assertSee('Lowongan tidak ditemukan');
    }

    public function test_pencarian_lowongan_menerima_input_yang_valid_dan_menolak_input_terlalu_panjang(): void
    {
        $perusahaan = User::factory()->create(['role' => 'perusahaan']);

        $this->actingAs($perusahaan)->get(route('lowongan.index', ['kata_kunci' => str_repeat('a', 101)]))
            ->assertSessionHasErrors('kata_kunci');

        $this->get(route('lowongan.index', ['status' => 'semua-yang-tidak-valid']))
            ->assertSessionHasErrors('status');
    }

    public function test_dashboard_company_counts_and_lists_only_its_own_applications(): void
    {
        $perusahaan = User::factory()->create(['role' => 'perusahaan']);
        $lowongan = Lowongan::factory()->create(['perusahaan_id' => $perusahaan->id, 'aktif' => true]);
        $pelamar = User::factory()->create(['role' => 'user']);
        Lamaran::factory()->create(['lowongan_id' => $lowongan->id, 'pelamar_id' => $pelamar->id, 'status' => 'Lamaran Terkirim']);
        $lowonganLain = Lowongan::factory()->create(['aktif' => true]);
        Lamaran::factory()->create(['lowongan_id' => $lowonganLain->id]);
        Lowongan::factory()->create(['perusahaan_id' => $perusahaan->id, 'aktif' => true, 'batas_lamaran' => now()->subDay()]);

        $this->actingAs($perusahaan)->get(route('dashboard.role', ['role' => 'perusahaan']))->assertOk()
            ->assertSee('Total Pelamar')->assertSee('Pelamar Baru')
            ->assertSee('>'.$pelamar->name.'<', false)->assertSee($lowongan->judul)
            ->assertDontSee($lowonganLain->judul);
    }

    public function test_company_can_open_its_candidate_menu_and_cannot_see_other_company_applications(): void
    {
        $perusahaan = User::factory()->create(['role' => 'perusahaan']);
        $lowonganMilik = Lowongan::factory()->create(['perusahaan_id' => $perusahaan->id]);
        $pelamarMilik = User::factory()->create(['role' => 'user']);
        Lamaran::factory()->create(['lowongan_id' => $lowonganMilik->id, 'pelamar_id' => $pelamarMilik->id]);

        $lowonganLain = Lowongan::factory()->create();
        $pelamarLain = User::factory()->create(['role' => 'user']);
        Lamaran::factory()->create(['lowongan_id' => $lowonganLain->id, 'pelamar_id' => $pelamarLain->id]);

        $this->actingAs($perusahaan)->get(route('perusahaan.pelamar.index'))->assertOk()
            ->assertSee($pelamarMilik->name)->assertSee($lowonganMilik->judul)
            ->assertDontSee($pelamarLain->name)->assertDontSee($lowonganLain->judul);
    }

    public function test_company_candidate_list_hides_the_requested_redundant_header(): void
    {
        $perusahaan = User::factory()->create(['role' => 'perusahaan']);

        $this->actingAs($perusahaan)->get(route('perusahaan.pelamar.index'))->assertOk()
            ->assertDontSee('>Rekrutmen<', false)
            ->assertDontSee('class="kepala-halaman-aplikasi"')
            ->assertDontSee('Semua lamaran yang masuk ke lowongan perusahaan Anda.')
            ->assertDontSee('0 lamaran')
            ->assertDontSee('Kembali ke lowongan');
    }

    public function test_only_company_accounts_can_open_company_candidate_list(): void
    {
        $pencariKerja = User::factory()->create(['role' => 'user']);

        $this->actingAs($pencariKerja)->get(route('perusahaan.pelamar.index'))->assertForbidden();
    }

    public function test_company_navigation_destinations_load_without_errors(): void
    {
        $perusahaan = User::factory()->create(['role' => 'perusahaan']);
        $lowongan = Lowongan::factory()->create(['perusahaan_id' => $perusahaan->id]);

        $this->actingAs($perusahaan);

        foreach ([
            route('dashboard.role', ['role' => 'perusahaan']),
            route('lowongan.index'),
            route('perusahaan.pelamar.index'),
            route('lowongan.create'),
            route('akun.edit'),
            route('lowongan.pelamar', $lowongan),
        ] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_admin_lowongan_list_is_presented_as_clear_cards_with_candidate_actions(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $lowongan = Lowongan::factory()->create(['judul' => 'Analis Data', 'lokasi' => 'Jakarta']);
        $pelamar = User::factory()->create(['role' => 'user', 'name' => 'Nadia Kandidat']);
        Lamaran::factory()->create(['lowongan_id' => $lowongan->id, 'pelamar_id' => $pelamar->id, 'status' => 'Lamaran Terkirim']);

        $this->actingAs($admin)->get(route('lowongan.index'))->assertOk()
            ->assertSee('Kelola Lowongan')
            ->assertSee('Analis Data')->assertSee('Jakarta')->assertSee('Pratinjau')->assertSee('Ubah')
            ->assertSee('1 lamaran')->assertSee('Aktif');

        $this->get(route('lowongan.pelamar', $lowongan))->assertOk()
            ->assertSee('Nadia Kandidat')->assertSee('Lamaran Terkirim');
    }

    public function test_dashboard_pencari_kerja_menampilkan_lowongan_dan_menu_dropdown(): void
    {
        $pencariKerja = User::factory()->create(['role' => 'user']);
        $lowongan = Lowongan::factory()->create(['judul' => 'Desainer Grafis']);

        $this->actingAs($pencariKerja)->get('/user/dashboard')->assertOk()
            ->assertSee('Desainer Grafis')->assertSee($lowongan->perusahaan->name)
            ->assertSee('data-menu-dropdown', false)->assertSee('aria-expanded="false"', false)
            ->assertSee('class="pencarian-dashboard"', false)->assertSee('Semua Lokasi')->assertSee('Semua Kategori')
            ->assertSee('>Dashboard</span>', false)->assertSee('>Riwayat Lamaran</span>', false)->assertSee('>Profil &amp; Akun</span>', false)
            ->assertSee('class="kisi-rekomendasi"', false);
    }

    public function test_dashboard_user_dapat_menyaring_lowongan_dengan_kata_kunci_lokasi_dan_kategori(): void
    {
        $pengguna = User::factory()->create(['role' => 'user']);
        $kategori = Kategori::factory()->create(['nama' => 'Desain Grafis']);
        Lowongan::factory()->create(['judul' => 'Desainer Produk', 'lokasi' => 'Bandung', 'kategori_id' => $kategori->id]);
        Lowongan::factory()->create(['judul' => 'Staf Akuntansi', 'lokasi' => 'Surabaya']);

        $this->actingAs($pengguna)->get(route('dashboard.role', ['role' => 'user', 'kata_kunci' => 'Desainer', 'lokasi' => 'Bandung', 'kategori' => $kategori->id]))
            ->assertOk()->assertSee('Desainer Produk')->assertDontSee('Staf Akuntansi')
            ->assertSee('value="Desainer"', false)->assertSee('value="Bandung" selected', false);
    }

    public function test_sidebar_admin_menampilkan_menu_pengelolaan_yang_dapat_diakses(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get('/admin/dashboard')->assertOk()
            ->assertSee('Ringkasan')->assertSee('Verifikasi Perusahaan')->assertSee('Kelola Lowongan')->assertSee('Kategori')->assertSee('Pengguna')
            ->assertDontSee('Pasang lowongan');
    }
}
