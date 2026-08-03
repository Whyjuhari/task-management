@props(['type' => null, 'message' => null])

@php
    $resolvedType = $type;
    $resolvedMessage = $message;

    foreach (['success', 'warning', 'error', 'status'] as $sessionType) {
        if ($resolvedMessage === null && session()->has($sessionType)) {
            $resolvedType = $sessionType === 'status' ? 'success' : $sessionType;
            $resolvedMessage = session($sessionType);
        }
    }

    $styles = match ($resolvedType) {
        'error' => 'border-danger/20 bg-danger/5 text-danger',
        'warning' => 'border-warning/25 bg-warning/10 text-amber-800',
        default => 'border-success/20 bg-success/5 text-success-strong',
    };
@endphp

@if ($resolvedMessage)
    <div data-flash-message role="{{ $resolvedType === 'error' ? 'alert' : 'status' }}"
        aria-live="{{ $resolvedType === 'error' ? 'assertive' : 'polite' }}"
        class="mb-6 flex min-h-12 items-center justify-between gap-3 rounded-xl border px-4 py-3 text-sm font-medium {{ $styles }}">
        <p class="min-w-0 flex-1 break-words">{{ $resolvedMessage }}</p>
        <button type="button" data-flash-dismiss
            class="flex size-11 shrink-0 items-center justify-center rounded-lg transition hover:bg-black/5 focus:outline-none focus:ring-2 focus:ring-current/30"
            aria-label="Tutup pesan">
            <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
@endif
