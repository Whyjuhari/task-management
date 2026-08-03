@props(['status', 'label' => null])

@php
    $styles = match ($status) {
        'active', 'submitted' => 'bg-success/10 text-success-strong ring-success/20',
        'draft', 'not_submitted' => 'bg-slate-100 text-secondary ring-border',
        'late' => 'bg-warning/10 text-amber-700 ring-warning/20',
        'closed', 'deadline_ended' => 'bg-danger/10 text-danger ring-danger/20',
        default => 'bg-primary/10 text-primary ring-primary/20',
    };

    $defaultLabel = match ($status) {
        'active' => 'Aktif',
        'submitted' => 'Sudah Dikumpulkan',
        'draft' => 'Draf',
        'not_submitted' => 'Belum Dikumpulkan',
        'late' => 'Terlambat',
        'closed' => 'Ditutup',
        'deadline_ended' => 'Deadline Berakhir',
        default => ucfirst(str_replace('_', ' ', $status)),
    };
@endphp

<span class="inline-flex max-w-full items-center whitespace-nowrap rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $styles }}">
    {{ $label ?? $defaultLabel }}
</span>
