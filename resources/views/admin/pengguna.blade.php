<x-panel-admin judul="Kelola Pengguna" aktif="pengguna">
    @if ($errors->any())
        <div class="rounded-3xl border-[1.5px] border-rose-300 bg-rose-50/90 px-5 py-4 text-sm font-medium text-rose-800 backdrop-blur-md" role="alert">{{ $errors->first() }}</div>
    @endif

    <form class="flex flex-col gap-3 rounded-3xl border-[1.5px] border-slate-200/90 bg-white/80 p-4 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.1)] backdrop-blur-xl sm:flex-row sm:items-center" method="GET">
        <div class="flex-1">
            <label class="sr-only" for="cari-pengguna">Cari nama atau email</label>
            <input id="cari-pengguna" type="search" name="kata_kunci" value="{{ $cari }}" class="w-full rounded-full border border-slate-200/90 bg-white/95 px-5 py-2.5 text-sm text-slate-800 placeholder:text-slate-400 focus:border-[#007AFF] focus:outline-none" placeholder="Cari nama atau email pengguna…">
        </div>
        <div class="sm:w-48">
            <label class="sr-only" for="filter-role">Filter peran</label>
            <select id="filter-role" name="role" class="w-full rounded-full border border-slate-200/90 bg-white/95 px-4 py-2.5 text-sm text-slate-800 focus:border-[#007AFF] focus:outline-none">
                <option value="">Semua Peran</option>
                @foreach ($jenisAkunTersedia as $kunci => $label)
                    <option value="{{ $kunci }}" @selected($peranAktif === $kunci)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <button class="inline-flex min-h-11 shrink-0 items-center justify-center rounded-full bg-[#007AFF] px-6 text-sm font-semibold text-white shadow-md shadow-[#007AFF]/20 transition hover:bg-[#0062CC]" type="submit">Cari</button>
    </form>

    <div class="overflow-hidden rounded-3xl border-[1.5px] border-slate-200/90 bg-white/80 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.1)] backdrop-blur-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-slate-200/60 bg-white/60 text-xs font-bold tracking-wider text-slate-500 uppercase">
                    <tr>
                        <th class="px-6 py-4">Pengguna</th>
                        <th class="px-6 py-4">Peran</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Terdaftar</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200/50">
                    @forelse ($daftarPengguna as $item)
                        <tr class="transition hover:bg-white/70">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <span class="grid size-9 shrink-0 place-items-center rounded-2xl bg-[#E0F2FE] text-xs font-bold text-[#007AFF]">{{ mb_strtoupper(mb_substr($item->name, 0, 1)) }}</span>
                                    <div>
                                        <strong class="block font-semibold text-slate-900">{{ $item->name }}</strong>
                                        <span class="text-xs text-slate-500">{{ $item->email }}</span>
                                        @if ($item->is_banned && $item->alasan_ban)
                                            <span class="mt-0.5 block text-[11px] font-medium text-rose-600">Alasan: {{ $item->alasan_ban }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="rounded-full bg-[#E0F2FE] px-3 py-1 text-xs font-semibold text-[#007AFF]">{{ $jenisAkunTersedia[$item->role] ?? $item->role }}</span>
                            </td>
                            <td class="px-6 py-4 text-xs font-medium">
                                @if ($item->is_banned)
                                    <span class="rounded-full bg-rose-100 px-3 py-1 font-bold text-rose-700">Dibanned</span>
                                @elseif ($item->role === 'perusahaan')
                                    <span class="text-slate-600">{{ $item->status_verifikasi }}</span>
                                @else
                                    <span class="rounded-full bg-emerald-100 px-3 py-1 font-semibold text-emerald-700">Aktif</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-500">{{ $item->created_at?->format('d M Y') ?? '-' }}</td>
                            <td class="px-6 py-4 text-right">
                                @if ($item->role !== 'admin')
                                    @if ($item->is_banned)
                                        <form method="POST" action="{{ route('admin.pengguna.ban', $item) }}" class="inline" data-kirim>
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="rounded-full bg-emerald-50 px-3.5 py-1.5 text-xs font-bold text-emerald-700 transition hover:bg-emerald-100">Buka Ban</button>
                                        </form>
                                    @else
                                        <button type="button" class="rounded-full bg-rose-50 px-3.5 py-1.5 text-xs font-bold text-rose-600 transition hover:bg-rose-100" onclick="const m = document.getElementById('modal-ban-pengguna'); document.getElementById('form-ban-pengguna').action = '{{ route('admin.pengguna.ban', $item) }}'; document.getElementById('nama-ban-pengguna').textContent = @js($item->name); m.classList.remove('hidden'); m.classList.add('flex');">Banned</button>
                                    @endif
                                @else
                                    <span class="text-xs text-slate-400">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-slate-500">Tidak ada pengguna ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-200/60 px-6 py-3.5">{{ $daftarPengguna->links() }}</div>
    </div>

    <!-- Modal Konfirmasi Banned Pengguna -->
    <div id="modal-ban-pengguna" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/45 p-4 backdrop-blur-sm" role="dialog" aria-modal="true" aria-labelledby="judul-ban-pengguna">
        <div class="w-full max-w-md rounded-3xl border border-slate-200 bg-white p-6 shadow-2xl">
            <h2 id="judul-ban-pengguna" class="text-base font-extrabold text-slate-900">Banned Akun Pengguna</h2>
            <p class="mt-1 text-xs text-slate-500">Akun <strong id="nama-ban-pengguna" class="text-slate-800"></strong> tidak akan bisa masuk ke sistem sampai ban dibuka.</p>
            <form id="form-ban-pengguna" method="POST" action="" class="mt-4 space-y-4" data-kirim>
                @csrf
                @method('PATCH')
                <div>
                    <label for="alasan-ban" class="mb-1.5 block text-xs font-bold text-slate-700">Alasan Banned</label>
                    <textarea id="alasan-ban" name="alasan_ban" rows="3" maxlength="500" class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm text-slate-800 focus:border-[#007AFF] focus:outline-none" placeholder="Contoh: Terbukti melakukan penipuan / spam lamaran…"></textarea>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-50" onclick="const m = document.getElementById('modal-ban-pengguna'); m.classList.add('hidden'); m.classList.remove('flex');">Batal</button>
                    <button type="submit" class="rounded-full bg-rose-600 px-5 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-rose-700" data-teks-proses="Memproses…">Konfirmasi Banned</button>
                </div>
            </form>
        </div>
    </div>
</x-panel-admin>
