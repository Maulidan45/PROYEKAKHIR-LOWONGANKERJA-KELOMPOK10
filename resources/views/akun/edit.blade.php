<x-layout judul="Akun perusahaan | Lokerku">
    <div class="mx-auto w-[min(72rem,calc(100%-2rem))] py-8 sm:w-[min(72rem,calc(100%-3rem))] sm:py-12">
        <section class="min-w-0">
            @if ($errors->any())
                <div class="mb-6 rounded-md border border-red-200 bg-red-50 p-4 text-sm text-red-900" role="alert" tabindex="-1" data-kesalahan-formulir>
                    <h2 class="font-semibold">Perubahan belum disimpan.</h2>
                    <p class="mt-1">Periksa isian yang ditandai di bawah.</p>
                </div>
            @endif

            @php($profil = $pengguna->profil_pendaftaran ?? [])
            @php($bagianValidasi = $errors->hasAny(['name', 'telepon', 'bidang_usaha', 'jumlah_karyawan', 'alamat_perusahaan', 'kota_perusahaan', 'deskripsi_perusahaan', 'situs_perusahaan', 'nama_penanggung_jawab', 'jabatan_penanggung_jawab']) ? 'profil-perusahaan' : ($errors->hasAny(['email', 'current_password_email']) ? 'email-akun' : ($errors->hasAny(['current_password_password', 'password']) ? 'keamanan-akun' : null)))
            @php($bagianAwal = $bagianValidasi ?? 'profil-perusahaan')
            <div class="layout-akun">
                <nav class="navigasi-akun" aria-label="Bagian pengaturan akun" data-navigasi-akun>
                    <p class="navigasi-akun-label">Pengaturan</p>
                    <a href="#profil-perusahaan" @if ($bagianAwal === 'profil-perusahaan') aria-current="location" @endif><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="8" r="3.25"/><path d="M5 20c.7-3.2 3.1-5 7-5s6.3 1.8 7 5"/></svg><span>Profil perusahaan</span></a>
                    <a href="#email-akun" @if ($bagianValidasi === 'email-akun') aria-current="location" @endif><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m4 7 8 6 8-6"/></svg><span>Email akun</span></a>
                    <a href="#keamanan-akun" @if ($bagianValidasi === 'keamanan-akun') aria-current="location" @endif><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="5" y="10" width="14" height="11" rx="2"/><path d="M8 10V7a4 4 0 1 1 8 0v3M12 14v3"/></svg><span>Kata sandi</span></a>
                    <a href="#keluar-akun"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M15 4h4v16h-4M10 8l-4 4 4 4M6 12h9"/></svg><span>Keluar</span></a>
                </nav>
                <div class="konten-akun">
            <section id="profil-perusahaan" data-panel-akun @if ($bagianAwal !== 'profil-perusahaan') hidden @endif @class(['bagian-pengaturan-akun', 'bagian-pengaturan-validasi' => $bagianValidasi === 'profil-perusahaan'])>
                <header class="kepala-bagian-akun">
                    <div>
                        <h1>Profil perusahaan</h1>
                        <p>Informasi ini digunakan pada profil perusahaan dan verifikasi legalitas Anda.</p>
                    </div>
                    <span @class([
                        'rounded-full px-3 py-1 text-xs font-semibold',
                        'bg-emerald-50 text-emerald-800' => $pengguna->status_verifikasi === 'Terverifikasi',
                        'bg-amber-50 text-amber-800' => $pengguna->status_verifikasi === 'Menunggu Verifikasi',
                        'bg-red-50 text-red-800' => $pengguna->status_verifikasi === 'Ditolak',
                    ])>{{ $pengguna->status_verifikasi }}</span>
                </header>
                @if ($pengguna->catatan_verifikasi)
                    <div class="mb-4 rounded-xl border border-amber-200 bg-amber-50 p-3 text-sm text-amber-900">
                        <strong>Catatan Admin:</strong> {{ $pengguna->catatan_verifikasi }}
                    </div>
                @endif
                <form method="POST" action="{{ route('akun.profil.update') }}" enctype="multipart/form-data" class="form-akun form-profil" data-kirim>
                    @csrf
                    @method('PATCH')
                    <div class="isian"><label for="name">Nama perusahaan</label><input id="name" name="name" value="{{ old('name', $pengguna->name) }}" maxlength="255" required @error('name') aria-invalid="true" aria-describedby="name-error" @enderror>@error('name')<p id="name-error" class="kesalahan-isian">{{ $message }}</p>@enderror</div>
                    <div class="isian"><label for="telepon">Nomor telepon</label><input id="telepon" name="telepon" type="tel" value="{{ old('telepon', $profil['telepon'] ?? '') }}" maxlength="25" autocomplete="tel" required @error('telepon') aria-invalid="true" aria-describedby="telepon-error" @enderror>@error('telepon')<p id="telepon-error" class="kesalahan-isian">{{ $message }}</p>@enderror</div>
                    <div class="isian"><label for="bidang_usaha">Bidang usaha</label><input id="bidang_usaha" name="bidang_usaha" value="{{ old('bidang_usaha', $profil['bidang_usaha'] ?? '') }}" maxlength="100" required @error('bidang_usaha') aria-invalid="true" aria-describedby="bidang_usaha-error" @enderror>@error('bidang_usaha')<p id="bidang_usaha-error" class="kesalahan-isian">{{ $message }}</p>@enderror</div>
                    <div class="isian"><label for="jumlah_karyawan">Jumlah karyawan</label><select id="jumlah_karyawan" name="jumlah_karyawan" required @error('jumlah_karyawan') aria-invalid="true" aria-describedby="jumlah_karyawan-error" @enderror>@foreach (['1–10', '11–50', '51–200', '201–500', '501+'] as $jumlah)<option value="{{ $jumlah }}" @selected(old('jumlah_karyawan', $profil['jumlah_karyawan'] ?? '') === $jumlah)>{{ $jumlah }}</option>@endforeach</select>@error('jumlah_karyawan')<p id="jumlah_karyawan-error" class="kesalahan-isian">{{ $message }}</p>@enderror</div>
                    <div class="isian"><label for="alamat_perusahaan">Alamat perusahaan</label><textarea id="alamat_perusahaan" name="alamat_perusahaan" rows="4" maxlength="500" required @error('alamat_perusahaan') aria-invalid="true" aria-describedby="alamat_perusahaan-error" @enderror>{{ old('alamat_perusahaan', $profil['alamat_perusahaan'] ?? '') }}</textarea>@error('alamat_perusahaan')<p id="alamat_perusahaan-error" class="kesalahan-isian">{{ $message }}</p>@enderror</div>
                    <div class="isian"><label for="kota_perusahaan">Kota perusahaan</label><input id="kota_perusahaan" name="kota_perusahaan" value="{{ old('kota_perusahaan', $profil['kota_perusahaan'] ?? '') }}" maxlength="100" autocomplete="address-level2" required @error('kota_perusahaan') aria-invalid="true" aria-describedby="kota_perusahaan-error" @enderror>@error('kota_perusahaan')<p id="kota_perusahaan-error" class="kesalahan-isian">{{ $message }}</p>@enderror</div>
                    <div class="isian"><label for="deskripsi_perusahaan">Deskripsi perusahaan</label><textarea id="deskripsi_perusahaan" name="deskripsi_perusahaan" rows="6" minlength="30" maxlength="2000" required @error('deskripsi_perusahaan') aria-invalid="true" aria-describedby="deskripsi_perusahaan-error" @enderror>{{ old('deskripsi_perusahaan', $profil['deskripsi_perusahaan'] ?? '') }}</textarea>@error('deskripsi_perusahaan')<p id="deskripsi_perusahaan-error" class="kesalahan-isian">{{ $message }}</p>@enderror</div>
                    <div class="isian"><label for="situs_perusahaan">Situs perusahaan (opsional)</label><input id="situs_perusahaan" name="situs_perusahaan" type="url" value="{{ old('situs_perusahaan', $profil['situs_perusahaan'] ?? '') }}" maxlength="255" autocomplete="url" placeholder="https://perusahaan.co.id" @error('situs_perusahaan') aria-invalid="true" aria-describedby="situs_perusahaan-error" @enderror>@error('situs_perusahaan')<p id="situs_perusahaan-error" class="kesalahan-isian">{{ $message }}</p>@enderror</div>
                    <div class="isian"><label for="nama_penanggung_jawab">Nama penanggung jawab</label><input id="nama_penanggung_jawab" name="nama_penanggung_jawab" value="{{ old('nama_penanggung_jawab', $profil['nama_penanggung_jawab'] ?? '') }}" maxlength="255" autocomplete="name" required @error('nama_penanggung_jawab') aria-invalid="true" aria-describedby="nama_penanggung_jawab-error" @enderror>@error('nama_penanggung_jawab')<p id="nama_penanggung_jawab-error" class="kesalahan-isian">{{ $message }}</p>@enderror</div>
                    <div class="isian"><label for="jabatan_penanggung_jawab">Jabatan penanggung jawab</label><input id="jabatan_penanggung_jawab" name="jabatan_penanggung_jawab" value="{{ old('jabatan_penanggung_jawab', $profil['jabatan_penanggung_jawab'] ?? '') }}" maxlength="100" autocomplete="organization-title" required @error('jabatan_penanggung_jawab') aria-invalid="true" aria-describedby="jabatan_penanggung_jawab-error" @enderror>@error('jabatan_penanggung_jawab')<p id="jabatan_penanggung_jawab-error" class="kesalahan-isian">{{ $message }}</p>@enderror</div>
                    <div class="isian" style="grid-column: 1 / -1;">
                        <label for="dokumen_legalitas">Dokumen Legalitas Usaha (NIB / SIUP / Akta, PDF/JPG/PNG, maks. 5 MB)</label>
                        <input id="dokumen_legalitas" name="dokumen_legalitas" type="file" accept=".pdf,.jpg,.jpeg,.png">
                        @if ($pengguna->dokumen_legalitas_path)
                            <p class="bantuan-isian">Dokumen tersimpan. <a class="font-semibold text-blue-700 hover:underline" href="{{ route('perusahaan.legalitas', $pengguna) }}">Unduh dokumen saat ini</a></p>
                        @endif
                        @error('dokumen_legalitas')<p class="kesalahan-isian">{{ $message }}</p>@enderror
                    </div>
                    <button class="tombol" type="submit" data-teks-proses="Menyimpan…">Simpan profil</button>
                </form>
            </section>

            <section id="email-akun" data-panel-akun @if ($bagianAwal !== 'email-akun') hidden @endif @class(['bagian-pengaturan-akun', 'bagian-pengaturan-validasi' => $bagianValidasi === 'email-akun'])>
                <header class="kepala-bagian-akun"><div><h1>Email akun</h1><p>Alamat email untuk masuk dan menerima informasi akun.</p></div></header>
                <form method="POST" action="{{ route('akun.email.update') }}" class="form-akun" data-kirim>
                    @csrf
                    @method('PATCH')
                    <div class="isian"><label for="email">Email</label><input id="email" name="email" type="email" value="{{ old('email', $pengguna->email) }}" maxlength="255" autocomplete="username" inputmode="email" autocapitalize="none" spellcheck="false" required @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>@error('email')<p id="email-error" class="kesalahan-isian">{{ $message }}</p>@enderror</div>
                    <div class="isian"><label for="current_password_email">Kata sandi saat ini</label><div class="kontrol-sandi"><input id="current_password_email" name="current_password_email" type="password" autocomplete="current-password" maxlength="72" required @error('current_password_email') aria-invalid="true" aria-describedby="current_password_email-error" @enderror><button type="button" class="tombol-sandi" aria-controls="current_password_email" aria-label="Tampilkan kata sandi" aria-pressed="false" data-lihat-sandi hidden><span class="teks-tersembunyi">Tampilkan kata sandi</span></button></div>@error('current_password_email')<p id="current_password_email-error" class="kesalahan-isian">{{ $message }}</p>@enderror</div>
                    <button class="tombol" type="submit" data-teks-proses="Menyimpan…">Simpan email</button>
                </form>
            </section>

            <section id="keamanan-akun" data-panel-akun @if ($bagianAwal !== 'keamanan-akun') hidden @endif @class(['bagian-pengaturan-akun', 'bagian-pengaturan-validasi' => $bagianValidasi === 'keamanan-akun'])>
                <header class="kepala-bagian-akun"><div><h1>Kata sandi</h1><p>Gunakan kata sandi yang kuat dan tidak digunakan di akun lain.</p></div></header>
                <form method="POST" action="{{ route('akun.kata_sandi.update') }}" class="form-akun" data-kirim>
                    @csrf
                    @method('PATCH')
                    <div class="isian"><label for="current_password_password">Kata sandi saat ini</label><div class="kontrol-sandi"><input id="current_password_password" name="current_password_password" type="password" autocomplete="current-password" maxlength="72" required @error('current_password_password') aria-invalid="true" aria-describedby="current_password_password-error" @enderror><button type="button" class="tombol-sandi" aria-controls="current_password_password" aria-label="Tampilkan kata sandi" aria-pressed="false" data-lihat-sandi hidden><span class="teks-tersembunyi">Tampilkan kata sandi</span></button></div>@error('current_password_password')<p id="current_password_password-error" class="kesalahan-isian">{{ $message }}</p>@enderror</div>
                    <div class="isian"><label for="password">Kata sandi baru</label><div class="kontrol-sandi"><input id="password" name="password" type="password" autocomplete="new-password" minlength="12" maxlength="72" required @error('password') aria-invalid="true" aria-describedby="password-help password-error" @else aria-describedby="password-help" @enderror><button type="button" class="tombol-sandi" aria-controls="password" aria-label="Tampilkan kata sandi" aria-pressed="false" data-lihat-sandi hidden><span class="teks-tersembunyi">Tampilkan kata sandi</span></button></div><p id="password-help" class="bantuan-isian">Minimal 12 karakter, maksimal 72 byte.</p>@error('password')<p id="password-error" class="kesalahan-isian">{{ $message }}</p>@enderror</div>
                    <div class="isian"><label for="password_confirmation">Ulangi kata sandi baru</label><div class="kontrol-sandi"><input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" minlength="12" maxlength="72" required><button type="button" class="tombol-sandi" aria-controls="password_confirmation" aria-label="Tampilkan konfirmasi kata sandi" aria-pressed="false" data-lihat-sandi hidden><span class="teks-tersembunyi">Tampilkan konfirmasi kata sandi</span></button></div></div>
                    <button class="tombol" type="submit" data-teks-proses="Menyimpan…">Ubah kata sandi</button>
                </form>
            </section>

            <section id="keluar-akun" class="bagian-pengaturan-akun bagian-keluar-akun" data-panel-akun @if ($bagianAwal !== 'keluar-akun') hidden @endif>
                <div><h1>Keluar dari akun</h1><p>Akhiri sesi akun ini pada perangkat yang sedang digunakan.</p></div>
                <form method="POST" action="{{ route('logout') }}" data-kirim>
                    @csrf
                    <button class="tombol tombol-sekunder" type="submit" data-teks-proses="Keluar…">Keluar</button>
                </form>
            </section>
                </div>
            </div>
        </section>
    </div>
</x-layout>