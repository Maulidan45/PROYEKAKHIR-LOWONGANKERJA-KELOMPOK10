<x-layout :judul="'Lamar ' . $lowongan->judul . ' | Lokerku'">
    <div class="mx-auto w-[min(64rem,calc(100%-2rem))] py-8 sm:py-12">
        <nav class="mb-5 flex flex-wrap items-center gap-2 text-sm text-slate-500" aria-label="Navigasi halaman">
            <a class="font-medium text-blue-700 hover:underline" href="{{ route('lowongan.show', $lowongan) }}">&larr; Kembali ke detail lowongan</a>
        </nav>

        <header class="mb-6 rounded-2xl border border-blue-100 bg-blue-50/70 p-6">
            <p class="text-xs font-bold tracking-wider text-blue-700 uppercase">Formulir Pengajuan Lamaran</p>
            <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">{{ $lowongan->judul }}</h1>
            <p class="mt-1 text-sm text-slate-600">{{ $lowongan->perusahaan->name }} &middot; {{ $lowongan->lokasi }} &middot; {{ $lowongan->tipe_kerja }}</p>
        </header>

        @if ($errors->any())
            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-900" role="alert">
                <h2 class="font-semibold">Mohon periksa kembali isian lamaran Anda:</h2>
                <ul class="mt-2 list-disc pl-5">
                    @foreach ($errors->all() as $galat)
                        <li>{{ $galat }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="form-pengajuan-lamaran" method="POST" action="{{ route('lamaran.store', $lowongan) }}" enctype="multipart/form-data" data-kirim>
            @csrf

            <!-- LANGKAH 1: ISI DATA & DOKUMEN -->
            <div id="langkah-isi-lamaran" class="space-y-6">
                <!-- Bagian A: Data Pribadi Pelamar -->
                <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-xs">
                    <div class="mb-5 border-b border-slate-100 pb-4">
                        <span class="rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-bold text-blue-700">Bagian A</span>
                        <h2 class="mt-2 text-lg font-bold text-slate-900">Data Pribadi Pelamar</h2>
                        <p class="text-sm text-slate-500">Data diambil otomatis dari profil Anda. Periksa dan perbarui sebelum mengirim lamaran.</p>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="isian">
                            <label for="nama_lengkap">Nama lengkap <span class="text-red-600">*</span></label>
                            <input id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap', $pengguna->name) }}" required data-konfirmasi-label="Nama Lengkap">
                        </div>
                        <div class="isian">
                            <label for="email">Email <span class="text-red-600">*</span></label>
                            <input id="email" name="email" type="email" value="{{ old('email', $pengguna->email) }}" required data-konfirmasi-label="Email">
                        </div>
                        <div class="isian">
                            <label for="telepon">Nomor telepon <span class="text-red-600">*</span></label>
                            <input id="telepon" name="telepon" type="tel" value="{{ old('telepon', $profil['telepon'] ?? '') }}" placeholder="081234567890" required data-konfirmasi-label="Nomor Telepon">
                        </div>
                        <div class="isian">
                            <label for="jenis_kelamin">Jenis kelamin <span class="text-red-600">*</span></label>
                            <select id="jenis_kelamin" name="jenis_kelamin" required data-konfirmasi-label="Jenis Kelamin">
                                <option value="">Pilih jenis kelamin</option>
                                @foreach (['Laki-laki', 'Perempuan'] as $jk)
                                    <option value="{{ $jk }}" @selected(old('jenis_kelamin', $profil['jenis_kelamin'] ?? '') === $jk)>{{ $jk }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="isian">
                            <label for="tempat_lahir">Tempat lahir <span class="text-red-600">*</span></label>
                            <input id="tempat_lahir" name="tempat_lahir" value="{{ old('tempat_lahir', $profil['tempat_lahir'] ?? '') }}" placeholder="Contoh: Jakarta" required data-konfirmasi-label="Tempat Lahir">
                        </div>
                        <div class="isian">
                            <label for="tanggal_lahir">Tanggal lahir <span class="text-red-600">*</span></label>
                            <input id="tanggal_lahir" name="tanggal_lahir" type="date" value="{{ old('tanggal_lahir', $profil['tanggal_lahir'] ?? '') }}" required data-konfirmasi-label="Tanggal Lahir">
                        </div>
                        <div class="isian">
                            <label for="domisili">Alamat atau domisili <span class="text-red-600">*</span></label>
                            <input id="domisili" name="domisili" value="{{ old('domisili', $profil['domisili'] ?? '') }}" placeholder="Kota / Kabupaten tempat tinggal" required data-konfirmasi-label="Domisili">
                        </div>
                        <div class="isian">
                            <label for="pendidikan_terakhir">Pendidikan terakhir <span class="text-red-600">*</span></label>
                            <select id="pendidikan_terakhir" name="pendidikan_terakhir" required data-konfirmasi-label="Pendidikan Terakhir">
                                <option value="">Pilih pendidikan</option>
                                @foreach (['SD', 'SMP', 'SMA/SMK', 'Diploma', 'S1', 'S2', 'S3'] as $pend)
                                    <option value="{{ $pend }}" @selected(old('pendidikan_terakhir', $profil['pendidikan_terakhir'] ?? '') === $pend)>{{ $pend }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </section>

                <!-- Bagian B: Dokumen Persyaratan -->
                <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-xs">
                    <div class="mb-5 border-b border-slate-100 pb-4">
                        <span class="rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-bold text-blue-700">Bagian B</span>
                        <h2 class="mt-2 text-lg font-bold text-slate-900">Dokumen Persyaratan</h2>
                        <p class="text-sm text-slate-500">Unggah dokumen dalam format yang sesuai (maksimal 5 MB per berkas). Berkas disimpan aman dan terlindungi.</p>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        @php
                            $daftarBerkas = [
                                ['id' => 'cv', 'label' => 'CV / Curriculum Vitae', 'wajib' => true, 'accept' => '.pdf,application/pdf', 'ket' => 'Format PDF, maks. 5 MB'],
                                ['id' => 'surat_lamaran', 'label' => 'Surat Lamaran Kerja', 'wajib' => true, 'accept' => '.pdf,application/pdf', 'ket' => 'Format PDF, maks. 5 MB'],
                                ['id' => 'ijazah', 'label' => 'Ijazah Terakhir', 'wajib' => true, 'accept' => '.pdf,.jpg,.jpeg,.png', 'ket' => 'Format PDF/JPG/PNG, maks. 5 MB'],
                                ['id' => 'portofolio', 'label' => 'Portofolio (Opsional)', 'wajib' => false, 'accept' => '.pdf,.jpg,.jpeg,.png', 'ket' => 'Format PDF/JPG/PNG, maks. 5 MB'],
                                ['id' => 'sertifikat', 'label' => 'Sertifikat Pendukung (Opsional)', 'wajib' => false, 'accept' => '.pdf,.jpg,.jpeg,.png', 'ket' => 'Format PDF/JPG/PNG, maks. 5 MB'],
                            ];
                        @endphp
                        @foreach ($daftarBerkas as $berkas)
                            <div class="rounded-xl border border-slate-200 bg-slate-50/70 p-4">
                                <label class="block text-sm font-semibold text-slate-900" for="{{ $berkas['id'] }}">
                                    {{ $berkas['label'] }}
                                    @if ($berkas['wajib']) <span class="text-red-600">*</span> @endif
                                </label>
                                <p class="mt-0.5 text-xs text-slate-500">{{ $berkas['ket'] }}</p>
                                <input class="mt-2 block w-full text-sm text-slate-700 file:mr-3 file:rounded-lg file:border-0 file:bg-blue-600 file:px-3 file:py-2 file:text-xs file:font-semibold file:text-white hover:file:bg-blue-700"
                                       id="{{ $berkas['id'] }}"
                                       name="{{ $berkas['id'] }}"
                                       type="file"
                                       accept="{{ $berkas['accept'] }}"
                                       @if ($berkas['wajib']) required @endif
                                       data-berkas-input
                                       data-berkas-label="{{ $berkas['label'] }}">
                                <p class="mt-2 text-xs font-medium text-blue-700" data-berkas-info="{{ $berkas['id'] }}">Belum ada berkas dipilih.</p>
                            </div>
                        @endforeach
                    </div>
                </section>

                <!-- Bagian C: Informasi Tambahan -->
                <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-xs">
                    <div class="mb-5 border-b border-slate-100 pb-4">
                        <span class="rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-bold text-blue-700">Bagian C</span>
                        <h2 class="mt-2 text-lg font-bold text-slate-900">Informasi Tambahan</h2>
                        <p class="text-sm text-slate-500">Jelaskan pengalaman kerja, keahlian utama, serta pesan Anda untuk perusahaan.</p>
                    </div>

                    <div class="grid gap-4">
                        <div class="isian">
                            <label for="pengalaman_kerja">Pengalaman kerja <span class="text-red-600">*</span></label>
                            <textarea id="pengalaman_kerja" name="pengalaman_kerja" rows="3" placeholder="Contoh: 2 tahun sebagai Staf Administrasi di PT Maju Jaya (atau Fresh Graduate - Pengalaman Magang)" required data-konfirmasi-label="Pengalaman Kerja">{{ old('pengalaman_kerja', $profil['pengalaman_kerja'] ?? '') }}</textarea>
                        </div>
                        <div class="isian">
                            <label for="keahlian">Keahlian yang dimiliki <span class="text-red-600">*</span></label>
                            <input id="keahlian" name="keahlian" value="{{ old('keahlian', $profil['keahlian'] ?? '') }}" placeholder="Contoh: Microsoft Excel, Administrasi Perkantoran, Komunikasi" required data-konfirmasi-label="Keahlian">
                        </div>
                        <div class="isian">
                            <label for="catatan">Pesan atau catatan untuk perusahaan (opsional)</label>
                            <textarea id="catatan" name="catatan" rows="3" placeholder="Tuliskan kesiapan bergabung atau informasi pendukung lainnya..." data-konfirmasi-label="Pesan untuk Perusahaan">{{ old('catatan') }}</textarea>
                        </div>
                    </div>
                </section>

                <div class="flex flex-wrap items-center justify-end gap-3">
                    <a class="tombol-kedua" href="{{ route('lowongan.show', $lowongan) }}">Batal</a>
                    <button type="button" id="btn-lanjut-konfirmasi" class="tombol-utama">Lanjut ke Konfirmasi Pengajuan &rarr;</button>
                </div>
            </div>

            <!-- LANGKAH 2: BAGIAN D KONFIRMASI PENGAJUAN -->
            <div id="langkah-konfirmasi-lamaran" class="space-y-6" hidden>
                <section class="rounded-2xl border border-blue-200 bg-white p-6 shadow-xs">
                    <div class="mb-5 border-b border-slate-100 pb-4">
                        <span class="rounded-full bg-blue-600 px-2.5 py-0.5 text-xs font-bold text-white">Bagian D · Konfirmasi</span>
                        <h2 class="mt-2 text-xl font-bold text-slate-900">Ringkasan Pengajuan Lamaran</h2>
                        <p class="text-sm text-slate-600">Pastikan seluruh data pribadi dan dokumen sudah benar sebelum mengirim lamaran.</p>
                    </div>

                    <div class="mb-6 rounded-xl border border-blue-100 bg-blue-50/60 p-4">
                        <p class="text-xs font-semibold text-blue-700 uppercase">Lowongan yang Dilamar</p>
                        <p class="mt-1 text-base font-bold text-slate-900">{{ $lowongan->judul }}</p>
                        <p class="text-sm text-slate-600">{{ $lowongan->perusahaan->name }} &middot; {{ $lowongan->lokasi }}</p>
                    </div>

                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <h3 class="mb-3 text-sm font-bold text-slate-900 uppercase">Informasi Pribadi</h3>
                            <dl id="ringkasan-data-pelamar" class="space-y-2 text-sm"></dl>
                        </div>
                        <div>
                            <h3 class="mb-3 text-sm font-bold text-slate-900 uppercase">Dokumen yang Diunggah</h3>
                            <ul id="ringkasan-dokumen-pelamar" class="space-y-2 text-sm"></ul>
                        </div>
                    </div>
                </section>

                <div class="flex flex-wrap items-center justify-between gap-3">
                    <button type="button" id="btn-kembali-edit" class="tombol-kedua">&larr; Kembali Edit</button>
                    <button type="submit" class="tombol-utama" data-teks-proses="Mengirim Lamaran…">Kirim Lamaran</button>
                </div>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('form-pengajuan-lamaran');
            const langkahIsi = document.getElementById('langkah-isi-lamaran');
            const langkahKonfirmasi = document.getElementById('langkah-konfirmasi-lamaran');
            const btnLanjut = document.getElementById('btn-lanjut-konfirmasi');
            const btnKembali = document.getElementById('btn-kembali-edit');

            document.querySelectorAll('[data-berkas-input]').forEach((input) => {
                input.addEventListener('change', () => {
                    const info = document.querySelector(`[data-berkas-info="${input.id}"]`);
                    if (!info) return;
                    if (input.files && input.files.length > 0) {
                        const file = input.files[0];
                        const ukuranMb = (file.size / (1024 * 1024)).toFixed(2);
                        info.textContent = `${file.name} (${ukuranMb} MB)`;
                        info.className = 'mt-2 text-xs font-semibold text-emerald-700';
                    } else {
                        info.textContent = 'Belum ada berkas dipilih.';
                        info.className = 'mt-2 text-xs font-medium text-blue-700';
                    }
                });
            });

            btnLanjut?.addEventListener('click', () => {
                const bidang = [...langkahIsi.querySelectorAll('input, select, textarea')];
                for (const el of bidang) {
                    if (!el.reportValidity()) {
                        return;
                    }
                }

                const dlData = document.getElementById('ringkasan-data-pelamar');
                dlData.replaceChildren();
                langkahIsi.querySelectorAll('[data-konfirmasi-label]').forEach((el) => {
                    const wrap = document.createElement('div');
                    wrap.className = 'rounded-lg border border-slate-100 bg-slate-50 px-3 py-2';
                    const dt = document.createElement('dt');
                    dt.className = 'text-xs text-slate-500';
                    dt.textContent = el.dataset.konfirmasiLabel;
                    const dd = document.createElement('dd');
                    dd.className = 'mt-0.5 font-medium text-slate-900 break-words';
                    dd.textContent = el.value.trim() || '-';
                    wrap.append(dt, dd);
                    dlData.appendChild(wrap);
                });

                const ulDokumen = document.getElementById('ringkasan-dokumen-pelamar');
                ulDokumen.replaceChildren();
                langkahIsi.querySelectorAll('[data-berkas-input]').forEach((input) => {
                    const li = document.createElement('li');
                    li.className = 'flex items-center justify-between gap-2 rounded-lg border border-slate-100 bg-slate-50 px-3 py-2';
                    const nama = document.createElement('span');
                    nama.className = 'font-medium text-slate-700';
                    nama.textContent = input.dataset.berkasLabel;
                    const status = document.createElement('span');
                    if (input.files && input.files.length > 0) {
                        status.className = 'text-xs font-semibold text-emerald-700 truncate max-w-[14rem]';
                        status.textContent = input.files[0].name;
                    } else {
                        status.className = 'text-xs text-slate-400';
                        status.textContent = 'Tidak diunggah';
                    }
                    li.append(nama, status);
                    ulDokumen.appendChild(li);
                });

                langkahIsi.hidden = true;
                langkahKonfirmasi.hidden = false;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });

            btnKembali?.addEventListener('click', () => {
                langkahKonfirmasi.hidden = true;
                langkahIsi.hidden = false;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        });
    </script>
</x-layout>
