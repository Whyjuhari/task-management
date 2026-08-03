<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $pageTitle }} - {{ config('app.name') }}</title>

    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <main class="flex min-h-screen items-center justify-center px-6 py-12">
        <section class="w-full max-w-xl rounded-2xl border border-border bg-card p-8 shadow-sm sm:p-10">
            <span class="inline-flex rounded-full bg-primary/10 px-3 py-1 text-sm font-semibold text-primary">
                {{ $roleLabel }}
            </span>

            <h1 class="mt-5 text-3xl font-semibold tracking-tight text-navy">{{ $pageTitle }}</h1>
            <p class="mt-3 leading-7 text-secondary">
                Anda berhasil masuk. Halaman ini masih berupa halaman sementara untuk tahap autentikasi dan pembatasan peran.
            </p>

            <dl class="mt-8 divide-y divide-border rounded-xl border border-border">
                <div class="grid gap-1 px-4 py-4 sm:grid-cols-3 sm:gap-4">
                    <dt class="text-sm font-medium text-secondary">Nama</dt>
                    <dd class="text-sm font-semibold text-navy sm:col-span-2">{{ $user->name }}</dd>
                </div>
                <div class="grid gap-1 px-4 py-4 sm:grid-cols-3 sm:gap-4">
                    <dt class="text-sm font-medium text-secondary">Email</dt>
                    <dd class="text-sm font-semibold text-navy sm:col-span-2">{{ $user->email }}</dd>
                </div>
                <div class="grid gap-1 px-4 py-4 sm:grid-cols-3 sm:gap-4">
                    <dt class="text-sm font-medium text-secondary">Peran</dt>
                    <dd class="text-sm font-semibold text-navy sm:col-span-2">{{ $user->role }}</dd>
                </div>
            </dl>

            <form method="POST" action="{{ route('logout') }}" class="mt-8">
                @csrf
                <button type="submit"
                    class="rounded-xl border border-border bg-card px-5 py-3 text-sm font-semibold text-navy transition hover:border-danger hover:text-danger focus:outline-none focus:ring-3 focus:ring-danger/15">
                    Keluar
                </button>
            </form>
        </section>
    </main>
</body>

</html>
