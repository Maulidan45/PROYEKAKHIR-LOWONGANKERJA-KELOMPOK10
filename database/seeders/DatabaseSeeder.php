<?php

namespace Database\Seeders;

use App\Models\Kategori;
use App\Models\Lowongan;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => 'password',
                'role' => 'user',
                'email_verified_at' => now(),
            ]
        );

        User::query()->firstOrCreate(
            ['email' => 'admin@lokerku.id'],
            [
                'name' => 'Admin Lokerku',
                'password' => 'admin12345678',
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        $daftarKategori = [
            'Administrasi & Perkantoran',
            'Akuntansi & Keuangan',
            'Pemasaran & Penjualan',
            'Teknologi Informasi',
            'Operasional & Logistik',
            'Sumber Daya Manusia',
            'Desain & Kreatif',
            'Teknik & Manufaktur',
            'Pendidikan & Pelatihan',
            'Kesehatan & Farmasi',
            'Perhotelan & Kuliner',
        ];

        $kategoriMap = [];
        foreach ($daftarKategori as $namaKategori) {
            $kategoriMap[$namaKategori] = Kategori::query()->firstOrCreate(
                ['slug' => Str::slug($namaKategori)],
                ['nama' => $namaKategori]
            );
        }

        $profilPerusahaan = [
            'nusantara' => [
                'email' => 'hr@nusantaradigital.id',
                'name' => 'PT Nusantara Digital Mandiri',
                'profil_pendaftaran' => [
                    'telepon' => '021-5550101',
                    'bidang_usaha' => 'Teknologi Informasi & Digital',
                    'jumlah_karyawan' => '201–500',
                    'kota_perusahaan' => 'Kota Jakarta Selatan',
                    'alamat_perusahaan' => 'Jl. Jend. Sudirman Kav. 52-53, Senayan, Kebayoran Baru',
                    'deskripsi_perusahaan' => 'PT Nusantara Digital Mandiri adalah perusahaan teknologi yang mengembangkan platform digital, layanan cloud, serta solusi e-commerce untuk perusahaan di seluruh Indonesia.',
                    'situs_perusahaan' => 'https://nusantaradigital.id',
                ],
            ],
            'samudra' => [
                'email' => 'rekrutmen@samudralogistik.id',
                'name' => 'PT Samudra Logistik Express',
                'profil_pendaftaran' => [
                    'telepon' => '021-5550202',
                    'bidang_usaha' => 'Transportasi & Logistik',
                    'jumlah_karyawan' => '501+',
                    'kota_perusahaan' => 'Kota Jakarta Utara',
                    'alamat_perusahaan' => 'Jl. Yos Sudarso No. 88, Tanjung Priok',
                    'deskripsi_perusahaan' => 'PT Samudra Logistik Express bergerak di bidang layanan pengiriman kargo darat, laut, dan pergudangan terpadu dengan jaringan distribusi nasional.',
                    'situs_perusahaan' => 'https://samudralogistik.id',
                ],
            ],
            'karya' => [
                'email' => 'karir@karyamandiri.co.id',
                'name' => 'PT Karya Mandiri Sejahtera',
                'profil_pendaftaran' => [
                    'telepon' => '021-5550303',
                    'bidang_usaha' => 'Konsultan Bisnis & Keuangan',
                    'jumlah_karyawan' => '51–200',
                    'kota_perusahaan' => 'Kota Jakarta Selatan',
                    'alamat_perusahaan' => 'Jl. HR Rasuna Said Blok X-5, Kuningan',
                    'deskripsi_perusahaan' => 'PT Karya Mandiri Sejahtera menyediakan layanan konsultasi manajemen, akuntansi, rekrutmen, dan pengembangan sumber daya manusia untuk sektor korporasi.',
                    'situs_perusahaan' => 'https://karyamandiri.co.id',
                ],
            ],
            'cakrawala' => [
                'email' => 'talent@cakrawalakreatif.id',
                'name' => 'PT Cakrawala Kreatif Media',
                'profil_pendaftaran' => [
                    'telepon' => '022-5550404',
                    'bidang_usaha' => 'Media, Periklanan & Desain',
                    'jumlah_karyawan' => '11–50',
                    'kota_perusahaan' => 'Kota Bandung',
                    'alamat_perusahaan' => 'Jl. Ir. H. Juanda No. 112, Dago',
                    'deskripsi_perusahaan' => 'PT Cakrawala Kreatif Media adalah agensi kreatif yang berfokus pada produksi konten digital, desain produk, dan strategi pemasaran merek modern.',
                    'situs_perusahaan' => 'https://cakrawalakreatif.id',
                ],
            ],
            'sentosa' => [
                'email' => 'hrd@sentosamanufaktur.co.id',
                'name' => 'PT Sentosa Manufaktur Industri',
                'profil_pendaftaran' => [
                    'telepon' => '021-5550505',
                    'bidang_usaha' => 'Manufaktur & Teknik Industri',
                    'jumlah_karyawan' => '501+',
                    'kota_perusahaan' => 'Kota Bekasi',
                    'alamat_perusahaan' => 'Kawasan Industri Jababeka Tahap II Blok C-14',
                    'deskripsi_perusahaan' => 'PT Sentosa Manufaktur Industri memproduksi komponen presisi dan peralatan teknik berstandar mutu internasional dengan penerapan K3 yang ketat.',
                    'situs_perusahaan' => 'https://sentosamanufaktur.co.id',
                ],
            ],
            'harmoni' => [
                'email' => 'karir@harmonihospitality.id',
                'name' => 'PT Harmoni Hospitality Grup',
                'profil_pendaftaran' => [
                    'telepon' => '0361-5550606',
                    'bidang_usaha' => 'Perhotelan & Kuliner',
                    'jumlah_karyawan' => '201–500',
                    'kota_perusahaan' => 'Kabupaten Badung',
                    'alamat_perusahaan' => 'Jl. Sunset Road No. 45, Kuta, Bali',
                    'deskripsi_perusahaan' => 'PT Harmoni Hospitality Grup mengelola jaringan hotel butik, resor, dan kafe modern di berbagai destinasi wisata unggulan Indonesia.',
                    'situs_perusahaan' => 'https://harmonihospitality.id',
                ],
            ],
        ];

        $perusahaan = [];
        foreach ($profilPerusahaan as $kode => $info) {
            $akun = User::query()->firstOrCreate(
                ['email' => $info['email']],
                [
                    'name' => $info['name'],
                    'password' => 'password123',
                    'role' => 'perusahaan',
                    'email_verified_at' => now(),
                ]
            );
            $akun->profil_pendaftaran = $info['profil_pendaftaran'];
            $akun->save();
            $perusahaan[$kode] = $akun;
        }

        $daftarLowongan = [
            // DKI Jakarta
            ['Staf Admin', 'Administrasi & Perkantoran', 'karya', 'Kota Jakarta Selatan', 'Penuh waktu', 5000000, 6500000, 'Mengelola pengarsipan dokumen, jadwal rapat, dan koordinasi administrasi harian kantor.'],
            ['E-Commerce & Marketplace', 'Pemasaran & Penjualan', 'nusantara', 'Kota Jakarta Barat', 'Penuh waktu', 5500000, 7800000, 'Mengelola toko resmi di marketplace, optimasi katalog produk, dan strategi promo bulanan.'],
            ['Frontend React Developer', 'Teknologi Informasi', 'nusantara', 'Kota Jakarta Pusat', 'Jarak jauh', 9000000, 14000000, 'Membangun antarmuka web modern yang responsif dan berkolaborasi dengan tim produk serta backend.'],
            ['UI/UX Designer', 'Desain & Kreatif', 'cakrawala', 'Kota Jakarta Selatan', 'Penuh waktu', 7500000, 11000000, 'Merancang wireframe, prototipe interaktif, dan sistem desain untuk aplikasi web maupun mobile.'],
            ['Magang Human Capital', 'Sumber Daya Manusia', 'karya', 'Kota Jakarta Timur', 'Magang', 2500000, 3500000, 'Membantu proses skrining kandidat, penjadwalan wawancara, dan dokumentasi orientasi karyawan.'],
            ['Koordinator Gudang Pelabuhan', 'Operasional & Logistik', 'samudra', 'Kota Jakarta Utara', 'Penuh waktu', 6500000, 9000000, 'Mengawasi penerimaan kontainer, pencatatan stok, dan kelancaran distribusi barang ekspor-impor.'],

            // Jawa Barat
            ['Pemasaran Digital', 'Pemasaran & Penjualan', 'nusantara', 'Kota Bandung', 'Penuh waktu', 6000000, 8500000, 'Merancang kampanye iklan digital, mengelola SEO/SEM, dan menganalisis performa konversi.'],
            ['Desainer Grafis', 'Desain & Kreatif', 'cakrawala', 'Kota Bandung', 'Paruh waktu', 3500000, 5000000, 'Membuat materi visual media sosial, banner kampanye, dan aset branding perusahaan.'],
            ['Operator Produksi CNC', 'Teknik & Manufaktur', 'sentosa', 'Kota Bekasi', 'Penuh waktu', 5200000, 6800000, 'Mengoperasikan mesin CNC sesuai standar kualitas dan menjaga keselamatan kerja di lini produksi.'],
            ['Supervisor Quality Control', 'Teknik & Manufaktur', 'sentosa', 'Kabupaten Karawang', 'Penuh waktu', 7000000, 9500000, 'Melakukan inspeksi mutu bahan baku serta produk jadi dan menyusun laporan evaluasi harian.'],
            ['Guru Bahasa Inggris', 'Pendidikan & Pelatihan', 'karya', 'Kota Depok', 'Paruh waktu', 3800000, 5500000, 'Mengajar kelas bahasa Inggris secara interaktif dan menyusun materi evaluasi peserta didik.'],
            ['Kurir Ekspedisi', 'Operasional & Logistik', 'samudra', 'Kota Bogor', 'Penuh waktu', 4000000, 5200000, 'Mengirimkan paket ke alamat pelanggan dengan aman dan memperbarui status pengiriman di aplikasi.'],

            // Banten
            ['Analis Keuangan', 'Akuntansi & Keuangan', 'karya', 'Kota Tangerang Selatan', 'Penuh waktu', 7500000, 10500000, 'Menyusun proyeksi arus kas, analisis anggaran tahunan, dan evaluasi efisiensi biaya operasional.'],
            ['Apoteker Pendamping', 'Kesehatan & Farmasi', 'karya', 'Kota Tangerang', 'Penuh waktu', 6000000, 8000000, 'Melayani konsultasi obat kepada pasien, mengelola stok farmasi, dan memastikan kepatuhan resep.'],
            ['Teknisi Listrik Industri', 'Teknik & Manufaktur', 'sentosa', 'Kota Cilegon', 'Penuh waktu', 5800000, 7600000, 'Melakukan perawatan panel listrik pabrik, troubleshooting mesin, dan inspeksi kelistrikan berkala.'],

            // Jawa Tengah & DI Yogyakarta
            ['Administrasi Penjualan', 'Administrasi & Perkantoran', 'samudra', 'Kota Semarang', 'Penuh waktu', 4500000, 5800000, 'Memproses pesanan pelanggan, membuat faktur penjualan, dan memantau pengiriman barang.'],
            ['Magang Digital Marketing', 'Pemasaran & Penjualan', 'cakrawala', 'Kota Semarang', 'Magang', 2000000, 2800000, 'Membantu riset kata kunci, penjadwalan konten media sosial, dan pelaporan metrik mingguan.'],
            ['Backend Laravel Developer', 'Teknologi Informasi', 'nusantara', 'Kota Yogyakarta', 'Jarak jauh', 8000000, 13000000, 'Membangun API yang andal menggunakan Laravel, merancang skema basis data, dan menulis pengujian otomatis.'],
            ['Video Editor & Motion', 'Desain & Kreatif', 'cakrawala', 'Kabupaten Sleman', 'Penuh waktu', 4800000, 6800000, 'Mengedit video promosi, menambahkan animasi grafis, dan memastikan kualitas audio-visual.'],
            ['Staff Pajak & Pembukuan', 'Akuntansi & Keuangan', 'karya', 'Kota Surakarta', 'Penuh waktu', 4500000, 6000000, 'Menghitung PPh/PPN bulanan, menyiapkan SPT masa, dan merapikan pembukuan transaksi harian.'],

            // Jawa Timur
            ['Staf Accounting', 'Akuntansi & Keuangan', 'karya', 'Kota Surabaya', 'Penuh waktu', 5500000, 7500000, 'Menyusun laporan keuangan bulanan, rekonsiliasi bank, dan pelaporan pajak perusahaan.'],
            ['Fullstack Web Developer', 'Teknologi Informasi', 'nusantara', 'Kota Surabaya', 'Penuh waktu', 7500000, 11500000, 'Mengembangkan fitur web end-to-end menggunakan Laravel dan JavaScript modern.'],
            ['Driver', 'Operasional & Logistik', 'samudra', 'Kabupaten Sidoarjo', 'Penuh waktu', 4200000, 5500000, 'Mengantarkan paket dan dokumen perusahaan tepat waktu dengan menjaga keselamatan berkendara.'],
            ['Barista & Kasir Kafe', 'Perhotelan & Kuliner', 'harmoni', 'Kota Malang', 'Paruh waktu', 2800000, 3800000, 'Meracik minuman kopi sesuai standar resep, melayani transaksi kasir, dan menjaga kebersihan area bar.'],
            ['Magang Teknik Industri', 'Teknik & Manufaktur', 'sentosa', 'Kabupaten Gresik', 'Magang', 2400000, 3200000, 'Membantu pemetaan alur kerja produksi, pencatatan efisiensi mesin, dan dokumentasi K3.'],

            // Bali & Nusa Tenggara
            ['Content Marketing', 'Pemasaran & Penjualan', 'nusantara', 'Kota Denpasar', 'Paruh waktu', 4000000, 5500000, 'Menulis artikel blog, naskah media sosial, dan materi kampanye merek perusahaan.'],
            ['Front Office Hotel', 'Perhotelan & Kuliner', 'harmoni', 'Kabupaten Badung', 'Penuh waktu', 4500000, 6200000, 'Menyambut tamu hotel, menangani proses check-in/check-out, serta membantu kebutuhan reservasi.'],
            ['Chef De Partie', 'Perhotelan & Kuliner', 'harmoni', 'Kota Mataram', 'Penuh waktu', 5000000, 7000000, 'Mengelola stasiun dapur, menjaga konsistensi rasa hidangan, dan mengontrol bahan makanan harian.'],

            // Sumatera
            ['Konsultan Penjualan', 'Pemasaran & Penjualan', 'karya', 'Kota Medan', 'Penuh waktu', 5000000, 8000000, 'Memberikan konsultasi solusi bisnis kepada calon klien dan mencapai target penjualan bulanan.'],
            ['Perawat Klinik', 'Kesehatan & Farmasi', 'karya', 'Kota Medan', 'Penuh waktu', 4500000, 6000000, 'Memberikan layanan asuhan keperawatan dasar, menyiapkan tindakan medis, dan mencatat rekam medis.'],
            ['Admin Logistik Cabang', 'Operasional & Logistik', 'samudra', 'Kota Palembang', 'Penuh waktu', 4300000, 5600000, 'Menginput data manifest pengiriman, memantau armada, dan berkoordinasi dengan kurir lapangan.'],
            ['Account Executive', 'Pemasaran & Penjualan', 'cakrawala', 'Kota Pekanbaru', 'Penuh waktu', 5000000, 7500000, 'Membangun relasi dengan klien korporat, menyusun penawaran kerja sama, dan memantau proyek berjalan.'],
            ['Staf Administrasi Proyek', 'Administrasi & Perkantoran', 'sentosa', 'Kota Bandar Lampung', 'Penuh waktu', 4200000, 5400000, 'Menyusun dokumen administrasi proyek, mengarsipkan surat jalan, dan merekap absensi tim lapangan.'],
            ['Instruktur Pelatihan Komputer', 'Pendidikan & Pelatihan', 'nusantara', 'Kota Padang', 'Paruh waktu', 3500000, 4800000, 'Membimbing peserta pelatihan perkantoran dan dasar pemrograman secara tatap muka maupun daring.'],

            // Kalimantan & Sulawesi
            ['HSE Officer Pertambangan', 'Teknik & Manufaktur', 'sentosa', 'Kota Balikpapan', 'Penuh waktu', 8000000, 12000000, 'Melaksanakan inspeksi keselamatan kerja, menyusun analisis risiko, dan memberikan briefing K3 harian.'],
            ['Data Analyst', 'Teknologi Informasi', 'nusantara', 'Kota Samarinda', 'Jarak jauh', 7500000, 11000000, 'Mengolah data operasional menjadi dasbor laporan serta memberikan rekomendasi berbasis data.'],
            ['Kasir & Administrasi Cabang', 'Akuntansi & Keuangan', 'karya', 'Kota Banjarmasin', 'Penuh waktu', 4300000, 5500000, 'Mengelola transaksi harian cabang, membuat laporan kas kecil, dan rekonsiliasi setoran harian.'],
            ['Staf HRD', 'Sumber Daya Manusia', 'karya', 'Kota Makassar', 'Penuh waktu', 5200000, 7000000, 'Mengelola proses rekrutmen, onboarding karyawan baru, dan administrasi kepegawaian.'],
            ['Magang Pengembangan Web', 'Teknologi Informasi', 'nusantara', 'Kota Makassar', 'Magang', 2200000, 3000000, 'Membantu pengembangan komponen antarmuka dan perbaikan bug aplikasi web bersama mentor.'],
            ['Koordinator Operasional Wilayah', 'Operasional & Logistik', 'samudra', 'Kota Manado', 'Penuh waktu', 5800000, 7800000, 'Mengatur rute distribusi wilayah Sulawesi Utara dan memastikan target ketepatan waktu pengiriman.'],
        ];

        foreach ($daftarLowongan as [$judul, $kategori, $kodePerusahaan, $lokasi, $tipeKerja, $gajiMin, $gajiMax, $deskripsi]) {
            Lowongan::query()->firstOrCreate(
                [
                    'judul' => $judul,
                    'perusahaan_id' => $perusahaan[$kodePerusahaan]->id,
                    'lokasi' => $lokasi,
                ],
                [
                    'kategori_id' => $kategoriMap[$kategori]->id,
                    'tipe_kerja' => $tipeKerja,
                    'gaji_min' => $gajiMin,
                    'gaji_max' => $gajiMax,
                    'batas_lamaran' => now()->addMonths(2)->toDateString(),
                    'deskripsi' => $deskripsi,
                    'aktif' => true,
                ]
            );
        }
    }
}
