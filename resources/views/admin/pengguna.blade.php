<x-panel-admin judul="Kelola Pengguna" aktif="pengguna">
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
                        <th class="px-6 py-4">Verifikasi</th>
                        <th class="px-6 py-4">Terdaftar</th>
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
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="rounded-full bg-[#E0F2FE] px-3 py-1 text-xs font-semibold text-[#007AFF]">{{ $jenisAkunTersedia[$item->role] ?? $item->role }}</span>
                            </td>
                            <td class="px-6 py-4 text-xs font-medium text-slate-600">
                                {{ $item->role === 'perusahaan' ? $item->status_verifikasi : 'Aktif' }}
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-500">{{ $item->created_at?->format('d M Y') ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-slate-500">Tidak ada pengguna ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-200/60 px-6 py-3.5">{{ $daftarPengguna->links() }}</div>
    </div>
</x-panel-admin>
