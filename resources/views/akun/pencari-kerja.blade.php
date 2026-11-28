<x-panel-user judul="Profil & Akun" aktif="akun">
    @php($profil = $pengguna->profil_pendaftaran ?? [])
    @php($kelengkapan = $pengguna->persentaseKelengkapanProfil())
    @php($bagianValidasi = $errors->hasAny(['name', 'telepon', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'domisili', 'alamat_lengkap', 'pendidikan_terakhir', 'institusi_pendidikan', 'jurusan', 'tahun_lulus', 'pengalaman_kerja', 'keahlian', 'ringkasan_pribadi', 'portofolio_url', 'ekspektasi_gaji', 'foto_profil', 'cv_default', 'surat_lamaran_default', 'ijazah_default']) ? 'profil-akun' : ($errors->hasAny(['email', 'current_password_email']) ? 'email-akun' : ($errors->hasAny(['current_password_password', 'password']) ? 'keamanan-akun' : null)))
    @php($bagianAwal = $bagianValidasi ?? 'profil-akun')

    @if ($errors->any())
        <div class="rounded-3xl border-[1.5px] border-rose-300 bg-rose-50/90 px-5 py-4 text-sm text-rose-900 shadow-xs backdrop-blur-xl" role="alert" tabindex="-1">
            <h2 class="font-bold">Perubahan belum disimpan.</h2>
            <p class="mt-0.5 text-xs">Periksa kembali isian yang ditandai di bawah.</p>
        </div>
    @endif

    <!-- Hero Profile Card with Completeness Progress -->
    <section class="overflow-hidden rounded-3xl border-[1.5px] border-slate-200/90 bg-white/85 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.08)] backdrop-blur-xl">
        <div class="h-20 bg-gradient-to-r from-[#007AFF] via-[#0A84FF] to-sky-400 sm:h-24"></div>
        <div class="px-5 pb-5 sm:px-7 sm:pb-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-4">
                    @if ($pengguna->foto_profil_path)
                        <img src="{{ asset('storage/' . $pengguna->foto_profil_path) }}" alt="{{ $pengguna->name }}" class="-mt-10 size-18 shrink-0 rounded-full border-4 border-white bg-white object-cover shadow-md sm:-mt-12 sm:size-20">
                    @else
                        <span class="-mt-10 grid size-18 shrink-0 place-items-center rounded-full border-4 border-white bg-[#E0F2FE] text-xl font-extrabold text-[#007AFF] shadow-md sm:-mt-12 sm:size-20">{{ mb_strtoupper(mb_substr($pengguna->name, 0, 1)) }}</span>
                    @endif
                    <div class="min-w-0 pt-2">
                        <h1 class="truncate !text-base font-extrabold !leading-tight text-slate-900 sm:!text-lg">{{ $pengguna->name }}</h1>
                        <p class="mt-0.5 truncate text-xs text-slate-500">{{ $pengguna->email }} &middot; {{ $profil['domisili'] ?? 'Lengkapi domisili' }}</p>
                    </div>
                </div>

                <!-- Completeness Pill & Bar -->
                <div class="w-full rounded-2xl border border-slate-200/80 bg-slate-50/90 p-3 sm:w-64">
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-bold text-slate-700">Kelengkapan Profil</span>
                        <span class="font-extrabold text-[#007AFF] tabular-nums">{{ $kelengkapan }}%</span>
                    </div>
                    <div class="mt-1.5 h-2 w-full overflow-hidden rounded-full bg-[#E0F2FE]">
                        <div class="h-full rounded-full bg-gradient-to-r from-[#007AFF] to-sky-400" style="width: {{ $kelengkapan }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Document Quick Badges -->
            <div class="mt-4 flex flex-wrap gap-2">
                <span @class(['rounded-full px-3 py-1 text-[11px] font-bold', 'bg-emerald-100 text-emerald-700' => !empty($profil['cv_default_path']), 'bg-slate-100 text-slate-500' => empty($profil['cv_default_path'])])>
                    CV: {{ !empty($profil['cv_default_path']) ? 'Tersedia' : 'Belum diunggah' }}
                </span>
                <span @class(['rounded-full px-3 py-1 text-[11px] font-bold', 'bg-emerald-100 text-emerald-700' => !empty($profil['surat_lamaran_default_path']), 'bg-slate-100 text-slate-500' => empty($profil['surat_lamaran_default_path'])])>
                    Surat Lamaran: {{ !empty($profil['surat_lamaran_default_path']) ? 'Tersedia' : 'Belum diunggah' }}
                </span>
                <span @class(['rounded-full px-3 py-1 text-[11px] font-bold', 'bg-emerald-100 text-emerald-700' => !empty($profil['ijazah_default_path']), 'bg-slate-100 text-slate-500' => empty($profil['ijazah_default_path'])])>
                    Ijazah: {{ !empty($profil['ijazah_default_path']) ? 'Tersedia' : 'Belum diunggah' }}
                </span>
            </div>
        </div>
    </section>

    <!-- 2-Column Bento Settings -->
    <div class="grid grid-cols-1 items-start gap-5 lg:grid-cols-12">
        <!-- Left Navigation Pills -->
        <nav class="flex gap-1.5 overflow-x-auto rounded-3xl border-[1.5px] border-slate-200/90 bg-white/85 p-2 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.08)] backdrop-blur-xl lg:sticky lg:top-24 lg:col-span-3 lg:flex-col lg:p-3" aria-label="Bagian pengaturan akun" data-navigasi-akun>
            <p class="hidden px-3 py-1.5 text-[11px] font-extrabold tracking-wider text-slate-400 uppercase lg:block">Pengaturan</p>
            <a href="#profil-akun" @if ($bagianAwal === 'profil-akun') aria-current="location" @endif class="flex shrink-0 items-center gap-2.5 rounded-2xl px-3.5 py-2.5 text-xs font-bold text-slate-600 transition hover:bg-[#E0F2FE] hover:text-[#007AFF] aria-[current=location]:bg-[#007AFF] aria-[current=location]:!text-white aria-[current=location]:shadow-sm aria-[current=location]:shadow-[#007AFF]/25">
                <svg class="size-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><circle cx="12" cy="8" r="3.25"/><path d="M5 20c.7-3.2 3.1-5 7-5s6.3 1.8 7 5"/></svg>
                <span>Profil Pribadi</span>
            </a>
            <a href="#email-akun" @if ($bagianValidasi === 'email-akun') aria-current="location" @endif class="flex shrink-0 items-center gap-2.5 rounded-2xl px-3.5 py-2.5 text-xs font-bold text-slate-600 transition hover:bg-[#E0F2FE] hover:text-[#007AFF] aria-[current=location]:bg-[#007AFF] aria-[current=location]:!text-white aria-[current=location]:shadow-sm aria-[current=location]:shadow-[#007AFF]/25">
                <svg class="size-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m4 7 8 6 8-6"/></svg>
                <span>Email Akun</span>
            </a>
            <a href="#keamanan-akun" @if ($bagianValidasi === 'keamanan-akun') aria-current="location" @endif class="flex shrink-0 items-center gap-2.5 rounded-2xl px-3.5 py-2.5 text-xs font-bold text-slate-600 transition hover:bg-[#E0F2FE] hover:text-[#007AFF] aria-[current=location]:bg-[#007AFF] aria-[current=location]:!text-white aria-[current=location]:shadow-sm aria-[current=location]:shadow-[#007AFF]/25">
                <svg class="size-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><rect x="5" y="10" width="14" height="11" rx="2"/><path d="M8 10V7a4 4 0 1 1 8 0v3M12 14v3"/></svg>
                <span>Kata Sandi</span>
            </a>
            <a href="#keluar-akun" class="flex shrink-0 items-center gap-2.5 rounded-2xl px-3.5 py-2.5 text-xs font-bold text-rose-600 transition hover:bg-rose-50 aria-[current=location]:bg-rose-600 aria-[current=location]:!text-white">
                <svg class="size-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M15 4h4v16h-4M10 8l-4 4 4 4M6 12h9"/></svg>
                <span>Keluar</span>
            </a>
        </nav>

        <!-- Right Content Panels -->
        <div class="min-w-0 space-y-5 lg:col-span-9">
            <section id="profil-akun" data-panel-akun @if ($bagianAwal !== 'profil-akun') hidden @endif>
                <form method="POST" action="{{ route('akun.profil.update') }}" enctype="multipart/form-data" class="space-y-5" data-kirim>
                    @csrf
                    @method('PATCH')

                    <!-- Card 1: Foto & Data Diri -->
                    <div class="rounded-3xl border-[1.5px] border-slate-200/90 bg-white/85 p-5 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.08)] backdrop-blur-xl sm:p-6">
                        <div class="mb-4 flex items-center gap-2.5 border-b border-slate-200/70 pb-3">
                            <span class="grid size-8 place-items-center rounded-xl bg-[#E0F2FE] text-[#007AFF]">
                                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="3.4"/><path d="M5 20c.8-3.4 3.4-5.2 7-5.2s6.2 1.8 7 5.2"/></svg>
                            </span>
                            <div>
                                <h2 class="text-sm font-bold text-slate-900">Foto &amp; Informasi Pribadi</h2>
                                <p class="text-[11px] text-slate-500">Data dasar untuk biodata pelamar otomatis</p>
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <span class="mb-1.5 block text-xs font-bold text-slate-700">Foto Profil (JPG, PNG, WEBP, maks 2 MB)</span>
                                <div class="flex flex-wrap items-center gap-4 rounded-2xl border border-dashed border-slate-300 bg-slate-50/70 p-3.5">
                                    @if ($pengguna->foto_profil_path)
                                        <img src="{{ asset('storage/' . $pengguna->foto_profil_path) }}" alt="{{ $pengguna->name }}" class="size-14 rounded-full border border-slate-200 object-cover">
                                    @else
                                        <span class="grid size-14 place-items-center rounded-full bg-[#E0F2FE] text-lg font-extrabold text-[#007AFF]">{{ mb_strtoupper(mb_substr($pengguna->name, 0, 1)) }}</span>
                                    @endif
                                    <div class="flex flex-wrap items-center gap-3">
                                        <input id="foto_profil_user" name="foto_profil" type="file" accept=".jpg,.jpeg,.png,.webp" class="sr-only" onchange="document.getElementById('nama-file-foto-user').textContent = this.files[0]?.name || 'Belum ada file dipilih';">
                                        <label for="foto_profil_user" class="inline-flex cursor-pointer items-center gap-2 rounded-full bg-[#007AFF] px-4 py-2 text-xs font-bold text-white shadow-sm shadow-[#007AFF]/20 transition hover:bg-[#0062CC]">
                                            <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M17 8l-5-5-5 5M12 3v12"/></svg>
                                            Upload Foto
                                        </label>
                                        <span id="nama-file-foto-user" class="text-xs font-medium text-slate-500">Belum ada file dipilih</span>
                                    </div>
                                </div>
                                @error('foto_profil')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label class="mb-1.5 block text-xs font-bold text-slate-700" for="name">Nama Lengkap</label>
                                <input id="name" name="name" value="{{ old('name', $pengguna->name) }}" maxlength="255" autocomplete="name" required>
                                @error('name')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-bold text-slate-700" for="telepon">Nomor Telepon</label>
                                <input id="telepon" name="telepon" type="tel" value="{{ old('telepon', $profil['telepon'] ?? '') }}" maxlength="25" placeholder="081234567890">
                                @error('telepon')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-bold text-slate-700" for="tempat_lahir">Tempat Lahir</label>
                                <input id="tempat_lahir" name="tempat_lahir" value="{{ old('tempat_lahir', $profil['tempat_lahir'] ?? '') }}" maxlength="100">
                                @error('tempat_lahir')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-bold text-slate-700" for="tanggal_lahir">Tanggal Lahir</label>
                                <input id="tanggal_lahir" name="tanggal_lahir" type="date" value="{{ old('tanggal_lahir', $profil['tanggal_lahir'] ?? '') }}">
                                @error('tanggal_lahir')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-bold text-slate-700" for="jenis_kelamin">Jenis Kelamin</label>
                                <select id="jenis_kelamin" name="jenis_kelamin">
                                    <option value="">Pilih jenis kelamin</option>
                                    @foreach (['Laki-laki', 'Perempuan'] as $jk)
                                        <option value="{{ $jk }}" @selected(old('jenis_kelamin', $profil['jenis_kelamin'] ?? '') === $jk)>{{ $jk }}</option>
                                    @endforeach
                                </select>
                                @error('jenis_kelamin')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-bold text-slate-700" for="domisili">Domisili / Kota</label>
                                <input id="domisili" name="domisili" value="{{ old('domisili', $profil['domisili'] ?? '') }}" maxlength="255">
                                @error('domisili')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <div class="sm:col-span-2">
                                <label class="mb-1.5 block text-xs font-bold text-slate-700" for="alamat_lengkap">Alamat Lengkap</label>
                                <input id="alamat_lengkap" name="alamat_lengkap" value="{{ old('alamat_lengkap', $profil['alamat_lengkap'] ?? '') }}" maxlength="500" placeholder="Jalan, RT/RW, Kecamatan">
                                @error('alamat_lengkap')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>

                    <!-- Card 2: Pendidikan, Keahlian & Pengalaman -->
                    <div class="rounded-3xl border-[1.5px] border-slate-200/90 bg-white/85 p-5 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.08)] backdrop-blur-xl sm:p-6">
                        <div class="mb-4 flex items-center gap-2.5 border-b border-slate-200/70 pb-3">
                            <span class="grid size-8 place-items-center rounded-xl bg-[#E0F2FE] text-[#007AFF]">
                                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                            </span>
                            <div>
                                <h2 class="text-sm font-bold text-slate-900">Pendidikan, Keahlian &amp; Pengalaman</h2>
                                <p class="text-[11px] text-slate-500">Digunakan untuk mencocokkan rekomendasi loker terbaik</p>
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="mb-1.5 block text-xs font-bold text-slate-700" for="pendidikan_terakhir">Pendidikan Terakhir</label>
                                <select id="pendidikan_terakhir" name="pendidikan_terakhir">
                                    <option value="">Pilih pendidikan</option>
                                    @foreach (['SD', 'SMP', 'SMA/SMK', 'Diploma', 'S1', 'S2', 'S3'] as $pend)
                                        <option value="{{ $pend }}" @selected(old('pendidikan_terakhir', $profil['pendidikan_terakhir'] ?? '') === $pend)>{{ $pend }}</option>
                                    @endforeach
                                </select>
                                @error('pendidikan_terakhir')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-bold text-slate-700" for="institusi_pendidikan">Nama Sekolah / Universitas</label>
                                <input id="institusi_pendidikan" name="institusi_pendidikan" value="{{ old('institusi_pendidikan', $profil['institusi_pendidikan'] ?? '') }}" maxlength="255" placeholder="Contoh: Universitas Indonesia">
                                @error('institusi_pendidikan')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-bold text-slate-700" for="jurusan">Jurusan / Program Studi</label>
                                <input id="jurusan" name="jurusan" value="{{ old('jurusan', $profil['jurusan'] ?? '') }}" maxlength="150" placeholder="Contoh: Teknik Informatika">
                                @error('jurusan')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-bold text-slate-700" for="tahun_lulus">Tahun Lulus</label>
                                <input id="tahun_lulus" name="tahun_lulus" type="number" min="1960" max="{{ (int) date('Y') + 5 }}" value="{{ old('tahun_lulus', $profil['tahun_lulus'] ?? '') }}" placeholder="Contoh: 2025">
                                @error('tahun_lulus')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-bold text-slate-700" for="ekspektasi_gaji">Ekspektasi Gaji</label>
                                <input id="ekspektasi_gaji" name="ekspektasi_gaji" value="{{ old('ekspektasi_gaji', $profil['ekspektasi_gaji'] ?? '') }}" maxlength="100" placeholder="Contoh: Rp 6.000.000 - Rp 8.000.000">
                                @error('ekspektasi_gaji')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-bold text-slate-700" for="portofolio_url">Portofolio / LinkedIn / GitHub</label>
                                <input id="portofolio_url" name="portofolio_url" type="url" value="{{ old('portofolio_url', $profil['portofolio_url'] ?? '') }}" placeholder="https://...">
                                @error('portofolio_url')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <div class="sm:col-span-2">
                                <label class="mb-1.5 block text-xs font-bold text-slate-700" for="keahlian">Keahlian (pisahkan dengan koma)</label>
                                <input id="keahlian" name="keahlian" value="{{ old('keahlian', $profil['keahlian'] ?? '') }}" placeholder="Contoh: Laravel, PHP, MySQL, Excel">
                                @error('keahlian')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <div class="sm:col-span-2">
                                <label class="mb-1.5 block text-xs font-bold text-slate-700" for="ringkasan_pribadi">Tentang Saya / Ringkasan Diri</label>
                                <textarea id="ringkasan_pribadi" name="ringkasan_pribadi" rows="2" placeholder="Ceritakan singkat latar belakang dan tujuan karier Anda...">{{ old('ringkasan_pribadi', $profil['ringkasan_pribadi'] ?? '') }}</textarea>
                                @error('ringkasan_pribadi')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <div class="sm:col-span-2">
                                <label class="mb-1.5 block text-xs font-bold text-slate-700" for="pengalaman_kerja">Pengalaman Kerja</label>
                                <textarea id="pengalaman_kerja" name="pengalaman_kerja" rows="3" placeholder="Ringkasan pengalaman kerja atau magang...">{{ old('pengalaman_kerja', $profil['pengalaman_kerja'] ?? '') }}</textarea>
                                @error('pengalaman_kerja')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>

                    <!-- Card 3: Berkas Lamaran Utama -->
                    <div class="rounded-3xl border-[1.5px] border-slate-200/90 bg-white/85 p-5 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.08)] backdrop-blur-xl sm:p-6">
                        <div class="mb-4 flex items-center gap-2.5 border-b border-slate-200/70 pb-3">
                            <span class="grid size-8 place-items-center rounded-xl bg-emerald-100 text-emerald-700">
                                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 3h9l3 3v15H6zM9 12h6M9 16h6"/></svg>
                            </span>
                            <div>
                                <h2 class="text-sm font-bold text-slate-900">Berkas Lamaran Utama</h2>
                                <p class="text-[11px] text-slate-500">Jika lengkap, melamar loker cukup 1 klik konfirmasi</p>
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-3">
                            <div class="rounded-2xl border border-slate-200/90 bg-slate-50/70 p-4">
                                <label class="block text-xs font-bold text-slate-800" for="cv_default">CV Utama (PDF)</label>
                                <span class="mt-1 inline-block rounded-full px-2.5 py-0.5 text-[10px] font-bold {{ !empty($profil['cv_default_path']) ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-600' }}">
                                    {{ !empty($profil['cv_default_path']) ? 'Sudah terunggah' : 'Belum ada' }}
                                </span>
                                <input id="cv_default" name="cv_default" type="file" accept=".pdf" class="mt-3 text-xs">
                                @error('cv_default')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <div class="rounded-2xl border border-slate-200/90 bg-slate-50/70 p-4">
                                <label class="block text-xs font-bold text-slate-800" for="surat_lamaran_default">Surat Lamaran (PDF)</label>
                                <span class="mt-1 inline-block rounded-full px-2.5 py-0.5 text-[10px] font-bold {{ !empty($profil['surat_lamaran_default_path']) ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-600' }}">
                                    {{ !empty($profil['surat_lamaran_default_path']) ? 'Sudah terunggah' : 'Belum ada' }}
                                </span>
                                <input id="surat_lamaran_default" name="surat_lamaran_default" type="file" accept=".pdf" class="mt-3 text-xs">
                                @error('surat_lamaran_default')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <div class="rounded-2xl border border-slate-200/90 bg-slate-50/70 p-4">
                                <label class="block text-xs font-bold text-slate-800" for="ijazah_default">Ijazah (PDF/JPG/PNG)</label>
                                <span class="mt-1 inline-block rounded-full px-2.5 py-0.5 text-[10px] font-bold {{ !empty($profil['ijazah_default_path']) ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-600' }}">
                                    {{ !empty($profil['ijazah_default_path']) ? 'Sudah terunggah' : 'Belum ada' }}
                                </span>
                                <input id="ijazah_default" name="ijazah_default" type="file" accept=".pdf,.jpg,.jpeg,.png" class="mt-3 text-xs">
                                @error('ijazah_default')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div class="mt-5 flex justify-end border-t border-slate-200/70 pt-4">
                            <button class="inline-flex min-h-11 items-center justify-center rounded-full bg-[#007AFF] px-7 text-sm font-bold text-white shadow-md shadow-[#007AFF]/25 transition hover:bg-[#0062CC]" type="submit" data-teks-proses="Menyimpan…">Simpan Profil</button>
                        </div>
                    </div>
                </form>
            </section>

            <section id="email-akun" data-panel-akun @if ($bagianAwal !== 'email-akun') hidden @endif class="rounded-3xl border-[1.5px] border-slate-200/90 bg-white/85 p-5 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.08)] backdrop-blur-xl sm:p-6">
                <h2 class="text-base font-bold text-slate-900">Email Akun</h2>
                <p class="mt-0.5 text-xs text-slate-500">Gunakan email yang dapat Anda akses untuk masuk ke akun.</p>
                <form method="POST" action="{{ route('akun.email.update') }}" class="mt-5 grid max-w-lg gap-4" data-kirim>
                    @csrf
                    @method('PATCH')
                    <div>
                        <label class="mb-1.5 block text-xs font-bold text-slate-700" for="email">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email', $pengguna->email) }}" maxlength="255" autocomplete="username" required>
                        @error('email')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-bold text-slate-700" for="current_password_email">Kata sandi saat ini</label>
                        <input id="current_password_email" name="current_password_email" type="password" autocomplete="current-password" maxlength="72" required>
                        @error('current_password_email')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <button class="inline-flex min-h-11 items-center justify-center rounded-full bg-[#007AFF] px-6 text-sm font-bold text-white shadow-sm transition hover:bg-[#0062CC]" type="submit" data-teks-proses="Menyimpan…">Simpan Email</button>
                    </div>
                </form>
            </section>

            <section id="keamanan-akun" data-panel-akun @if ($bagianAwal !== 'keamanan-akun') hidden @endif class="rounded-3xl border-[1.5px] border-slate-200/90 bg-white/85 p-5 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.08)] backdrop-blur-xl sm:p-6">
                <h2 class="text-base font-bold text-slate-900">Kata Sandi</h2>
                <p class="mt-0.5 text-xs text-slate-500">Gunakan kata sandi baru minimal 12 karakter.</p>
                <form method="POST" action="{{ route('akun.kata_sandi.update') }}" class="mt-5 grid max-w-lg gap-4" data-kirim>
                    @csrf
                    @method('PATCH')
                    <div>
                        <label class="mb-1.5 block text-xs font-bold text-slate-700" for="current_password_password">Kata sandi saat ini</label>
                        <input id="current_password_password" name="current_password_password" type="password" autocomplete="current-password" maxlength="72" required>
                        @error('current_password_password')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-bold text-slate-700" for="password">Kata sandi baru</label>
                        <input id="password" name="password" type="password" autocomplete="new-password" minlength="12" maxlength="72" required>
                        @error('password')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-bold text-slate-700" for="password_confirmation">Ulangi kata sandi baru</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" minlength="12" maxlength="72" required>
                    </div>
                    <div>
                        <button class="inline-flex min-h-11 items-center justify-center rounded-full bg-[#007AFF] px-6 text-sm font-bold text-white shadow-sm transition hover:bg-[#0062CC]" type="submit" data-teks-proses="Menyimpan…">Ubah Kata Sandi</button>
                    </div>
                </form>
            </section>

            <section id="keluar-akun" data-panel-akun @if ($bagianAwal !== 'keluar-akun') hidden @endif class="flex flex-wrap items-center justify-between gap-4 rounded-3xl border-[1.5px] border-slate-200/90 bg-white/85 p-5 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.08)] backdrop-blur-xl sm:p-6">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Keluar dari Akun</h2>
                    <p class="mt-0.5 text-xs text-slate-500">Akhiri sesi akun ini pada perangkat yang sedang digunakan.</p>
                </div>
                <form method="POST" action="{{ route('logout') }}" data-kirim>
                    @csrf
                    <button class="rounded-full bg-rose-600 px-6 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-rose-700" type="submit" data-teks-proses="Keluar…">Keluar</button>
                </form>
            </section>
        </div>
    </div>
</x-panel-user>
