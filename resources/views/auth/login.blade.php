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
    <main class="flex min-h-screen items-center justify-center px-4 py-8 sm:px-6 lg:py-12">
        <section class="page-enter ui-surface grid w-full max-w-5xl overflow-hidden lg:grid-cols-[0.9fr_1.1fr]">
            <div class="hidden bg-primary p-10 text-white lg:flex lg:flex-col lg:justify-between">
                <a href="{{ url('/') }}"
                    class="flex w-fit items-center gap-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-white/60">
                    <span
                        class="flex size-10 items-center justify-center rounded-lg bg-white/10 ring-1 ring-inset ring-white/10"
                        aria-hidden="true">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M5 3h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Z" />
                        </svg>
                    </span>
                    <span>
                        <span class="block text-sm font-semibold">TaskFlow</span>
                        <span class="block text-xs text-slate-400">Pelatihan</span>
                    </span>
                </a>

                <div class="my-14 max-w-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Manajemen tugas
                        pelatihan</p>
                    <h1 class="mt-4 text-3xl font-semibold leading-tight tracking-tight">
                        Satu ruang kerja untuk semua.
                    </h1>
                    <p class="mt-4 text-sm leading-7 text-slate-300">
                        Kelola tugas, pantau pengumpulan, dan ikuti deadline melalui akses yang sesuai dengan peran
                        pengguna.
                    </p>
                </div>

                <p class="text-xs leading-5 text-slate-400">Prototype Mini Dashboard Manajemen Tugas</p>
            </div>

            <div class="p-5 sm:p-8 lg:p-12">
                <div class="mb-8 lg:hidden">
                    <a href="{{ url('/') }}"
                        class="inline-flex items-center gap-3 rounded-lg focus:outline-none focus:ring-3 focus:ring-primary/20">
                        <span class="flex size-10 items-center justify-center rounded-lg bg-primary text-white"
                            aria-hidden="true">
                            <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
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
                </div>

                <div class="mb-8">
                    <p class="text-sm font-semibold text-primary">Selamat datang</p>
                    <h1 class="mt-2 text-3xl font-semibold tracking-tight text-navy">Masuk ke akun Anda</h1>
                    <p class="mt-3 text-sm leading-6 text-secondary">
                        Gunakan akun instruktur atau peserta yang telah disediakan.
                    </p>
                </div>

                <form method="POST" action="{{ route('login.store') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="email" class="ui-label">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}"
                            autocomplete="email" autofocus required
                            @error('email') aria-invalid="true" aria-describedby="email-error" @enderror
                            @class([
                                'ui-control',
                                'border-danger focus:border-danger' => $errors->has('email'),
                            ])>
                        @error('email')
                            <p id="email-error" role="alert" class="mt-2 text-sm text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="ui-label">Kata sandi</label>
                        <input id="password" name="password" type="password" autocomplete="current-password" required
                            @error('password') aria-invalid="true" aria-describedby="password-error" @enderror
                            @class([
                                'ui-control',
                                'border-danger focus:border-danger' => $errors->has('password'),
                            ])>
                        @error('password')
                            <p id="password-error" role="alert" class="mt-2 text-sm text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <label class="flex min-h-11 cursor-pointer items-center gap-3 text-sm text-secondary">
                        <input name="remember" type="checkbox" value="1"
                            class="size-5 rounded border-border text-primary focus:ring-primary">
                        <span>Ingat saya</span>
                    </label>

                    <button type="submit" class="ui-button ui-button-primary w-full">Masuk</button>
                </form>
            </div>
        </section>
    </main>
</body>

</html>
