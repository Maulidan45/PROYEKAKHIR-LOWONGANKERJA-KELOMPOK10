@php($komponenPanel = auth()->user()->role === 'perusahaan' ? 'panel-perusahaan' : (auth()->user()->role === 'user' ? 'panel-user' : 'layout'))
@php($pakaiPanelModern = $komponenPanel !== 'layout')
<x-dynamic-component :component="$komponenPanel" :judul="$pakaiPanelModern ? 'Notifikasi' : 'Notifikasi | Lokerku'" aktif="notifikasi">
@if ($pakaiPanelModern)
    <div class="flex flex-wrap items-center justify-between gap-3">
        <p class="text-xs font-medium text-slate-500">{{ $jumlahBelumDibaca }} notifikasi belum dibaca.</p>
        @if ($jumlahBelumDibaca > 0)
            <form method="POST" action="{{ route('notifikasi.baca') }}" data-kirim>
                @csrf
                <button class="rounded-full bg-[#007AFF] px-5 py-2 text-xs font-bold text-white shadow-sm shadow-[#007AFF]/25 transition hover:bg-[#0062CC]" type="submit" data-teks-proses="Memproses…">Tandai Semua Dibaca</button>
            </form>
        @endif
    </div>
@else
    <div class="mx-auto w-[min(60rem,calc(100%-2rem))] py-8 sm:py-12">
        <header class="mb-6 flex flex-wrap items-center justify-between gap-4 border-b border-slate-200 pb-6">
            <div>
                <p class="text-xs font-bold tracking-wider text-blue-700 uppercase">Pusat Informasi</p>
                <h1 class="mt-1 text-3xl font-bold tracking-tight text-slate-900">Notifikasi</h1>
                <p class="mt-1 text-sm text-slate-600">{{ $jumlahBelumDibaca }} notifikasi belum dibaca.</p>
            </div>
            @if ($jumlahBelumDibaca > 0)
                <form method="POST" action="{{ route('notifikasi.baca') }}" data-kirim>
                    @csrf
                    <button class="tombol-utama" type="submit" data-teks-proses="Memproses…">Tandai Semua Sudah Dibaca</button>
                </form>
            @endif
        </header>
@endif

        <div class="overflow-hidden rounded-3xl border-[1.5px] border-slate-200/90 bg-white/85 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.08)] backdrop-blur-xl">
            <ul class="divide-y divide-slate-100">
                @forelse ($daftarNotifikasi as $notifikasi)
                    <li @class(['flex flex-wrap items-center justify-between gap-4 p-5 transition', 'bg-blue-50/40' => $notifikasi->read_at === null])>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2">
                                @if ($notifikasi->read_at === null)
                                    <span class="size-2.5 shrink-0 rounded-full bg-blue-600" aria-label="Belum dibaca"></span>
                                @endif
                                <strong class="text-sm font-bold text-slate-900">{{ $notifikasi->data['judul_lowongan'] ?? ($notifikasi->data['status'] ?? 'Pemberitahuan Sistem') }}</strong>
                                <span class="text-xs text-slate-400">{{ $notifikasi->created_at->locale('id')->diffForHumans() }}</span>
                            </div>
                            <p class="mt-1 text-sm text-slate-700">{{ $notifikasi->data['pesan'] ?? ('Status: ' . ($notifikasi->data['status'] ?? '-')) }}</p>
                            @if (! empty($notifikasi->data['catatan']))
                                <p class="mt-1 text-xs text-slate-500">Catatan: {{ $notifikasi->data['catatan'] }}</p>
                            @endif
                        </div>
                        <form method="POST" action="{{ route('notifikasi.baca_satu', $notifikasi->id) }}">
                            @csrf
                            <button class="tombol-kedua" type="submit">{{ ! empty($notifikasi->data['url']) ? 'Buka' : 'Tandai Dibaca' }}</button>
                        </form>
                    </li>
                @empty
                    <li class="p-10 text-center text-sm text-slate-500">Belum ada notifikasi.</li>
                @endforelse
            </ul>
            <div class="border-t border-slate-100 px-5 py-3">{{ $daftarNotifikasi->links() }}</div>
        </div>
@unless ($pakaiPanelModern)
    </div>
@endunless
</x-dynamic-component>
