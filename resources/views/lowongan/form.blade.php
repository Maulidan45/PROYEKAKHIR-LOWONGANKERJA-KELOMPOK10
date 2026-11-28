@php($komponenPanel = auth()->user()->role === 'admin' ? 'panel-admin' : 'panel-perusahaan')
<x-dynamic-component :component="$komponenPanel" :judul="$aksi" aktif="lowongan">
    @if ($komponenPanel === 'panel-admin')
        <x-slot:aksi>
            <a class="order-first mr-auto inline-flex min-h-9 items-center gap-1.5 rounded-full border-[1.5px] border-slate-200/90 bg-white/85 px-4 text-xs font-bold text-slate-700 shadow-xs backdrop-blur-md transition hover:border-[#007AFF]/40 hover:bg-[#E0F2FE] hover:text-[#007AFF] sm:min-h-10" href="{{ route('lowongan.index') }}">
                <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="m15 18-6-6 6-6"/></svg>
                Kembali
            </a>
        </x-slot:aksi>
        @if ($lowongan->exists && $lowongan->perusahaan)
            <p class="px-1 text-xs font-semibold text-slate-500">{{ $lowongan->perusahaan->name }}</p>
        @endif
    @else
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-xl font-extrabold tracking-tight text-slate-900 sm:text-2xl">{{ $aksi }}</h1>
                @if ($lowongan->exists && $lowongan->perusahaan)
                    <p class="mt-0.5 text-xs font-medium text-slate-500">{{ $lowongan->perusahaan->name }}</p>
                @endif
            </div>
            <a class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-700 transition hover:bg-[#E0F2FE] hover:text-[#007AFF]" href="{{ route('lowongan.index') }}">
                <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="m15 18-6-6 6-6"/></svg>
                Kembali
            </a>
        </div>
    @endif

    <form method="POST" action="{{ $lowongan->exists ? route('lowongan.update', $lowongan) : route('lowongan.store') }}" class="grid gap-5 rounded-3xl border-[1.5px] border-slate-200/90 bg-white/85 p-5 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.08)] backdrop-blur-xl sm:grid-cols-2 sm:p-7" data-kirim>
        @csrf
        @if ($lowongan->exists)
            @method('PUT')
        @endif
        @include('lowongan.bagian-form')
        @if ($errors->any())
            <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-900 sm:col-span-2" role="alert">{{ $errors->first() }}</div>
        @endif
        <div class="flex flex-wrap items-center gap-3 sm:col-span-2">
            <button class="inline-flex min-h-11 items-center justify-center rounded-full bg-[#007AFF] px-6 text-sm font-bold text-white shadow-md shadow-[#007AFF]/25 transition hover:bg-[#0062CC]" type="submit" data-teks-proses="Menyimpan…">{{ $aksi }}</button>
            <a class="inline-flex min-h-11 items-center justify-center rounded-full border border-slate-200 bg-white px-5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50" href="{{ route('lowongan.index') }}">Batal</a>
        </div>
    </form>
</x-dynamic-component>
