<x-layout judul="Akun saya | Lokerku">
    <div class="mx-auto w-[min(72rem,calc(100%-2rem))] py-8 sm:w-[min(72rem,calc(100%-3rem))] sm:py-12">
        <section class="min-w-0" aria-label="Pengaturan akun">
            @if ($errors->any())
                <div class="mb-6 rounded-md border border-red-200 bg-red-50 p-4 text-sm text-red-900" role="alert" tabindex="-1">
                    <h2 class="font-semibold">Perubahan belum disimpan.</h2>
                    <p class="mt-1">Periksa isian yang ditandai di bawah.</p>
                </div>
            @endif
            @php($bagianValidasi = $errors->has('name') ? 'profil-akun' : ($errors->hasAny(['email', 'current_password_email']) ? 'email-akun' : ($errors->hasAny(['current_password_password', 'password']) ? 'keamanan-akun' : null)))
            @php($bagianAwal = $bagianValidasi ?? 'profil-akun')
            <div class="layout-akun">
                <nav class="navigasi-akun" aria-label="Bagian pengaturan akun">
                    <p class="navigasi-akun-label">Pengaturan</p>
                    <a href="#profil-akun" @if ($bagianAwal === 'profil-akun') aria-current="location" @endif><span>Profil</span></a>
                    <a href="#email-akun" @if ($bagianValidasi === 'email-akun') aria-current="location" @endif><span>Email akun</span></a>
                    <a href="#keamanan-akun" @if ($bagianValidasi === 'keamanan-akun') aria-current="location" @endif><span>Kata sandi</span></a>
                    <a href="#keluar-akun"><span>Keluar</span></a>
                </nav>
                <div class="konten-akun">
                    <section id="profil-akun" data-panel-akun @if ($bagianAwal !== 'profil-akun') hidden @endif @class(['bagian-pengaturan-akun', 'bagian-pengaturan-validasi' => $bagianValidasi === 'profil-akun'])>
                        <header class="kepala-bagian-akun"><div><h1>Akun saya</h1><p>Perbarui nama yang tampil pada lamaran Anda.</p></div></header>
                        <form method="POST" action="{{ route('akun.profil.update') }}" class="form-akun">
                            @csrf
                            @method('PATCH')
                            <div class="isian"><label for="name">Nama</label><input id="name" name="name" value="{{ old('name', $pengguna->name) }}" maxlength="255" autocomplete="name" required @error('name') aria-invalid="true" aria-describedby="name-error" @enderror>@error('name')<p id="name-error" class="kesalahan-isian">{{ $message }}</p>@enderror</div>
                            <button class="tombol" type="submit">Simpan nama</button>
                        </form>
                    </section>
                    <section id="email-akun" data-panel-akun @if ($bagianAwal !== 'email-akun') hidden @endif @class(['bagian-pengaturan-akun', 'bagian-pengaturan-validasi' => $bagianValidasi === 'email-akun'])>
                        <header class="kepala-bagian-akun"><div><h1>Email akun</h1><p>Gunakan email yang dapat Anda akses untuk masuk ke akun.</p></div></header>
                        <form method="POST" action="{{ route('akun.email.update') }}" class="form-akun">
                            @csrf
                            @method('PATCH')
                            <div class="isian"><label for="email">Email</label><input id="email" name="email" type="email" value="{{ old('email', $pengguna->email) }}" maxlength="255" autocomplete="username" inputmode="email" autocapitalize="none" spellcheck="false" required @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>@error('email')<p id="email-error" class="kesalahan-isian">{{ $message }}</p>@enderror</div>
                            <div class="isian"><label for="current_password_email">Kata sandi saat ini</label><input id="current_password_email" name="current_password_email" type="password" autocomplete="current-password" maxlength="72" required @error('current_password_email') aria-invalid="true" aria-describedby="current_password_email-error" @enderror>@error('current_password_email')<p id="current_password_email-error" class="kesalahan-isian">{{ $message }}</p>@enderror</div>
                            <button class="tombol" type="submit">Simpan email</button>
                        </form>
                    </section>
                    <section id="keamanan-akun" data-panel-akun @if ($bagianAwal !== 'keamanan-akun') hidden @endif @class(['bagian-pengaturan-akun', 'bagian-pengaturan-validasi' => $bagianValidasi === 'keamanan-akun'])>
                        <header class="kepala-bagian-akun"><div><h1>Kata sandi</h1><p>Gunakan kata sandi baru minimal 12 karakter.</p></div></header>
                        <form method="POST" action="{{ route('akun.kata_sandi.update') }}" class="form-akun">
                            @csrf
                            @method('PATCH')
                            <div class="isian"><label for="current_password_password">Kata sandi saat ini</label><input id="current_password_password" name="current_password_password" type="password" autocomplete="current-password" maxlength="72" required @error('current_password_password') aria-invalid="true" aria-describedby="current_password_password-error" @enderror>@error('current_password_password')<p id="current_password_password-error" class="kesalahan-isian">{{ $message }}</p>@enderror</div>
                            <div class="isian"><label for="password">Kata sandi baru</label><input id="password" name="password" type="password" autocomplete="new-password" minlength="12" maxlength="72" required @error('password') aria-invalid="true" aria-describedby="password-help password-error" @else aria-describedby="password-help" @enderror><p id="password-help" class="bantuan-isian">Minimal 12 karakter, maksimal 72 byte.</p>@error('password')<p id="password-error" class="kesalahan-isian">{{ $message }}</p>@enderror</div>
                            <div class="isian"><label for="password_confirmation">Ulangi kata sandi baru</label><input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" minlength="12" maxlength="72" required></div>
                            <button class="tombol" type="submit">Ubah kata sandi</button>
                        </form>
                    </section>
                    <section id="keluar-akun" class="bagian-pengaturan-akun bagian-keluar-akun" data-panel-akun @if ($bagianAwal !== 'keluar-akun') hidden @endif>
                        <div><h1>Keluar dari akun</h1><p>Akhiri sesi akun ini pada perangkat yang sedang digunakan.</p></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="tombol tombol-sekunder" type="submit">Keluar</button>
                        </form>
                    </section>
                </div>
            </div>
        </section>
    </div>
</x-layout>