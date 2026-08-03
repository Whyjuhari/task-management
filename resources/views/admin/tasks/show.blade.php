@extends('layouts.app')

@section('title', $pageTitle)

@php
    $submissionTypeLabels = [
        'file' => 'File',
        'link' => 'Tautan',
        'file_or_link' => 'File atau tautan',
    ];
@endphp

@section('content')
    <x-page-header :title="$task->title" description="Detail lengkap tugas pelatihan.">
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.tasks.index') }}"
                class="ui-button ui-button-secondary">
                Kembali
            </a>
            <a href="{{ route('admin.tasks.edit', $task) }}"
                class="ui-button ui-button-primary">
                Edit Tugas
            </a>
        </div>
    </x-page-header>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_320px]">
        <div class="space-y-6">
            <section class="ui-surface p-5 sm:p-8">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-border pb-5">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-secondary">Status tugas</p>
                        <div class="mt-2"><x-status-badge :status="$task->status" /></div>
                    </div>
                    @include('admin.tasks._status-form', ['task' => $task, 'context' => 'show'])
                </div>

                <dl class="mt-6 grid gap-5 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm text-secondary">Kategori</dt>
                        <dd class="mt-1 text-sm font-semibold text-navy">{{ $task->category ?: 'Tanpa kategori' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-secondary">Jenis pengumpulan</dt>
                        <dd class="mt-1 text-sm font-semibold text-navy">
                            {{ $submissionTypeLabels[$task->submission_type] ?? $task->submission_type }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm text-secondary">Tanggal mulai</dt>
                        <dd class="mt-1 text-sm font-semibold text-navy">
                            {{ $task->start_date?->format('d/m/Y H:i') ?? 'Tidak ditentukan' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm text-secondary">Deadline</dt>
                        <dd class="mt-1 text-sm font-semibold text-navy">{{ $task->deadline->format('d/m/Y H:i') }}</dd>
                        <dd class="mt-2"><x-deadline-indicator :deadline="$task->deadline" /></dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-sm text-secondary">Dibuat oleh</dt>
                        <dd class="mt-1 text-sm font-semibold text-navy">{{ $task->creator->name }}</dd>
                    </div>
                </dl>
            </section>

            <section class="ui-surface p-5 sm:p-8">
                <h2 class="text-lg font-semibold text-navy">Deskripsi</h2>
                <p class="mt-3 whitespace-pre-line text-sm leading-7 text-secondary">{{ $task->description }}</p>

                <div class="mt-6 border-t border-border pt-6">
                    <h2 class="text-lg font-semibold text-navy">Instruksi</h2>
                    <p class="mt-3 whitespace-pre-line text-sm leading-7 text-secondary">
                        {{ $task->instructions ?: 'Tidak ada instruksi tambahan.' }}
                    </p>
                </div>
            </section>
        </div>

        <aside class="ui-surface h-fit border-danger/20 p-5">
            <h2 class="font-semibold text-navy">Hapus tugas</h2>
            <p class="mt-2 text-sm leading-6 text-secondary">
                Tugas dan seluruh pengumpulan terkait akan dihapus permanen.
            </p>
            <form method="POST" action="{{ route('admin.tasks.destroy', $task) }}" class="mt-4" data-confirm-delete
                data-task-title="{{ $task->title }}">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="ui-button ui-button-danger w-full">
                    Hapus Tugas
                </button>
            </form>
        </aside>
    </div>
@endsection
