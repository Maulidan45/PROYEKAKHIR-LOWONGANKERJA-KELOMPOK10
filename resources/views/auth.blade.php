<x-layout :judul="($pendaftaran ? 'Daftar' : 'Masuk').' | Lokerku'" :halaman-autentikasi="true">
    <div class="pembungkus halaman-autentikasi-form">
        <section @class(['panel-autentikasi' => true, 'panel-autentikasi-pendaftaran' => $pendaftaran, 'panel-autentikasi-masuk' => ! $pendaftaran]) aria-labelledby="form-title">
            <h2 id="form-title">{{ $pendaftaran ? 'Buat akun' : 'Selamat datang kembali' }}</h2>
            <p class="deskripsi-formulir">{{ $pendaftaran ? 'Lengkapi data untuk membuat akun kariermu.' : 'Masuk dengan email dan kata sandi yang terdaftar.' }}</p>
            @if ($errors->any())
                <div class="ringkasan-kesalahan" role="alert" tabindex="-1" data-kesalahan-formulir>
                    <h3>{{ $pendaftaran ? 'Akun belum dapat dibuat.' : 'Belum berhasil masuk.' }}</h3>
                    <p>Periksa isian yang ditandai di bawah.</p>
                </div>
            @endif
            <form action="{{ route($pendaftaran ? 'register.store' : 'login.store') }}" method="POST" class="formulir-autentikasi" data-kirim>
                @csrf
                @if ($pendaftaran)
                    <fieldset class="pilihan-akun" @if ($errors->has('role')) aria-describedby="role-error" @endif>
                        <legend>Jenis akun</legend>
                        <label>
                            <input type="radio" name="role" value="user" required @checked(old('role', 'user') === 'user')>
                            <span>Pencari kerja<small>Untuk akun pribadi</small></span>
                        </label>
                        <label>
                            <input type="radio" name="role" value="perusahaan" required @checked(old('role', 'user') === 'perusahaan')>
                            <span>Perusahaan<small>Untuk tim rekrutmen</small></span>
                        </label>
                    </fieldset>
                    @error('role')<p id="role-error" class="kesalahan-isian">{{ $message }}</p>@enderror
                    <div class="isian">
                        <label for="name" data-label-nama>{{ old('role') === 'perusahaan' ? 'Nama perusahaan' : 'Nama lengkap' }}</label>
                        <input id="name" name="name" type="text" value="{{ is_string(old('name')) ? old('name') : '' }}" autocomplete="name" maxlength="255" required @if ($errors->has('name')) aria-invalid="true" aria-describedby="name-error" @endif>
                        @error('name')<p id="name-error" class="kesalahan-isian">{{ $message }}</p>@enderror
                    </div>
                    <x-isian-pendaftaran nama="telepon" keterangan="Nomor telepon" jenis="tel" :batas="25" autocomplete="tel" />
                    <noscript><p class="bantuan-isian">Isi bagian sesuai jenis akun yang dipilih. Data bagian lain tidak akan disimpan.</p></noscript>
                    <fieldset class="bagian-pendaftaran" data-bagian-akun="user">
                        <legend>Profil pencari kerja</legend>
                        <x-isian-pendaftaran nama="domisili" keterangan="Domisili" :batas="100" autocomplete="address-level2" />
                        <x-isian-pendaftaran nama="pendidikan_terakhir" keterangan="Pendidikan terakhir" jenis="select" :pilihan="['SD', 'SMP', 'SMA/SMK', 'Diploma', 'S1', 'S2', 'S3']" />
                    </fieldset>
                    <fieldset class="bagian-pendaftaran" data-bagian-akun="perusahaan">
                        <legend>Profil perusahaan</legend>
                        <x-isian-pendaftaran nama="bidang_usaha" keterangan="Bidang usaha" :batas="100" placeholder="Contoh: Teknologi informasi" />
                        <x-isian-pendaftaran nama="jumlah_karyawan" keterangan="Jumlah karyawan" jenis="select" :pilihan="['1–10', '11–50', '51–200', '201–500', '501+']" />
                        <x-isian-pendaftaran nama="alamat_perusahaan" keterangan="Alamat perusahaan" jenis="textarea" :batas="500" autocomplete="street-address" />
                        <x-isian-pendaftaran nama="kota_perusahaan" keterangan="Kota perusahaan" :batas="100" autocomplete="address-level2" />
                        <x-isian-pendaftaran nama="deskripsi_perusahaan" keterangan="Deskripsi perusahaan" jenis="textarea" :minimal="30" :batas="2000" placeholder="Ceritakan kegiatan perusahaan (minimal 30 karakter)." />
                        <x-isian-pendaftaran nama="situs_perusahaan" keterangan="Situs perusahaan" jenis="url" :wajib="false" autocomplete="url" placeholder="https://perusahaan.co.id" />
                        <x-isian-pendaftaran nama="nama_penanggung_jawab" keterangan="Nama penanggung jawab" autocomplete="name" />
                        <x-isian-pendaftaran nama="jabatan_penanggung_jawab" keterangan="Jabatan penanggung jawab" :batas="100" autocomplete="organization-title" />
                    </fieldset>
                @endif
                <div class="isian">
                    <label for="email">Email</label>
                    <input id="email" name="email" type="email" value="{{ is_string(old('email')) ? old('email') : '' }}" autocomplete="username" inputmode="email" autocapitalize="none" spellcheck="false" maxlength="255" required @if ($errors->has('email')) aria-invalid="true" aria-describedby="email-error" @endif>
                    @error('email')<p id="email-error" class="kesalahan-isian">{{ $message }}</p>@enderror
                </div>
                <div class="isian">
                    <label for="password">Kata sandi</label>
                    <div class="kontrol-sandi">
                        <input id="password" name="password" type="password" autocomplete="{{ $pendaftaran ? 'new-password' : 'current-password' }}" required @if ($pendaftaran) minlength="12" maxlength="72" @else maxlength="1000" @endif @if ($pendaftaran || $errors->has('password')) aria-describedby="{{ $pendaftaran ? 'password-help ' : '' }}{{ $errors->has('password') ? 'password-error' : '' }}" @endif @if ($errors->has('password')) aria-invalid="true" @endif>
                        <button type="button" class="tombol-sandi" aria-controls="password" aria-label="Tampilkan kata sandi" aria-pressed="false" data-lihat-sandi hidden><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/><circle cx="12" cy="12" r="2.5"/></svg><span class="teks-tersembunyi">Tampilkan kata sandi</span></button>
                    </div>
                    @if ($pendaftaran)<p id="password-help" class="bantuan-isian">Minimal 12 karakter, maksimal 72 byte. Gunakan kata sandi yang belum dipakai di akun lain.</p>@endif
                    @error('password')<p id="password-error" class="kesalahan-isian">{{ $message }}</p>@enderror
                </div>
                @if ($pendaftaran)
                    <div class="isian">
                        <label for="password_confirmation">Ulangi kata sandi</label>
                        <div class="kontrol-sandi">
                            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" minlength="12" maxlength="72" required>
                            <button type="button" class="tombol-sandi" aria-controls="password_confirmation" aria-label="Tampilkan konfirmasi kata sandi" aria-pressed="false" data-lihat-sandi hidden><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/><circle cx="12" cy="12" r="2.5"/></svg><span class="teks-tersembunyi">Tampilkan konfirmasi kata sandi</span></button>
                        </div>
                    </div>
                @endif
                <button class="tombol" type="submit" data-teks-proses="{{ $pendaftaran ? 'Membuat akun…' : 'Memeriksa akun…' }}">{{ $pendaftaran ? 'Buat akun' : 'Masuk' }}</button>
            </form>
            <p class="alih-autentikasi">
                {{ $pendaftaran ? 'Sudah punya akun?' : 'Belum punya akun?' }}
                <a href="{{ route($pendaftaran ? 'login' : 'register') }}">{{ $pendaftaran ? 'Masuk' : 'Daftar akun' }}</a>
            </p>
        </section>
    </div>
</x-layout>