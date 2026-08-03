@props([
    'label',
    'value',
    'tone' => 'default',
    'description' => null,
])

@php
    $toneStyles = match ($tone) {
        'primary' => ['value' => 'text-primary', 'marker' => 'bg-primary'],
        'success' => ['value' => 'text-success-strong', 'marker' => 'bg-success'],
        'warning' => ['value' => 'text-amber-700', 'marker' => 'bg-warning'],
        'danger' => ['value' => 'text-danger', 'marker' => 'bg-danger'],
        default => ['value' => 'text-navy', 'marker' => 'bg-slate-300'],
    };
@endphp

<article {{ $attributes->class(['ui-stat-item p-5 sm:p-6']) }}>
    <div class="flex items-center gap-2">
        <span class="size-2 rounded-full {{ $toneStyles['marker'] }}" aria-hidden="true"></span>
        <p class="min-w-0 text-sm font-medium text-secondary">{{ $label }}</p>
    </div>
    <p class="mt-2 text-3xl font-bold tracking-tight {{ $toneStyles['value'] }}">{{ $value }}</p>
    @if ($description)
        <p class="mt-1 text-xs leading-5 text-secondary">{{ $description }}</p>
    @endif
</article>
