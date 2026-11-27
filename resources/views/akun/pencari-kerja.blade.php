<x-panel-user judul="Profil & Akun" aktif="akun">
    @php($profil = $pengguna->profil_pendaftaran ?? [])
    <section class="min-w-0 rounded-3xl border-[1.5px] border-slate-200/90 bg-white/85 p-5 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.08)] backdrop-blur-xl sm:p-7" aria-label="Pengaturan akun">
            @if ($errors->any())
                <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-900" role="alert" tabindex="-1">
                    <h2 class="font-semibold">Perubahan belum disimpan.</h2>
                    <p class="mt-1">Periksa isian yang ditandai di bawah.</p>
                </div>
            @endif
            @php($bagianValidasi = $errors->hasAny(['name', 'telepon', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'domisili', 'alamat_lengkap', 'pendidikan_terakhir', 'institusi_pendidikan', 'jurusan', 'tahun_lulus', 'pengalaman_kerja', 'keahlian', 'ringkasan_pribadi', 'portofolio_url', 'ekspektasi_gaji', 'foto_profil', 'cv_default', 'surat_lamaran_default', 'ijazah_default']) ? 'profil-akun' : ($errors->hasAny(['email', 'current_password_email']) ? 'email-akun' : ($errors->hasAny(['current_password_password', 'password']) ? 'keamanan-akun' : null)))
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
                        <form method="POST" action="{{ route('akun.profil.update') }}" enctype="multipart/form-data" class="form-akun form-profil" data-kirim>
                            @csrf
                            @method('PATCH')
                            <div class="isian sm:col-span-2">
                                <label for="foto_profil_user">Foto Profil (JPG, PNG, WEBP, maks 2 MB)</label>
                                <div class="mt-1 flex flex-wrap items-center gap-4">
                                    @if ($pengguna->foto_profil_path)
                                        <img src="{{ asset('storage/' . $pengguna->foto_profil_path) }}" alt="{{ $pengguna->name }}" class="size-16 rounded-full border border-slate-200 object-cover">
                                    @else
                                        <span class="grid size-16 place-items-center rounded-full bg-[#E0F2FE] text-xl font-extrabold text-[#007AFF]">{{ mb_strtoupper(mb_substr($pengguna->name, 0, 1)) }}</span>
                                    @endif
                                    <input id="foto_profil_user" name="foto_profil" type="file" accept=".jpg,.jpeg,.png,.webp" class="flex-1">
                                </div>
                                @error('foto_profil')<p class="kesalahan-isian">{{ $message }}</p>@enderror
                            </div>
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
                                <label for="alamat_lengkap">Alamat lengkap</label>
                                <input id="alamat_lengkap" name="alamat_lengkap" value="{{ old('alamat_lengkap', $profil['alamat_lengkap'] ?? '') }}" maxlength="500" placeholder="Jalan, RT/RW, Kecamatan">
                                @error('alamat_lengkap')<p class="kesalahan-isian">{{ $message }}</p>@enderror
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
                                <label for="institusi_pendidikan">Nama Sekolah / Universitas</label>
                                <input id="institusi_pendidikan" name="institusi_pendidikan" value="{{ old('institusi_pendidikan', $profil['institusi_pendidikan'] ?? '') }}" maxlength="255" placeholder="Contoh: Universitas Indonesia">
                                @error('institusi_pendidikan')<p class="kesalahan-isian">{{ $message }}</p>@enderror
                            </div>
                            <div class="isian">
                                <label for="jurusan">Jurusan / Program Studi</label>
                                <input id="jurusan" name="jurusan" value="{{ old('jurusan', $profil['jurusan'] ?? '') }}" maxlength="150" placeholder="Contoh: Teknik Informatika">
                                @error('jurusan')<p class="kesalahan-isian">{{ $message }}</p>@enderror
                            </div>
                            <div class="isian">
                                <label for="tahun_lulus">Tahun Lulus</label>
                                <input id="tahun_lulus" name="tahun_lulus" type="number" min="1960" max="{{ (int) date('Y') + 5 }}" value="{{ old('tahun_lulus', $profil['tahun_lulus'] ?? '') }}" placeholder="Contoh: 2025">
                                @error('tahun_lulus')<p class="kesalahan-isian">{{ $message }}</p>@enderror
                            </div>
                            <div class="isian">
                                <label for="ekspektasi_gaji">Ekspektasi Gaji</label>
                                <input id="ekspektasi_gaji" name="ekspektasi_gaji" value="{{ old('ekspektasi_gaji', $profil['ekspektasi_gaji'] ?? '') }}" maxlength="100" placeholder="Contoh: Rp 6.000.000 - Rp 8.000.000">
                                @error('ekspektasi_gaji')<p class="kesalahan-isian">{{ $message }}</p>@enderror
                            </div>
                            <div class="isian">
                                <label for="portofolio_url">Tautan Portofolio / LinkedIn / GitHub</label>
                                <input id="portofolio_url" name="portofolio_url" type="url" value="{{ old('portofolio_url', $profil['portofolio_url'] ?? '') }}" placeholder="https://...">
                                @error('portofolio_url')<p class="kesalahan-isian">{{ $message }}</p>@enderror
                            </div>
                            <div class="isian">
                                <label for="keahlian">Keahlian (pisahkan dengan koma)</label>
                                <input id="keahlian" name="keahlian" value="{{ old('keahlian', $profil['keahlian'] ?? '') }}" placeholder="Contoh: Laravel, PHP, MySQL, Excel">
                                @error('keahlian')<p class="kesalahan-isian">{{ $message }}</p>@enderror
                            </div>
                            <div class="isian">
                                <label for="ringkasan_pribadi">Tentang Saya / Ringkasan Diri</label>
                                <textarea id="ringkasan_pribadi" name="ringkasan_pribadi" rows="2" placeholder="Ceritakan singkat latar belakang dan tujuan karier Anda...">{{ old('ringkasan_pribadi', $profil['ringkasan_pribadi'] ?? '') }}</textarea>
                                @error('ringkasan_pribadi')<p class="kesalahan-isian">{{ $message }}</p>@enderror
                            </div>
                            <div class="isian">
                                <label for="pengalaman_kerja">Pengalaman kerja</label>
                                <textarea id="pengalaman_kerja" name="pengalaman_kerja" rows="3" placeholder="Ringkasan pengalaman kerja atau magang...">{{ old('pengalaman_kerja', $profil['pengalaman_kerja'] ?? '') }}</textarea>
                                @error('pengalaman_kerja')<p class="kesalahan-isian">{{ $message }}</p>@enderror
                            </div>
                            <div class="isian">
                                <label for="cv_default">Berkas CV Utama (PDF, maks 5 MB) @if(!empty($profil['cv_default_path'])) <span class="text-emerald-600 font-bold">(Sudah terunggah)</span> @endif</label>
                                <input id="cv_default" name="cv_default" type="file" accept=".pdf">
                                @error('cv_default')<p class="kesalahan-isian">{{ $message }}</p>@enderror
                            </div>
                            <div class="isian">
                                <label for="surat_lamaran_default">Berkas Surat Lamaran Utama (PDF, maks 5 MB) @if(!empty($profil['surat_lamaran_default_path'])) <span class="text-emerald-600 font-bold">(Sudah terunggah)</span> @endif</label>
                                <input id="surat_lamaran_default" name="surat_lamaran_default" type="file" accept=".pdf">
                                @error('surat_lamaran_default')<p class="kesalahan-isian">{{ $message }}</p>@enderror
                            </div>
                            <div class="isian">
                                <label for="ijazah_default">Berkas Ijazah Terakhir (PDF/JPG/PNG, maks 5 MB) @if(!empty($profil['ijazah_default_path'])) <span class="text-emerald-600 font-bold">(Sudah terunggah)</span> @endif</label>
                                <input id="ijazah_default" name="ijazah_default" type="file" accept=".pdf,.jpg,.jpeg,.png">
                                @error('ijazah_default')<p class="kesalahan-isian">{{ $message }}</p>@enderror
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
</x-panel-user>
