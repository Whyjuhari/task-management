@extends('layouts.app')

@section('title', $pageTitle)

@section('content')
    <x-page-header title="Detail Pengumpulan" :description="$task->title">
        <a href="{{ route('tasks.show', $task) }}"
            class="inline-flex min-h-11 items-center rounded-xl border border-border bg-card px-4 py-2.5 text-sm font-semibold text-navy transition hover:bg-slate-50 focus:outline-none focus:ring-3 focus:ring-primary/15">
            Lihat Tugas
        </a>
    </x-page-header>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_320px]">
        <section class="rounded-xl border border-border bg-card p-5 shadow-sm sm:p-8">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-border pb-5">
                <div>
                    <p class="text-sm text-secondary">Status pengumpulan</p>
                    <div class="mt-2"><x-status-badge :status="$submission->status" /></div>
                </div>
                <p class="text-sm font-semibold text-navy">
                    {{ $submission->submitted_at->copy()->locale('id')->translatedFormat('d F Y, H:i') }}
                </p>
            </div>

            <dl class="mt-6 space-y-6">
                <div>
                    <dt class="text-sm text-secondary">File</dt>
                    <dd class="mt-1 break-words text-sm font-semibold text-navy">
                        {{ $submission->original_file_name ?: 'Tidak ada file' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-sm text-secondary">Tautan</dt>
                    <dd class="mt-1 break-all text-sm font-semibold">
                        @if ($submission->submission_link)
                            <a href="{{ $submission->submission_link }}" target="_blank" rel="noopener noreferrer"
                                class="text-primary underline decoration-primary/30 underline-offset-4 hover:decoration-primary">
                                {{ $submission->submission_link }}
                            </a>
                        @else
                            <span class="text-navy">Tidak ada tautan</span>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-sm text-secondary">Catatan</dt>
                    <dd class="mt-2 whitespace-pre-line text-sm leading-7 text-navy">
                        {{ $submission->note ?: 'Tidak ada catatan.' }}
                    </dd>
                </div>
            </dl>
        </section>

        <aside class="h-fit rounded-xl border border-border bg-card p-5 shadow-sm sm:p-6">
            <p class="text-xs font-semibold uppercase tracking-wider text-secondary">Informasi waktu</p>
            <dl class="mt-4 space-y-4">
                <div>
                    <dt class="text-sm text-secondary">Dikumpulkan</dt>
                    <dd class="mt-1 text-sm font-semibold text-navy">
                        {{ $submission->submitted_at->copy()->locale('id')->translatedFormat('d F Y, H:i') }}
                    </dd>
                </div>
                <div>
                    <dt class="text-sm text-secondary">Deadline</dt>
                    <dd class="mt-1 text-sm font-semibold text-navy">
                        {{ $task->deadline->copy()->locale('id')->translatedFormat('d F Y, H:i') }}
                    </dd>
                </div>
            </dl>

            <div class="mt-5 border-t border-border pt-5">
                @if ($task->canBeSubmitted())
                    <a href="{{ route('submissions.edit', $submission) }}"
                        class="inline-flex min-h-11 w-full items-center justify-center rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 focus:outline-none focus:ring-3 focus:ring-primary/25">
                        Perbarui Pengumpulan
                    </a>
                @else
                    <button type="button" disabled
                        class="min-h-11 w-full cursor-not-allowed rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white opacity-60">
                        Perbarui Pengumpulan
                    </button>
                    <p class="mt-3 text-xs leading-5 text-secondary">
                        Tugas telah ditutup atau belum memasuki tanggal mulai.
                    </p>
                @endif
            </div>
        </aside>
    </div>
@endsection
