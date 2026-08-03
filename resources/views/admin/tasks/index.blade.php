@extends('layouts.app')

@section('title', $pageTitle)

@section('content')
    <x-page-header title="Kelola Tugas" description="Buat, cari, dan kelola tugas peserta pelatihan.">
        <a href="{{ route('admin.tasks.create') }}" class="ui-button ui-button-primary px-5">
            <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14" />
            </svg>
            Buat Tugas
        </a>
    </x-page-header>

    @if ($errors->any())
        <div role="alert" class="mb-6 rounded-xl border border-danger/20 bg-danger/5 px-4 py-3 text-sm text-danger">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="GET" action="{{ route('admin.tasks.index') }}"
        class="ui-filter-panel mb-6 grid gap-4 p-4 sm:p-5 md:grid-cols-[minmax(0,1fr)_220px_auto] md:items-end">
        <div>
            <label for="search" class="ui-label">Cari berdasarkan judul</label>
            <input id="search" name="search" type="search" value="{{ $search }}"
                placeholder="Masukkan judul tugas" class="ui-control">
        </div>

        <div>
            <label for="status-filter" class="ui-label">Filter status</label>
            <select id="status-filter" name="status" class="ui-control">
                <option value="">Semua status</option>
                <option value="draft" @selected($status === 'draft')>Draf</option>
                <option value="active" @selected($status === 'active')>Aktif</option>
                <option value="closed" @selected($status === 'closed')>Ditutup</option>
            </select>
        </div>

        <div class="flex flex-wrap gap-2">
            <button type="submit" class="ui-button ui-button-primary">
                Terapkan
            </button>
            @if ($search !== '' || $status !== null)
                <a href="{{ route('admin.tasks.index') }}" class="ui-button ui-button-secondary">
                    Reset
                </a>
            @endif
        </div>
    </form>

    @if ($tasks->isEmpty())
        <x-empty-state :title="$search !== '' || $status !== null ? 'Tugas tidak ditemukan' : 'Belum ada tugas'" :description="$search !== '' || $status !== null
                        ? 'Coba ubah kata pencarian atau filter status yang digunakan.'
                        : 'Buat tugas pertama untuk memulai kegiatan pelatihan.'">
            @if ($search === '' && $status === null)
                <a href="{{ route('admin.tasks.create') }}"
                    class="inline-flex min-h-11 items-center rounded-xl bg-primary px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-navy focus:outline-none focus:ring-3 focus:ring-primary/30">
                    Buat Tugas
                </a>
            @endif
        </x-empty-state>
    @else
        <div class="ui-surface hidden overflow-hidden xl:block">
            <div class="overflow-x-auto" tabindex="0" aria-label="Tabel tugas dapat digeser secara horizontal">
                <table class="w-full min-w-920px text-left text-sm">
                    <caption class="sr-only">Daftar tugas pelatihan</caption>
                    <thead class="border-b border-border bg-slate-50 text-xs uppercase tracking-wider text-secondary">
                        <tr>
                            <th scope="col" class="px-5 py-4 font-semibold">Tugas</th>
                            <th scope="col" class="px-5 py-4 font-semibold">Deadline</th>
                            <th scope="col" class="px-5 py-4 font-semibold">Status</th>
                            <th scope="col" class="px-5 py-4 font-semibold">Ubah status</th>
                            <th scope="col" class="px-5 py-4 text-right font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @foreach ($tasks as $task)
                            <tr class="align-top transition hover:bg-slate-50/70">
                                <td class="px-5 py-4">
                                    <a href="{{ route('admin.tasks.show', $task) }}"
                                        class="font-semibold text-navy transition hover:text-primary">
                                        {{ $task->title }}
                                    </a>
                                    <p class="mt-1 text-xs text-secondary">{{ $task->category ?: 'Tanpa kategori' }}</p>
                                </td>
                                <td class="whitespace-nowrap px-5 py-4 text-secondary">
                                    <p>{{ $task->deadline->format('d/m/Y H:i') }}</p>
                                    <div class="mt-2"><x-deadline-indicator :deadline="$task->deadline" /></div>
                                </td>
                                <td class="px-5 py-4">
                                    <x-status-badge :status="$task->status" />
                                </td>
                                <td class="px-5 py-4">
                                    @include('admin.tasks._status-form', [
                                        'task' => $task,
                                        'context' => 'desktop',
                                    ])
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('admin.tasks.show', $task) }}"
                                            class="inline-flex min-h-11 items-center rounded-lg border border-border px-3 py-2 font-semibold text-navy transition hover:bg-slate-100">
                                            Detail
                                        </a>
                                        <a href="{{ route('admin.tasks.edit', $task) }}"
                                            class="inline-flex min-h-11 items-center rounded-lg border border-border px-3 py-2 font-semibold text-primary transition hover:bg-primary/5">
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('admin.tasks.destroy', $task) }}"
                                            data-confirm-delete data-task-title="{{ $task->title }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="min-h-11 cursor-pointer rounded-lg border border-danger/30 px-3 py-2 font-semibold text-danger transition hover:bg-danger/5">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="space-y-4 xl:hidden">
            @foreach ($tasks as $task)
                <article class="ui-surface p-5">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <a href="{{ route('admin.tasks.show', $task) }}"
                                class="font-semibold text-navy transition hover:text-primary">
                                {{ $task->title }}
                            </a>
                            <p class="mt-1 text-xs text-secondary">{{ $task->category ?: 'Tanpa kategori' }}</p>
                        </div>
                        <x-status-badge :status="$task->status" />
                    </div>

                    <dl class="mt-4 border-y border-border py-3">
                        <div class="flex items-center justify-between gap-4">
                            <dt class="text-sm text-secondary">Deadline</dt>
                            <dd class="text-right text-sm font-semibold text-navy">
                                <p>{{ $task->deadline->format('d/m/Y H:i') }}</p>
                                <div class="mt-2"><x-deadline-indicator :deadline="$task->deadline" /></div>
                            </dd>
                        </div>
                    </dl>

                    <div class="mt-4">
                        @include('admin.tasks._status-form', ['task' => $task, 'context' => 'mobile'])
                    </div>

                    <div class="mt-4 grid grid-cols-3 gap-2">
                        <a href="{{ route('admin.tasks.show', $task) }}"
                            class="inline-flex min-h-11 items-center justify-center rounded-lg border border-border px-2 py-2 text-sm font-semibold text-navy">
                            Detail
                        </a>
                        <a href="{{ route('admin.tasks.edit', $task) }}"
                            class="inline-flex min-h-11 items-center justify-center rounded-lg border border-border px-2 py-2 text-sm font-semibold text-primary">
                            Edit
                        </a>
                        <form method="POST" action="{{ route('admin.tasks.destroy', $task) }}" data-confirm-delete
                            data-task-title="{{ $task->title }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="min-h-11 w-full cursor-pointer rounded-lg border border-danger/30 px-2 py-2 text-sm font-semibold text-danger">
                                Hapus
                            </button>
                        </form>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $tasks->links('components.pagination') }}
        </div>
    @endif
@endsection
