<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Masuk - {{ config('app.name') }}</title>

    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <main class="flex min-h-screen items-center justify-center px-6 py-12">
        <section class="w-full max-w-md rounded-2xl border border-border bg-card p-8 shadow-sm sm:p-10">
            <div class="mb-8">
                <div class="mb-5 flex size-12 items-center justify-center rounded-xl bg-primary text-white">
                    <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4" />
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M5 3h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Z" />
                    </svg>
                </div>

                <p class="text-sm font-semibold text-primary">{{ config('app.name') }}</p>
                <h1 class="mt-2 text-3xl font-semibold tracking-tight text-navy">Masuk ke akun Anda</h1>
                <p class="mt-3 text-sm leading-6 text-secondary">
                    Gunakan akun instruktur atau peserta yang telah disediakan.
                </p>
            </div>

            <form method="POST" action="{{ route('login.store') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="mb-2 block text-sm font-medium text-navy">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email"
                        autofocus required
                        class="w-full rounded-xl border border-border bg-card px-4 py-3 text-sm text-navy outline-none transition focus:border-primary focus:ring-3 focus:ring-primary/15">
                    @error('email')
                        <p class="mt-2 text-sm text-danger">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="mb-2 block text-sm font-medium text-navy">Kata sandi</label>
                    <input id="password" name="password" type="password" autocomplete="current-password" required
                        class="w-full rounded-xl border border-border bg-card px-4 py-3 text-sm text-navy outline-none transition focus:border-primary focus:ring-3 focus:ring-primary/15">
                    @error('password')
                        <p class="mt-2 text-sm text-danger">{{ $message }}</p>
                    @enderror
                </div>

                <label class="flex cursor-pointer items-center gap-3 text-sm text-secondary">
                    <input name="remember" type="checkbox" value="1"
                        class="size-4 rounded border-border text-primary focus:ring-primary">
                    <span>Ingat saya</span>
                </label>

                <button type="submit"
                    class="w-full rounded-xl bg-primary px-4 py-3 text-sm font-semibold text-white transition hover:bg-blue-700 focus:outline-none focus:ring-3 focus:ring-primary/30">
                    Masuk
                </button>
            </form>
        </section>
    </main>
</body>

</html>
