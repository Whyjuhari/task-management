<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', $pageTitle ?? 'Dasbor') - {{ config('app.name') }}</title>

    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="overflow-x-hidden antialiased">
    <a href="#main-content"
        class="fixed left-4 top-3 z-70 -translate-y-20 rounded-lg bg-navy px-4 py-2.5 text-sm font-semibold text-white transition focus:translate-y-0">
        Lewati ke konten utama
    </a>

    <div class="min-h-screen bg-background">
        <div data-drawer-overlay
            class="pointer-events-none invisible fixed inset-0 z-40 bg-navy/60 opacity-0 transition-opacity duration-200 lg:hidden"
            aria-hidden="true"></div>

        <x-sidebar :user="auth()->user()" />

        <div data-app-content class="min-w-0 lg:pl-64">
            <x-header :title="$pageTitle ?? 'Dasbor'" :user="auth()->user()" />

            <main id="main-content" class="min-w-0 px-4 py-6 sm:px-6 lg:px-8 lg:py-7">
                <div class="page-enter mx-auto w-full max-w-7xl">
                    <x-flash-message />

                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <x-confirm-dialog />
</body>

</html>
