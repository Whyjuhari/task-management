<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name') }}</title>

    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <main class="flex min-h-screen items-center justify-center px-6 py-12">
        <section class="w-full max-w-xl rounded border border-border bg-card p-8 shadow-sm sm:p-10">
            <div class="mb-6 flex size-14 items-center justify-center rounded bg-primary text-white shadow-sm">
                <svg class="size-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6" />
                </svg>
            </div>

            <p class="mb-3 text-sm font-semibold uppercase tracking-wider text-primary">Konfigurasi awal berhasil</p>
            <h1 class="text-3xl font-semibold tracking-tight text-navy sm:text-4xl">
                {{ config('app.name') }}
            </h1>
            <p class="mt-4 leading-7 text-secondary">
                Mini Dashboard Manajemen Tugas (Task Management), Menggunakan Laravel Blade
            </p>
        </section>
    </main>
</body>

</html>
