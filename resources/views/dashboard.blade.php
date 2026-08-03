@extends('layouts.app')

@section('title', $pageTitle)

@section('content')
    <x-page-header :title="$pageTitle" :description="'Halo, ' . $participant->name . '. ' . $description" />

    <section aria-label="Statistik dasbor peserta" class="ui-stat-grid mb-6 sm:grid-cols-2 xl:grid-cols-5">
        <x-stat-item label="Total Tugas Aktif" :value="$statistics['total_active_tasks']" icon="active" />
        <x-stat-item label="Sudah Dikumpulkan" :value="$statistics['submitted']" tone="success" icon="done" />
        <x-stat-item label="Belum Dikumpulkan" :value="$statistics['not_submitted']" icon="pending" />
        <x-stat-item label="Terlambat" :value="$statistics['late']" tone="warning" icon="late" />
        <x-stat-item label="Deadline Terdekat" :value="$nearestDeadlineTask
            ? $nearestDeadlineTask->deadline->copy()->locale('id')->translatedFormat('d M Y, H:i')
            : 'Tidak ada'" tone="primary" icon="calendar" :description="$nearestDeadlineTask?->title" />
    </section>



    <div class="grid gap-6 xl:grid-cols-2">
        <section class="ui-surface p-5 sm:p-6">
            <div class="ui-section-heading flex items-start justify-between gap-4">
                <div class="flex min-w-0 items-start gap-3">
                    <span class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-warning/10 text-amber-600"
                        aria-hidden="true">
                        <x-icon name="alert" class="size-5" />
                    </span>
                    <div class="min-w-0">
                        <h2 class="text-lg font-semibold text-navy">Perlu Segera Dikerjakan</h2>
                        <p class="mt-1 text-sm text-secondary">Tugas aktif yang belum Anda kumpulkan.</p>
                    </div>
                </div>
                <a href="{{ route('tasks.index') }}"
                    class="inline-flex min-h-11 shrink-0 items-center text-sm font-semibold text-primary hover:underline">
                    Daftar tugas
                </a>
            </div>

            @if ($urgentTasks->isEmpty())
                <div class="pt-5">
                    <x-empty-state title="Tidak ada tugas mendesak"
                        description="Semua tugas aktif yang tersedia sudah Anda kumpulkan." />
                </div>
            @else
                <div class="space-y-3 pt-5">
                    @foreach ($urgentTasks as $task)
                        <article
                            class="grid gap-3 rounded-lg border border-border bg-slate-50/70 p-4 sm:grid-cols-[1fr_auto] sm:items-start">
                            <div class="flex min-w-0 items-start gap-3">
                                <span
                                    class="mt-0.5 flex size-9 shrink-0 items-center justify-center rounded-lg bg-card text-secondary ring-1 ring-border"
                                    aria-hidden="true">
                                    <x-icon name="file-text" class="size-4" />
                                </span>
                                <div class="min-w-0">
                                    <a href="{{ route('tasks.show', $task) }}"
                                        class="font-semibold text-navy transition hover:text-primary">
                                        {{ $task->title }}
                                    </a>
                                    <p class="mt-1 text-sm text-secondary">{{ $task->category ?: 'Tanpa kategori' }}</p>
                                    <p class="mt-2 text-xs text-secondary">
                                        Deadline {{ $task->deadline->copy()->locale('id')->translatedFormat('d F Y, H:i') }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex shrink-0 flex-col items-start gap-1 sm:items-end">
                                <x-deadline-indicator :deadline="$task->deadline" />
                                <p class="text-xs font-semibold text-secondary">{{ $task->remainingTime() }}</p>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>

        <section class="ui-surface p-5 sm:p-6">
            <div class="ui-section-heading">
                <div class="flex items-start gap-3">
                    <span class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-success/10 text-success"
                        aria-hidden="true">
                        <x-icon name="upload" class="size-5" />
                    </span>
                    <div class="min-w-0">
                        <h2 class="text-lg font-semibold text-navy">Pengumpulan Terbaru Anda</h2>
                        <p class="mt-1 text-sm text-secondary">
                            @if ($latestSubmissionCount > 0)
                                {{ $latestSubmissionCount }} pengumpulan terbaru yang diperbarui.
                            @else
                                Belum ada pengumpulan yang diperbarui.
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            @if ($latestSubmissions->isEmpty())
                <div class="pt-5">
                    <x-empty-state title="Belum ada pengumpulan"
                        description="Pengumpulan terbaru akan tampil setelah Anda mengirim tugas." />
                </div>
            @else
                <div class="space-y-3 pt-5">
                    @foreach ($latestSubmissions as $submission)
                        <article
                            class="grid gap-3 rounded-lg border border-border bg-slate-50/70 p-4 sm:grid-cols-[1fr_auto] sm:items-start">
                            <div class="flex min-w-0 items-start gap-3">
                                <span
                                    class="mt-0.5 flex size-9 shrink-0 items-center justify-center rounded-lg bg-card text-primary ring-1 ring-border"
                                    aria-hidden="true">
                                    <x-icon name="check-circle" class="size-4" />
                                </span>
                                <div class="min-w-0">
                                    <a href="{{ route('submissions.show', $submission) }}"
                                        class="font-semibold text-navy transition hover:text-primary">
                                        {{ $submission->task->title }}
                                    </a>
                                    <p class="mt-1 text-sm text-secondary">
                                        {{ $submission->submitted_at->copy()->locale('id')->translatedFormat('d F Y, H:i') }}
                                    </p>
                                </div>
                            </div>
                            <div class="shrink-0">
                                <x-status-badge :status="$submission->status" />
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>
    </div>

    <section class="ui-surface mb-6 p-5 sm:p-6">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex min-w-0 items-start gap-3">
                <span class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary"
                    aria-hidden="true">
                    <x-icon name="chart-line" class="size-5" />
                </span>
                <div class="min-w-0">
                    <h2 class="text-lg font-semibold text-navy">Progres Tugas Aktif</h2>
                    <p class="mt-1 text-sm text-secondary">Tugas aktif yang sudah Anda kumpulkan, termasuk yang terlambat.
                    </p>
                </div>
            </div>

            <div class="flex shrink-0 items-center gap-3 rounded-lg bg-primary/10 px-4 py-3 text-primary">
                <span class="text-sm font-semibold text-primary/80">Selesai</span>
                <span class="text-2xl font-bold tracking-tight">
                    {{ number_format($statistics['completion_percentage'], 1, ',', '.') }}%
                </span>
            </div>
        </div>

        <div class="mt-5 h-3 overflow-hidden rounded-full bg-slate-200" role="progressbar" aria-label="Progres tugas aktif"
            aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $statistics['completion_percentage'] }}">
            <div class="h-full rounded-full bg-primary transition-[width] duration-300"
                style="width: {{ min(100, $statistics['completion_percentage']) }}%"></div>
        </div>

        <div class="mt-4 grid gap-3 text-sm sm:grid-cols-3">
            <div class="rounded-lg bg-slate-50 px-4 py-3">
                <p class="text-xs font-semibold uppercase text-secondary">Dikumpulkan</p>
                <p class="mt-1 font-semibold text-navy">
                    {{ $statistics['submitted'] + $statistics['late'] }} dari {{ $statistics['total_active_tasks'] }}
                    tugas aktif
                </p>
            </div>
            <div class="rounded-lg bg-slate-50 px-4 py-3">
                <p class="text-xs font-semibold uppercase text-secondary">Belum</p>
                <p class="mt-1 font-semibold text-navy">{{ $statistics['not_submitted'] }} tugas</p>
            </div>
            <div class="rounded-lg bg-slate-50 px-4 py-3">
                <p class="text-xs font-semibold uppercase text-secondary">Terlambat</p>
                <p class="mt-1 font-semibold text-navy">{{ $statistics['late'] }} pengumpulan</p>
            </div>
        </div>
    </section>
@endsection
