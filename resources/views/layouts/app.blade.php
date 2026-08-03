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
    <div class="min-h-screen bg-background">
        <div data-drawer-overlay class="fixed inset-0 z-40 hidden bg-navy/60 lg:hidden"
            aria-hidden="true"></div>

        <x-sidebar :user="auth()->user()" />

        <div data-app-content class="min-w-0 lg:pl-72">
            <x-header :title="$pageTitle ?? 'Dasbor'" :user="auth()->user()" />

            <main id="main-content" class="min-w-0 px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
                <div class="mx-auto w-full max-w-7xl">
                    <x-flash-message />

                    @yield('content')
                </div>
            </main>
        </div>
    </div>
</body>

</html>
