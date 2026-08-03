@props(['label', 'value', 'tone' => 'default', 'icon' => null, 'description' => null])

@php
    $toneStyles = match ($tone) {
        'primary' => ['value' => 'text-primary', 'icon' => 'bg-primary/10 text-primary'],
        'success' => ['value' => 'text-success-strong', 'icon' => 'bg-success/10 text-success'],
        'warning' => ['value' => 'text-amber-700', 'icon' => 'bg-warning/12 text-amber-600'],
        'danger' => ['value' => 'text-danger', 'icon' => 'bg-danger/10 text-danger'],
        default => ['value' => 'text-navy', 'icon' => 'bg-slate-100 text-secondary'],
    };

    $valueSize = strlen((string) $value) > 8 ? 'text-lg leading-7 sm:text-xl' : 'text-3xl';
@endphp

<article {{ $attributes->class(['ui-stat-item p-5 sm:p-6']) }}>
    <div class="flex items-start justify-between gap-4">
        <div class="shrink-0">
            @if ($icon)
                <span class="flex size-11 items-center justify-center rounded-lg {{ $toneStyles['icon'] }}"
                    aria-hidden="true">
                    <x-icon :name="$icon" class="size-5" />
                </span>
            @endif
        </div>

        <p class="min-w-0 text-right font-bold tracking-tight {{ $valueSize }} {{ $toneStyles['value'] }}">
            {{ $value }}
        </p>
    </div>

    <div class="mt-4 min-w-0">
        <p class="text-sm font-medium leading-5 text-secondary">{{ $label }}</p>
        @if ($description)
            <p class="mt-1 text-xs leading-5 text-secondary">{{ $description }}</p>
        @endif
    </div>
</article>
