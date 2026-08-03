@props([
    'title' => 'Belum ada data',
    'description' => 'Data akan ditampilkan di sini setelah tersedia.',
])

<div class="rounded-xl border border-dashed border-border bg-card px-5 py-10 text-center sm:px-8 sm:py-12">
    <div class="mx-auto flex size-14 items-center justify-center rounded-xl bg-slate-100 text-secondary">
        <svg class="size-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"
            aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M20 13V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v7m16 0-3-3m3 3-3 3M4 13l3-3m-3 3 3 3m2 4h6" />
        </svg>
    </div>
    <h2 class="mt-5 text-lg font-semibold text-navy">{{ $title }}</h2>
    <p class="mx-auto mt-2 max-w-xl text-sm leading-6 text-secondary">{{ $description }}</p>

    @unless ($slot->isEmpty())
        <div class="mt-5">{{ $slot }}</div>
    @endunless
</div>
