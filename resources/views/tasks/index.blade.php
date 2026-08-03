@extends('layouts.app')

@section('title', $pageTitle)

@php($participant = auth()->user())

@section('content')
    <x-page-header title="Daftar Tugas"
        description="Lihat tugas aktif dan tugas yang telah ditutup, diurutkan berdasarkan deadline terdekat." />

    @if ($errors->any())
        <div role="alert" class="mb-6 rounded-xl border border-danger/20 bg-danger/5 px-4 py-3 text-sm text-danger">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="GET" action="{{ route('tasks.index') }}"
        class="ui-filter-panel mb-6 grid gap-4 p-4 sm:p-5 md:grid-cols-[minmax(0,1fr)_240px_auto] md:items-end">
        <div>
            <label for="search" class="ui-label">Cari berdasarkan judul</label>
            <input id="search" name="search" type="search" value="{{ $search }}"
                placeholder="Masukkan judul tugas"
                class="ui-control">
        </div>

        <div>
            <label for="category-filter" class="ui-label">Filter kategori</label>
            <select id="category-filter" name="category"
                class="ui-control">
                <option value="">Semua kategori</option>
                @foreach ($categories as $categoryOption)
                    <option value="{{ $categoryOption }}" @selected($category === $categoryOption)>
                        {{ $categoryOption }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="flex flex-wrap gap-2">
            <button type="submit"
                class="ui-button ui-button-primary">
                Terapkan
            </button>
            @if ($search !== '' || $category !== null)
                <a href="{{ route('tasks.index') }}"
                    class="ui-button ui-button-secondary">
                    Reset
                </a>
            @endif
        </div>
    </form>

    @if ($tasks->isEmpty())
        <x-empty-state title="Tugas tidak ditemukan"
            description="Belum ada tugas yang sesuai dengan pencarian atau kategori yang dipilih." />
    @else
        <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($tasks as $task)
                @php($personalStatus = $task->personalStatusFor($participant))

                <article class="ui-surface flex min-w-0 flex-col p-5 transition hover:border-slate-300">
                    <div class="flex flex-wrap items-center gap-2">
                        <x-status-badge :status="$task->status" />
                        <x-status-badge :status="$personalStatus" />
                    </div>

                    <div class="mt-5 min-w-0 flex-1">
                        <p class="text-xs font-semibold uppercase tracking-wider text-secondary">
                            {{ $task->category ?: 'Tanpa kategori' }}
                        </p>
                        <h2 class="mt-2 text-lg font-semibold leading-7 text-navy">
                            <a href="{{ route('tasks.show', $task) }}" class="transition hover:text-primary">
                                {{ $task->title }}
                            </a>
                        </h2>
                        <p class="mt-3 line-clamp-3 text-sm leading-6 text-secondary">{{ $task->description }}</p>
                    </div>

                    <dl class="mt-5 space-y-3 border-t border-border pt-5">
                        <div>
                            <dt class="text-xs font-medium text-secondary">Deadline</dt>
                            <dd class="mt-1 text-sm font-semibold text-navy">
                                {{ $task->deadline->copy()->locale('id')->translatedFormat('d F Y, H:i') }}
                            </dd>
                            <dd class="mt-2"><x-deadline-indicator :deadline="$task->deadline" /></dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-secondary">Sisa waktu</dt>
                            <dd @class([
                                'mt-1 text-sm font-semibold',
                                'text-danger' => !$task->canBeSubmitted(),
                                'text-primary' => $task->canBeSubmitted(),
                            ])>
                                {{ $task->remainingTime() }}
                            </dd>
                        </div>
                    </dl>

                    <a href="{{ route('tasks.show', $task) }}"
                        class="mt-5 inline-flex min-h-11 items-center justify-center rounded-xl border border-primary px-4 py-2.5 text-sm font-semibold text-primary transition hover:bg-primary hover:text-white focus:outline-none focus:ring-3 focus:ring-primary/20">
                        Lihat Detail
                    </a>
                </article>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $tasks->links('components.pagination') }}
        </div>
    @endif
@endsection
