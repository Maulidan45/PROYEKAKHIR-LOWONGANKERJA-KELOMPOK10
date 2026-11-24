@props(['label' => null, 'judul', 'deskripsi' => null])

<header class="mb-8 flex flex-col gap-5 border-b border-slate-200 pb-6 sm:mb-10 sm:flex-row sm:items-end sm:justify-between sm:pb-7">
    <div class="min-w-0">
        @if ($label)
            <p class="mb-2 flex items-center gap-2 text-xs font-bold tracking-[0.14em] text-blue-800 uppercase">
                <span class="size-1.5 shrink-0 rounded-full bg-orange-500" aria-hidden="true"></span>{{ $label }}
            </p>
        @endif
        <h1 class="text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">{{ $judul }}</h1>
        @if ($deskripsi)
            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600 sm:text-base">{{ $deskripsi }}</p>
        @endif
    </div>
    @if (! $slot->isEmpty())
        <div class="flex flex-wrap items-center gap-2">{{ $slot }}</div>
    @endif
</header>