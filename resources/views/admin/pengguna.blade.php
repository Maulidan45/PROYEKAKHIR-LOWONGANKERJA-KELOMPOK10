<x-panel-admin judul="Kelola Pengguna" label="Manajemen Akun" aktif="pengguna" deskripsi="Daftar seluruh akun pencari kerja, perusahaan, dan administrator yang terdaftar.">
    <form class="mb-5 flex flex-col gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-xs sm:flex-row sm:items-center" method="GET">
        <div class="flex-1">
            <label class="sr-only" for="cari-pengguna">Cari nama atau email</label>
            <input id="cari-pengguna" type="search" name="kata_kunci" value="{{ $cari }}" placeholder="Cari nama atau email pengguna…">
        </div>
        <div class="sm:w-48">
            <label class="sr-only" for="filter-role">Filter peran</label>
            <select id="filter-role" name="role">
                <option value="">Semua Peran</option>
                @foreach ($jenisAkunTersedia as $kunci => $label)
                    <option value="{{ $kunci }}" @selected($peranAktif === $kunci)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <button class="tombol-utama shrink-0" type="submit">Cari</button>
    </form>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-slate-200 bg-slate-50 text-xs font-semibold text-slate-600 uppercase">
                    <tr>
                        <th class="px-5 py-3.5">Pengguna</th>
                        <th class="px-5 py-3.5">Peran</th>
                        <th class="px-5 py-3.5">Verifikasi</th>
                        <th class="px-5 py-3.5">Terdaftar</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($daftarPengguna as $item)
                        <tr class="hover:bg-slate-50">
                            <td class="px-5 py-3.5">
                                <strong class="block font-semibold text-slate-900">{{ $item->name }}</strong>
                                <span class="text-xs text-slate-500">{{ $item->email }}</span>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-semibold text-blue-700">{{ $jenisAkunTersedia[$item->role] ?? $item->role }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-xs text-slate-600">
                                {{ $item->role === 'perusahaan' ? $item->status_verifikasi : 'Aktif' }}
                            </td>
                            <td class="px-5 py-3.5 text-xs text-slate-500">{{ $item->created_at?->format('d M Y') ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-8 text-center text-slate-500">Tidak ada pengguna ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-100 px-5 py-3">{{ $daftarPengguna->links() }}</div>
    </div>
</x-panel-admin>
