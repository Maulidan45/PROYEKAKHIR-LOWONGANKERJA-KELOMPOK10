<x-layout :judul="'Pelamar | Lokerku'">
    @php($perusahaan = auth()->user()->role === 'perusahaan')
    <div @class(['mx-auto w-[min(72rem,calc(100%-2rem))] py-8 sm:w-[min(72rem,calc(100%-3rem))] sm:py-12' => $perusahaan, 'pembungkus halaman-aplikasi' => ! $perusahaan])>
        @unless ($perusahaan)
            <x-menu-peran :jenis-akun="auth()->user()->role" />
        @endunless
        <section @class(['mx-auto min-w-0 max-w-5xl' => $perusahaan, 'isi-aplikasi' => ! $perusahaan])>
            @if ($perusahaan)
                <x-kepala-halaman label="Rekrutmen" judul="Pelamar · {{ $lowongan->judul }}" deskripsi="Tinjau kandidat dan perbarui status lamarannya.">
                    <a class="tombol-kedua" href="{{ route('perusahaan.pelamar.index') }}">Semua pelamar</a>
                </x-kepala-halaman>
                @php($jumlahPerStatus = $lowongan->lamarans->countBy('status'))
                <div class="mb-5 flex flex-wrap items-center gap-2" aria-label="Ringkasan status lamaran">
                    <span class="inline-flex min-h-8 items-center rounded-md bg-blue-700 px-3 text-sm font-semibold text-white tabular-nums">{{ $lowongan->lamarans->count() }} lamaran</span>
                    @foreach (App\Models\Lamaran::STATUS as $status)
                        <span class="inline-flex min-h-8 items-center gap-2 rounded-md border border-slate-200 bg-white px-3 text-sm text-slate-600">{{ $status }}<strong class="font-bold text-slate-900 tabular-nums">{{ $jumlahPerStatus->get($status, 0) }}</strong></span>
                    @endforeach
                </div>
                <section class="grid gap-3" aria-label="Daftar kandidat">
                    @forelse ($lowongan->lamarans as $lamaran)
                        <article class="flex flex-wrap items-center gap-4 rounded-lg border border-slate-200 bg-white p-4 sm:flex-nowrap sm:p-5">
                            <span class="grid size-10 shrink-0 place-items-center rounded-full bg-blue-50 text-sm font-bold text-blue-800" aria-hidden="true">{{ mb_strtoupper(mb_substr($lamaran->pelamar->name, 0, 1)) }}</span>
                            <div class="min-w-0 flex-1">
                                <strong class="block truncate text-sm font-semibold text-slate-900">{{ $lamaran->pelamar->name }}</strong>
                                <p class="mt-0.5 truncate text-sm text-slate-600">{{ $lamaran->pelamar->email }}</p>
                                <p class="mt-0.5 text-xs text-slate-500">Melamar {{ $lamaran->created_at->format('d M Y') }}</p>
                            </div>
                            <x-status-lamaran :status="$lamaran->status" />
                            <form class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row sm:items-center" method="POST" action="{{ route('lamaran.update', $lamaran) }}" data-kirim>
                                @csrf
                                @method('PATCH')
                                <label class="sr-only" for="status-{{ $lamaran->id }}">Status lamaran {{ $lamaran->pelamar->name }}</label>
                                <select class="sm:w-40" id="status-{{ $lamaran->id }}" name="status">
                                    @foreach (App\Models\Lamaran::STATUS as $status)
                                        <option @selected($lamaran->status === $status)>{{ $status }}</option>
                                    @endforeach
                                </select>
                                <button class="tombol-kedua" type="submit" data-teks-proses="Menyimpan…">Simpan</button>
                            </form>
                        </article>
                    @empty
                        <div class="rounded-lg border border-dashed border-slate-300 bg-white px-6 py-12 text-center">
                            <h2 class="text-lg font-semibold text-slate-900">Belum ada pelamar pada lowongan ini.</h2>
                            <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-600">Sebarkan lowongan atau periksa batas lamarannya agar kandidat bisa melamar.</p>
                            <a class="tombol-kedua mt-4" href="{{ route('lowongan.edit', $lowongan) }}">Ubah lowongan</a>
                        </div>
                    @endforelse
                </section>
            @else
                <header class="kepala-halaman-aplikasi">
                    <div><p class="eyebrow">{{ $lowongan->perusahaan->name }} · {{ $lowongan->kategori->nama }}</p><h1>Pelamar</h1><p>{{ $lowongan->judul }} · {{ $lowongan->lokasi }}</p></div>
                    <a class="tombol tombol-sekunder" href="{{ route('lowongan.index') }}">Kembali ke lowongan</a>
                </header>
                <section class="grid gap-3" aria-label="Daftar pelamar">
                    @forelse ($lowongan->lamarans as $lamaran)
                        <article class="admin-kandidat-kartu">
                            <span class="admin-kandidat-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr($lamaran->pelamar->name, 0, 1)) }}</span>
                            <div class="admin-kandidat-info">
                                <strong>{{ $lamaran->pelamar->name }}</strong>
                                <p>{{ $lamaran->pelamar->email }}</p>
                                <small>Melamar {{ $lamaran->created_at->format('d M Y') }}</small>
                            </div>
                            <x-status-lamaran :status="$lamaran->status" />
                            <form class="admin-kandidat-form" method="POST" action="{{ route('lamaran.update', $lamaran) }}">
                                @csrf
                                @method('PATCH')
                                <label class="teks-tersembunyi" for="status-{{ $lamaran->id }}">Status lamaran {{ $lamaran->pelamar->name }}</label>
                                <select id="status-{{ $lamaran->id }}" name="status">
                                    @foreach (App\Models\Lamaran::STATUS as $status)
                                        <option @selected($lamaran->status === $status)>{{ $status }}</option>
                                    @endforeach
                                </select>
                                <button class="tombol tombol-sekunder" type="submit">Simpan</button>
                            </form>
                        </article>
                    @empty
                        <div class="empty-lowongan">
                            <h2>Belum ada pelamar pada lowongan ini</h2>
                            <p>Pelamar yang mengirimkan lamaran akan tercatat di sini.</p>
                        </div>
                    @endforelse
                </section>
            @endif
        </section>
    </div>
</x-layout>