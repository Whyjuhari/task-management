@props(['status', 'label' => null])

@php
    $styles = match ($status) {
        'active', 'submitted' => 'bg-success/10 text-success ring-success/20',
        'draft' => 'bg-slate-100 text-secondary ring-border',
        'late' => 'bg-warning/10 text-amber-700 ring-warning/20',
        'closed' => 'bg-danger/10 text-danger ring-danger/20',
        default => 'bg-primary/10 text-primary ring-primary/20',
    };

    $defaultLabel = match ($status) {
        'active' => 'Aktif',
        'submitted' => 'Terkumpul',
        'draft' => 'Draf',
        'late' => 'Terlambat',
        'closed' => 'Ditutup',
        default => ucfirst(str_replace('_', ' ', $status)),
    };
@endphp

<span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $styles }}">
    {{ $label ?? $defaultLabel }}
</span>
