<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('code') - {{ config('app.name') }}</title>

    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <main class="flex min-h-screen items-center justify-center px-4 py-10 sm:px-6">
        <section class="ui-surface page-enter w-full max-w-lg p-6 text-center sm:p-10">
            <a href="{{ url('/') }}"
                class="mx-auto inline-flex items-center gap-3 rounded-lg focus:outline-none focus:ring-3 focus:ring-primary/20">
                <span class="flex size-11 items-center justify-center rounded-xl bg-primary text-white" aria-hidden="true">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4" />
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M5 3h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Z" />
                    </svg>
                </span>
                <span class="text-left">
                    <span class="block text-sm font-semibold text-navy">TaskFlow</span>
                    <span class="block text-xs text-secondary">Pelatihan</span>
                </span>
            </a>

            <p class="mt-8 text-sm font-semibold uppercase tracking-[0.18em] text-primary">Error @yield('code')</p>
            <h1 class="mt-3 text-2xl font-semibold tracking-tight text-navy sm:text-3xl">@yield('heading')</h1>
            <p class="mx-auto mt-3 max-w-md text-sm leading-7 text-secondary">@yield('message')</p>

            @php
                $destination = auth()->check()
                    ? route(auth()->user()->role === \App\Models\User::ROLE_ADMIN ? 'admin.dashboard' : 'dashboard')
                    : route('login');
            @endphp

            <a href="{{ $destination }}" class="ui-button ui-button-primary mt-7 w-full sm:w-auto">
                @auth
                    Kembali ke Dasbor
                @else
                    Kembali ke Halaman Masuk
                @endauth
            </a>
        </section>
    </main>
</body>

</html>
