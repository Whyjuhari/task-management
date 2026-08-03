@props(['deadline'])

@php
    $resolvedDeadline = $deadline instanceof \Carbon\CarbonInterface
        ? $deadline->copy()
        : \Illuminate\Support\Carbon::parse($deadline);
    $now = now();

    if ($resolvedDeadline->isPast()) {
        $label = 'Deadline lewat';
        $styles = 'bg-danger/10 text-danger ring-danger/20';
        $dotStyles = 'bg-danger';
    } elseif ($resolvedDeadline->lessThan($now->copy()->addDay())) {
        $label = 'Kurang dari 1 hari';
        $styles = 'bg-danger/10 text-danger ring-danger/20';
        $dotStyles = 'bg-danger';
    } elseif ($resolvedDeadline->lessThanOrEqualTo($now->copy()->addDays(3))) {
        $label = '1–3 hari';
        $styles = 'bg-warning/10 text-amber-700 ring-warning/25';
        $dotStyles = 'bg-warning';
    } else {
        $label = 'Lebih dari 3 hari';
        $styles = 'bg-success/10 text-success-strong ring-success/20';
        $dotStyles = 'bg-success';
    }

    $formattedDeadline = $resolvedDeadline->locale('id')->translatedFormat('d F Y, H:i');
@endphp

<span title="Deadline {{ $formattedDeadline }}" aria-label="Indikator deadline: {{ $label }}"
    class="inline-flex max-w-full items-center gap-2 whitespace-nowrap rounded-md px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $styles }}">
    <span class="size-1.5 rounded-full {{ $dotStyles }}" aria-hidden="true"></span>
    {{ $label }}
</span>
