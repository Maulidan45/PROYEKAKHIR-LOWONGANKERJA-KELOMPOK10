<x-layout judul="Profil & Akun | Lokerku">
    @php($profil = $pengguna->profil_pendaftaran ?? [])
    <div class="mx-auto w-[min(72rem,calc(100%-2rem))] py-8 sm:w-[min(72rem,calc(100%-3rem))] sm:py-12">
        <section class="min-w-0" aria-label="Pengaturan akun">
            @if ($errors->any())
                <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-900" role="alert" tabindex="-1">
                    <h2 class="font-semibold">Perubahan belum disimpan.</h2>
                    <p class="mt-1">Periksa isian yang ditandai di bawah.</p>
                </div>
            @endif
            @php($bagianValidasi = $errors->hasAny(['name', 'telepon', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'domisili', 'pendidikan_terakhir', 'pengalaman_kerja', 'keahlian']) ? 'profil-akun' : ($errors->hasAny(['email', 'current_password_email']) ? 'email-akun' : ($errors->hasAny(['current_password_password', 'password']) ? 'keamanan-akun' : null)))
            @php($bagianAwal = $bagianValidasi ?? 'profil-akun')
            <div class="layout-akun">
                <nav class="navigasi-akun" aria-label="Bagian pengaturan akun" data-navigasi-akun>
                    <p class="navigasi-akun-label">Pengaturan</p>
                    <a href="#profil-akun" @if ($bagianAwal === 'profil-akun') aria-current="location" @endif><span>Profil Pribadi</span></a>
                    <a href="#email-akun" @if ($bagianValidasi === 'email-akun') aria-current="location" @endif><span>Email Akun</span></a>
                    <a href="#keamanan-akun" @if ($bagianValidasi === 'keamanan-akun') aria-current="location" @endif><span>Kata Sandi</span></a>
                    <a href="#keluar-akun"><span>Keluar</span></a>
                </nav>
                <div class="konten-akun">
                    <section id="profil-akun" data-panel-akun @if ($bagianAwal !== 'profil-akun') hidden @endif @class(['bagian-pengaturan-akun', 'bagian-pengaturan-validasi' => $bagianValidasi === 'profil-akun'])>
                        <header class="kepala-bagian-akun">
                            <div>
                                <h1>Profil Pencari Kerja</h1>
                                <p>Kelengkapan profil: <strong>{{ $pengguna->persentaseKelengkapanProfil() }}%</strong>. Data ini otomatis mengisi formulir lamaran Anda.</p>
                            </div>
                        </header>
                        <form method="POST" action="{{ route('akun.profil.update') }}" class="form-akun form-profil" data-kirim>
                            @csrf
                            @method('PATCH')
                            <div class="isian">
                                <label for="name">Nama lengkap</label>
                                <input id="name" name="name" value="{{ old('name', $pengguna->name) }}" maxlength="255" autocomplete="name" required>
                                @error('name')<p class="kesalahan-isian">{{ $message }}</p>@enderror
                            </div>
                            <div class="isian">
                                <label for="telepon">Nomor telepon</label>
                                <input id="telepon" name="telepon" type="tel" value="{{ old('telepon', $profil['telepon'] ?? '') }}" maxlength="25" placeholder="081234567890">
                                @error('telepon')<p class="kesalahan-isian">{{ $message }}</p>@enderror
                            </div>
                            <div class="isian">
                                <label for="tempat_lahir">Tempat lahir</label>
                                <input id="tempat_lahir" name="tempat_lahir" value="{{ old('tempat_lahir', $profil['tempat_lahir'] ?? '') }}" maxlength="100">
                                @error('tempat_lahir')<p class="kesalahan-isian">{{ $message }}</p>@enderror
                            </div>
                            <div class="isian">
                                <label for="tanggal_lahir">Tanggal lahir</label>
                                <input id="tanggal_lahir" name="tanggal_lahir" type="date" value="{{ old('tanggal_lahir', $profil['tanggal_lahir'] ?? '') }}">
                                @error('tanggal_lahir')<p class="kesalahan-isian">{{ $message }}</p>@enderror
                            </div>
                            <div class="isian">
                                <label for="jenis_kelamin">Jenis kelamin</label>
                                <select id="jenis_kelamin" name="jenis_kelamin">
                                    <option value="">Pilih jenis kelamin</option>
                                    @foreach (['Laki-laki', 'Perempuan'] as $jk)
                                        <option value="{{ $jk }}" @selected(old('jenis_kelamin', $profil['jenis_kelamin'] ?? '') === $jk)>{{ $jk }}</option>
                                    @endforeach
                                </select>
                                @error('jenis_kelamin')<p class="kesalahan-isian">{{ $message }}</p>@enderror
                            </div>
                            <div class="isian">
                                <label for="domisili">Domisili / Kota</label>
                                <input id="domisili" name="domisili" value="{{ old('domisili', $profil['domisili'] ?? '') }}" maxlength="255">
                                @error('domisili')<p class="kesalahan-isian">{{ $message }}</p>@enderror
                            </div>
                            <div class="isian">
                                <label for="pendidikan_terakhir">Pendidikan terakhir</label>
                                <select id="pendidikan_terakhir" name="pendidikan_terakhir">
                                    <option value="">Pilih pendidikan</option>
                                    @foreach (['SD', 'SMP', 'SMA/SMK', 'Diploma', 'S1', 'S2', 'S3'] as $pend)
                                        <option value="{{ $pend }}" @selected(old('pendidikan_terakhir', $profil['pendidikan_terakhir'] ?? '') === $pend)>{{ $pend }}</option>
                                    @endforeach
                                </select>
                                @error('pendidikan_terakhir')<p class="kesalahan-isian">{{ $message }}</p>@enderror
                            </div>
                            <div class="isian">
                                <label for="keahlian">Keahlian (pisahkan dengan koma)</label>
                                <input id="keahlian" name="keahlian" value="{{ old('keahlian', $profil['keahlian'] ?? '') }}" placeholder="Contoh: Laravel, PHP, MySQL, Excel">
                                @error('keahlian')<p class="kesalahan-isian">{{ $message }}</p>@enderror
                            </div>
                            <div class="isian">
                                <label for="pengalaman_kerja">Pengalaman kerja</label>
                                <textarea id="pengalaman_kerja" name="pengalaman_kerja" rows="3" placeholder="Ringkasan pengalaman kerja atau magang...">{{ old('pengalaman_kerja', $profil['pengalaman_kerja'] ?? '') }}</textarea>
                                @error('pengalaman_kerja')<p class="kesalahan-isian">{{ $message }}</p>@enderror
                            </div>
                            <button class="tombol-utama" type="submit" data-teks-proses="Menyimpan…">Simpan Profil</button>
                        </form>
                    </section>
                    <section id="email-akun" data-panel-akun @if ($bagianAwal !== 'email-akun') hidden @endif @class(['bagian-pengaturan-akun', 'bagian-pengaturan-validasi' => $bagianValidasi === 'email-akun'])>
                        <header class="kepala-bagian-akun"><div><h1>Email akun</h1><p>Gunakan email yang dapat Anda akses untuk masuk ke akun.</p></div></header>
                        <form method="POST" action="{{ route('akun.email.update') }}" class="form-akun">
                            @csrf
                            @method('PATCH')
                            <div class="isian"><label for="email">Email</label><input id="email" name="email" type="email" value="{{ old('email', $pengguna->email) }}" maxlength="255" autocomplete="username" required>@error('email')<p class="kesalahan-isian">{{ $message }}</p>@enderror</div>
                            <div class="isian"><label for="current_password_email">Kata sandi saat ini</label><input id="current_password_email" name="current_password_email" type="password" autocomplete="current-password" maxlength="72" required>@error('current_password_email')<p class="kesalahan-isian">{{ $message }}</p>@enderror</div>
                            <button class="tombol-utama" type="submit">Simpan email</button>
                        </form>
                    </section>
                    <section id="keamanan-akun" data-panel-akun @if ($bagianAwal !== 'keamanan-akun') hidden @endif @class(['bagian-pengaturan-akun', 'bagian-pengaturan-validasi' => $bagianValidasi === 'keamanan-akun'])>
                        <header class="kepala-bagian-akun"><div><h1>Kata sandi</h1><p>Gunakan kata sandi baru minimal 12 karakter.</p></div></header>
                        <form method="POST" action="{{ route('akun.kata_sandi.update') }}" class="form-akun">
                            @csrf
                            @method('PATCH')
                            <div class="isian"><label for="current_password_password">Kata sandi saat ini</label><input id="current_password_password" name="current_password_password" type="password" autocomplete="current-password" maxlength="72" required>@error('current_password_password')<p class="kesalahan-isian">{{ $message }}</p>@enderror</div>
                            <div class="isian"><label for="password">Kata sandi baru</label><input id="password" name="password" type="password" autocomplete="new-password" minlength="12" maxlength="72" required><p class="bantuan-isian">Minimal 12 karakter, maksimal 72 byte.</p>@error('password')<p class="kesalahan-isian">{{ $message }}</p>@enderror</div>
                            <div class="isian"><label for="password_confirmation">Ulangi kata sandi baru</label><input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" minlength="12" maxlength="72" required></div>
                            <button class="tombol-utama" type="submit">Ubah kata sandi</button>
                        </form>
                    </section>
                    <section id="keluar-akun" class="bagian-pengaturan-akun bagian-keluar-akun" data-panel-akun @if ($bagianAwal !== 'keluar-akun') hidden @endif>
                        <div><h1>Keluar dari akun</h1><p>Akhiri sesi akun ini pada perangkat yang sedang digunakan.</p></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="tombol-kedua" type="submit">Keluar</button>
                        </form>
                    </section>
                </div>
            </div>
        </section>
    </div>
</x-layout>
