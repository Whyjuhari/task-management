@props(['title', 'user'])

@php
    $isAdmin = $user?->role === \App\Models\User::ROLE_ADMIN;
    $roleLabel = $isAdmin ? 'Admin / Instruktur' : 'Peserta';
    $initial = mb_strtoupper(mb_substr($user?->name ?? 'P', 0, 1));
@endphp

<header class="sticky top-0 z-30 border-b border-border bg-card">
    <div class="grid min-h-20 grid-cols-[auto_minmax(0,1fr)_auto] items-center gap-3 px-4 sm:px-6 lg:px-8">
        <button type="button" data-drawer-open
            class="cursor-pointer flex size-11 items-center justify-center rounded-xl border border-border text-navy transition hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-primary/30 lg:hidden"
            aria-controls="app-sidebar" aria-expanded="false">
            <span class="sr-only">Buka navigasi</span>
            <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        <div class="min-w-0">
            <p class="text-xs font-medium text-secondary">{{ config('app.name') }}</p>
            <h1 class="truncate text-base font-semibold text-navy sm:text-lg">{{ $title }}</h1>
        </div>

        <div class="flex min-w-0 items-center justify-end gap-2 sm:gap-3">
            <div class="flex size-9 shrink-0 items-center justify-center rounded-full bg-primary/10 text-sm font-semibold text-primary shadow-sm sm:size-10"
                aria-hidden="true">
                {{ $initial }}
            </div>
            <div class="hidden min-w-0 max-w-40 text-right sm:block">
                <p class="truncate text-sm font-semibold text-navy">{{ $user?->name }}</p>
                <p class="truncate text-xs text-secondary">{{ $roleLabel }}</p>
            </div>
        </div>
    </div>
</header>
