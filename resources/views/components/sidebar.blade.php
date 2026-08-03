@props(['user'])

@php
    $isAdmin = $user?->role === \App\Models\User::ROLE_ADMIN;

    $navigation = $isAdmin
        ? [
            ['label' => 'Dasbor', 'route' => 'admin.dashboard', 'active' => 'admin.dashboard', 'icon' => 'dashboard'],
            ['label' => 'Kelola Tugas', 'route' => 'admin.tasks.index', 'active' => 'admin.tasks.*', 'icon' => 'tasks'],
            [
                'label' => 'Monitoring Pengumpulan',
                'route' => 'admin.submissions.index',
                'active' => 'admin.submissions.*',
                'icon' => 'monitoring',
            ],
            [
                'label' => 'Data Peserta',
                'route' => 'admin.participants.index',
                'active' => 'admin.participants.*',
                'icon' => 'participants',
            ],
        ]
        : [
            ['label' => 'Dasbor', 'route' => 'dashboard', 'active' => 'dashboard', 'icon' => 'dashboard'],
            ['label' => 'Daftar Tugas', 'route' => 'tasks.index', 'active' => 'tasks.*', 'icon' => 'tasks'],
            [
                'label' => 'Pengumpulan Saya',
                'route' => 'submissions.index',
                'active' => 'submissions.*',
                'icon' => 'submissions',
            ],
        ];
@endphp

<aside id="app-sidebar" data-drawer
    class="fixed inset-y-0 left-0 z-50 flex w-72 -translate-x-full flex-col bg-navy text-white shadow-xl transition-transform duration-200 ease-out lg:translate-x-0 lg:shadow-none"
    aria-label="Navigasi utama">
    <div class="flex h-20 shrink-0 items-center justify-between border-b border-white/10 px-5">
        <a href="{{ route($isAdmin ? 'admin.dashboard' : 'dashboard') }}"
            class="flex min-w-0 items-center gap-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-white/60">
            <span
                class="flex size-10 shrink-0 items-center justify-center rounded bg-primary text-white shadow-lg shadow-primary/25">
                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4" />
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M5 3h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Z" />
                </svg>
            </span>
            <span class="min-w-0">
                <span class="block truncate text-sm font-semibold">TaskFlow</span>
                <span class="block truncate text-xs text-slate-400">Pelatihan</span>
            </span>
        </a>

        <button type="button" data-drawer-close
            class="cursor-pointer flex size-11 items-center justify-center rounded text-slate-300 transition hover:bg-white/10 hover:text-white focus:outline-none focus:ring-2 focus:ring-white/60 lg:hidden"
            aria-label="Tutup navigasi">
            <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <div class="px-5 pb-2 pt-6">
        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">
            {{ $isAdmin ? 'Menu Instruktur' : 'Menu Peserta' }}
        </p>
    </div>

    <nav class="flex-1 space-y-1.5 overflow-y-auto px-3 py-2">
        @foreach ($navigation as $item)
            @php($isActive = request()->routeIs($item['active']))

            <a href="{{ route($item['route']) }}" data-drawer-link @class([
                'group flex min-h-11 items-center gap-3 rounded px-3 py-2.5 text-sm font-medium transition focus:outline-none focus:ring-2 focus:ring-white/60',
                'bg-primary text-white shadow-lg shadow-primary/20' => $isActive,
                'text-slate-300 hover:bg-white/10 hover:text-white' => !$isActive,
            ])
                @if ($isActive) aria-current="page" @endif>
                @switch($item['icon'])
                    @case('dashboard')
                        <svg class="size-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 13h8V3H3v10Zm0 8h8v-4H3v4Zm12 0h6V11h-6v10Zm0-14h6V3h-6v4Z" />
                        </svg>
                    @break

                    @case('tasks')
                        <svg class="size-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 5h10M9 12h10M9 19h10M5 5h.01M5 12h.01M5 19h.01" />
                        </svg>
                    @break

                    @case('monitoring')
                        <svg class="size-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 19V9m5 10V5m5 14v-7m5 7V3" />
                        </svg>
                    @break

                    @case('participants')
                        <svg class="size-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm13 10v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" />
                        </svg>
                    @break

                    @default
                        <svg class="size-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 16V4m0 12-4-4m4 4 4-4M4 20h16" />
                        </svg>
                @endswitch

                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>

    <div class="border-t border-white/10 p-3">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="cursor-pointer flex min-h-11 w-full items-center gap-3 rounded px-3 py-2.5 text-sm font-medium text-slate-300 transition hover:bg-danger/20 hover:text-white focus:outline-none focus:ring-2 focus:ring-white/60">
                <svg class="size-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M10 17l5-5-5-5m5 5H3m12-9h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" />
                </svg>
                <span>Keluar</span>
            </button>
        </form>
    </div>
</aside>
