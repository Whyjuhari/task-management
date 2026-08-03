@props(['title', 'description' => null])

<div class="mb-6 flex flex-col gap-4 sm:mb-8 sm:flex-row sm:items-end sm:justify-between">
    <div class="min-w-0">
        <h2 class="text-2xl font-semibold tracking-tight text-navy sm:text-3xl">{{ $title }}</h2>
        @if ($description)
            <p class="mt-2 max-w-3xl text-sm leading-6 text-secondary sm:text-base">{{ $description }}</p>
        @endif
    </div>

    @unless ($slot->isEmpty())
        <div class="shrink-0">{{ $slot }}</div>
    @endunless
</div>
