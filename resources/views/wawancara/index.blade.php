<x-layout judul="Jadwal Wawancara | Lokerku">
    <div class="mx-auto w-[min(72rem,calc(100%-2rem))] py-8 sm:w-[min(72rem,calc(100%-3rem))] sm:py-12">
        <section class="mx-auto max-w-5xl">
            <x-kepala-halaman label="Rekrutmen" judul="Jadwal Wawancara" :deskripsi="$adalahPerusahaan ? 'Kelola jadwal wawancara bersama kandidat terpilih.' : 'Pantau jadwal wawancara Anda bersama perusahaan.'">
                <a class="tombol-kedua" href="{{ route('dashboard') }}">Kembali ke Dashboard</a>
            </x-kepala-halaman>

            @if ($errors->any())
                <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-900" role="alert">{{ $errors->first() }}</div>
            @endif

            <div class="grid gap-4">
                @forelse ($daftarJadwal as $jadwal)
                    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs sm:p-6">
                        <div class="flex flex-wrap items-start justify-between gap-4">
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-semibold text-blue-700">{{ $jadwal->tipe }}</span>
                                    <span @class([
                                        'rounded-full px-2.5 py-0.5 text-xs font-semibold',
                                        'bg-amber-50 text-amber-800' => in_array($jadwal->status, ['Dijadwalkan', 'Diubah'], true),
                                        'bg-emerald-50 text-emerald-800' => $jadwal->status === 'Selesai',
                                        'bg-red-50 text-red-800' => $jadwal->status === 'Dibatalkan',
                                    ])>{{ $jadwal->status }}</span>
                                </div>
                                <h2 class="mt-2 text-lg font-bold text-slate-900">{{ $jadwal->lamaran->lowongan->judul }}</h2>
                                <p class="text-sm text-slate-600">
                                    @if ($adalahPerusahaan)
                                        Pelamar: <strong>{{ $jadwal->pelamar->name }}</strong>
                                    @else
                                        Perusahaan: <strong>{{ $jadwal->perusahaan->name }}</strong>
                                    @endif
                                </p>
                                <p class="mt-2 text-sm font-semibold text-slate-900">
                                    {{ $jadwal->tanggal->format('d M Y') }} &middot; Pukul {{ $jadwal->jam }} WIB
                                </p>
                                <p class="mt-1 text-sm text-blue-700 break-all">{{ $jadwal->lokasi_atau_tautan }}</p>
                                @if ($jadwal->catatan)
                                    <p class="mt-2 rounded-lg bg-slate-50 px-3 py-2 text-xs text-slate-600">{{ $jadwal->catatan }}</p>
                                @endif
                            </div>
                            <a class="tombol-kedua shrink-0" href="{{ route('lamaran.show', $jadwal->lamaran) }}">Detail Lamaran</a>
                        </div>

                        @if ($adalahPerusahaan)
                            <form class="mt-4 grid gap-3 border-t border-slate-100 pt-4 sm:grid-cols-5 sm:items-end" method="POST" action="{{ route('wawancara.update', $jadwal) }}" data-kirim>
                                @csrf
                                @method('PATCH')
                                <div>
                                    <label class="mb-1 block text-xs font-semibold text-slate-600">Tanggal</label>
                                    <input type="date" name="tanggal" value="{{ $jadwal->tanggal->toDateString() }}" required>
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-semibold text-slate-600">Jam</label>
                                    <input type="time" name="jam" value="{{ $jadwal->jam }}" required>
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-semibold text-slate-600">Tipe</label>
                                    <select name="tipe">
                                        @foreach (App\Models\JadwalWawancara::TIPE as $tp)
                                            <option value="{{ $tp }}" @selected($jadwal->tipe === $tp)>{{ $tp }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-semibold text-slate-600">Status</label>
                                    <select name="status">
                                        @foreach (App\Models\JadwalWawancara::STATUS as $st)
                                            <option value="{{ $st }}" @selected($jadwal->status === $st)>{{ $st }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="sm:col-span-4">
                                    <label class="mb-1 block text-xs font-semibold text-slate-600">Lokasi / Tautan</label>
                                    <input name="lokasi_atau_tautan" value="{{ $jadwal->lokasi_atau_tautan }}" required>
                                </div>
                                <button class="tombol-utama" type="submit" data-teks-proses="Menyimpan…">Perbarui</button>
                            </form>
                        @endif
                    </article>
                @empty
                    <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center">
                        <h2 class="text-lg font-semibold text-slate-900">Belum ada jadwal wawancara</h2>
                        <p class="mt-1 text-sm text-slate-600">{{ $adalahPerusahaan ? 'Anda dapat membuat jadwal wawancara dari halaman detail pelamar.' : 'Jadwal wawancara akan muncul di sini apabila perusahaan mengundang Anda.' }}</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-6">{{ $daftarJadwal->links() }}</div>
        </section>
    </div>
</x-layout>
