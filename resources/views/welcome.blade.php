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
    <main class="flex min-h-screen items-center justify-center px-4 py-10 sm:px-6">
        <section class="page-enter ui-surface w-full max-w-2xl overflow-hidden">
            <div class="border-b border-border px-6 py-5 sm:px-8">
                <div class="flex items-center gap-3">
                    <span class="flex size-10 items-center justify-center rounded-lg bg-primary text-white"
                        aria-hidden="true">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M5 3h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Z" />
                        </svg>
                    </span>
                    <div>
                        <p class="text-sm font-semibold text-navy">TaskFlow</p>
                        <p class="text-xs text-secondary">Pelatihan</p>
                    </div>
                </div>
            </div>

            <div class="px-6 py-8 sm:px-8 sm:py-10">
                <p class="text-sm font-semibold text-primary">Setup Awal Dashboard Task Management.</p>
                <h1 class="mt-2 text-3xl font-semibold tracking-tight text-navy sm:text-4xl">
                    {{ config('app.name') }}
                </h1>
                <p class="mt-4 max-w-xl text-sm leading-7 text-secondary sm:text-base">
                    Mini dashboard manajemen tugas untuk instruktur dan peserta pelatihan.
                </p>

                <a href="{{ route('login') }}" class="ui-button ui-button-primary mt-7 w-full sm:w-auto">
                    Masuk ke Aplikasi
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6" />
                    </svg>
                </a>
            </div>
        </section>
    </main>
</body>

</html>
