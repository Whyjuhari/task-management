@props(['title', 'description' => null])

<div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
    <div class="min-w-0">
        <h2 class="wrap-words text-2xl font-semibold tracking-tight text-navy sm:text-[1.75rem]">{{ $title }}</h2>
        @if ($description)
            <p class="mt-1.5 max-w-3xl text-sm leading-6 text-secondary">{{ $description }}</p>
        @endif
    </div>

    @unless ($slot->isEmpty())
        <div class="w-full shrink-0 [&>a]:w-full sm:w-auto sm:[&>a]:w-auto">{{ $slot }}</div>
    @endunless
</div>
