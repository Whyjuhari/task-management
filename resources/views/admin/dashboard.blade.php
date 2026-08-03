@extends('layouts.app')

@section('title', $pageTitle)

@section('content')
    <x-page-header :title="$pageTitle"
        :description="'Selamat datang, '.auth()->user()->name.'. '.$description" />

    <section aria-label="Statistik dasbor admin"
        class="ui-stat-grid mb-6 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-6">
        <x-stat-item label="Total Tugas" :value="$statistics['total_tasks']" icon="tasks" />
        <x-stat-item label="Tugas Aktif" :value="$statistics['active_tasks']" tone="primary" icon="active" />
        <x-stat-item label="Total Peserta" :value="$statistics['total_participants']" icon="participants" />
        <x-stat-item label="Total Pengumpulan" :value="$statistics['total_submissions']" tone="success" icon="submissions" />
        <x-stat-item label="Pengumpulan Terlambat" :value="$statistics['late_submissions']" tone="warning" icon="late" />
        <x-stat-item label="Mendekati Deadline" :value="$statistics['near_deadline_tasks']" tone="danger" icon="deadline" />
    </section>

    <div class="grid gap-6 xl:grid-cols-2">
        <section class="ui-surface p-5 sm:p-6">
            <div class="ui-section-heading flex items-start justify-between gap-4">
                <div class="flex min-w-0 items-start gap-3">
                    <span class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary"
                        aria-hidden="true">
                        <x-icon name="list" class="size-5" />
                    </span>
                    <div class="min-w-0">
                        <h2 class="text-lg font-semibold text-navy">Tugas Terbaru</h2>
                        <p class="mt-1 text-sm text-secondary">Lima tugas yang terakhir dibuat.</p>
                    </div>
                </div>
                <a href="{{ route('admin.tasks.index') }}"
                    class="inline-flex min-h-11 shrink-0 items-center text-sm font-semibold text-primary hover:underline">
                    Lihat semua
                </a>
            </div>

            @if ($latestTasks->isEmpty())
                <div class="pt-5">
                    <x-empty-state title="Belum ada tugas" description="Tugas terbaru akan tampil setelah tugas dibuat." />
                </div>
            @else
                <div class="space-y-3 pt-5">
                    @foreach ($latestTasks as $task)
                        <article
                            class="group flex flex-col gap-3 rounded-lg border border-border bg-slate-50/70 p-4 transition hover:border-primary/30 hover:bg-card sm:flex-row sm:items-start sm:justify-between">
                            <div class="flex min-w-0 items-start gap-3">
                                <span class="mt-0.5 flex size-9 shrink-0 items-center justify-center rounded-lg bg-card text-secondary ring-1 ring-border"
                                    aria-hidden="true">
                                    <x-icon name="file-text" class="size-4" />
                                </span>
                                <div class="min-w-0">
                                    <a href="{{ route('admin.tasks.show', $task) }}"
                                        class="font-semibold text-navy transition group-hover:text-primary">
                                        {{ $task->title }}
                                    </a>
                                    <p class="mt-1 text-sm leading-5 text-secondary">
                                        Dibuat {{ $task->created_at->copy()->locale('id')->translatedFormat('d F Y, H:i') }}
                                        oleh {{ $task->creator->name }}
                                    </p>
                                </div>
                            </div>
                            <div class="shrink-0">
                                <x-status-badge :status="$task->status" />
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>

        <section class="ui-surface p-5 sm:p-6">
            <div class="ui-section-heading">
                <div class="flex items-start gap-3">
                    <span class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-danger/10 text-danger"
                        aria-hidden="true">
                        <x-icon name="calendar" class="size-5" />
                    </span>
                    <div class="min-w-0">
                        <h2 class="text-lg font-semibold text-navy">Deadline Terdekat</h2>
                        <p class="mt-1 text-sm text-secondary">Tugas aktif dengan deadline yang akan datang.</p>
                    </div>
                </div>
            </div>

            @if ($nearestDeadlineTasks->isEmpty())
                <div class="pt-5">
                    <x-empty-state title="Tidak ada deadline mendatang"
                        description="Belum ada tugas aktif dengan deadline yang akan datang." />
                </div>
            @else
                <div class="space-y-3 pt-5">
                    @foreach ($nearestDeadlineTasks as $task)
                        <article
                            class="grid gap-3 rounded-lg border border-border bg-slate-50/70 p-4 sm:grid-cols-[1fr_auto] sm:items-start">
                            <div class="flex min-w-0 items-start gap-3">
                                <span class="mt-0.5 flex size-9 shrink-0 items-center justify-center rounded-lg bg-card text-secondary ring-1 ring-border"
                                    aria-hidden="true">
                                    <x-icon name="clock" class="size-4" />
                                </span>
                                <div class="min-w-0">
                                    <a href="{{ route('admin.tasks.show', $task) }}"
                                        class="font-semibold text-navy transition hover:text-primary">
                                        {{ $task->title }}
                                    </a>
                                    <p class="mt-1 text-sm leading-5 text-secondary">
                                        {{ $task->deadline->copy()->locale('id')->translatedFormat('d F Y, H:i') }}
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
            <div class="ui-section-heading flex items-start justify-between gap-4">
                <div class="flex min-w-0 items-start gap-3">
                    <span class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-success/10 text-success"
                        aria-hidden="true">
                        <x-icon name="upload" class="size-5" />
                    </span>
                    <div class="min-w-0">
                        <h2 class="text-lg font-semibold text-navy">Aktivitas Pengumpulan Terbaru</h2>
                        <p class="mt-1 text-sm text-secondary">Lima pengumpulan terakhir dari peserta.</p>
                    </div>
                </div>
                <a href="{{ route('admin.submissions.index') }}"
                    class="inline-flex min-h-11 shrink-0 items-center text-sm font-semibold text-primary hover:underline">
                    Monitoring
                </a>
            </div>

            @if ($latestSubmissions->isEmpty())
                <div class="pt-5">
                    <x-empty-state title="Belum ada pengumpulan"
                        description="Aktivitas peserta akan tampil setelah tugas dikumpulkan." />
                </div>
            @else
                <div class="space-y-3 pt-5">
                    @foreach ($latestSubmissions as $submission)
                        <article
                            class="grid gap-3 rounded-lg border border-border bg-slate-50/70 p-4 sm:grid-cols-[1fr_auto] sm:items-start">
                            <div class="flex min-w-0 items-start gap-3">
                                <span class="mt-0.5 flex size-9 shrink-0 items-center justify-center rounded-lg bg-card text-primary ring-1 ring-border"
                                    aria-hidden="true">
                                    <x-icon name="check-circle" class="size-4" />
                                </span>
                                <div class="min-w-0">
                                    <a href="{{ route('admin.submissions.show', $submission) }}"
                                        class="font-semibold text-navy transition hover:text-primary">
                                        {{ $submission->user->name }}
                                    </a>
                                    <p class="mt-1 truncate text-sm text-secondary">{{ $submission->task->title }}</p>
                                    <p class="mt-1 text-xs text-secondary">
                                        {{ $submission->submitted_at->copy()->locale('id')->translatedFormat('d F Y, H:i') }}
                                    </p>
                                </div>
                            </div>
                            <div class="shrink-0">
                                <x-status-badge :status="$submission->status"
                                    :label="$submission->status === \App\Models\Submission::STATUS_LATE
                                        ? 'Terlambat'
                                        : 'Sudah Mengumpulkan'" />
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>

        <section class="ui-surface p-5 sm:p-6">
            <div class="ui-section-heading">
                <div class="flex items-start gap-3">
                    <span class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary"
                        aria-hidden="true">
                        <x-icon name="chart-line" class="size-5" />
                    </span>
                    <div class="min-w-0">
                        <h2 class="text-lg font-semibold text-navy">Progres Pengumpulan</h2>
                        <p class="mt-1 text-sm text-secondary">Perbandingan peserta yang mengumpulkan pada tugas aktif.</p>
                    </div>
                </div>
            </div>

            @if ($taskProgress->isEmpty())
                <div class="pt-5">
                    <x-empty-state title="Belum ada progres"
                        description="Progres akan tersedia setelah terdapat tugas aktif." />
                </div>
            @else
                <div class="space-y-4 pt-5">
                    @foreach ($taskProgress as $progress)
                        <article class="rounded-lg bg-slate-50/70 p-4">
                            <div class="flex items-start justify-between gap-4">
                                <a href="{{ route('admin.submissions.index', ['task_id' => $progress['task']->id]) }}"
                                    class="min-w-0 font-semibold text-navy transition hover:text-primary">
                                    {{ $progress['task']->title }}
                                </a>
                                <span class="shrink-0 rounded-md bg-primary/10 px-2.5 py-1 text-sm font-bold text-primary">
                                    {{ number_format($progress['percentage'], 1, ',', '.') }}%
                                </span>
                            </div>
                            <div class="mt-4 h-3 overflow-hidden rounded-full bg-slate-200" role="progressbar"
                                aria-label="Progres {{ $progress['task']->title }}"
                                aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $progress['percentage'] }}">
                                <div class="h-full rounded-full bg-primary transition-[width] duration-300"
                                    style="width: {{ min(100, $progress['percentage']) }}%"></div>
                            </div>
                            <div class="mt-3 flex flex-wrap items-center justify-between gap-2 text-xs text-secondary">
                                <span>{{ $progress['submitted_count'] }} dari {{ $statistics['total_participants'] }} peserta</span>
                                <a href="{{ route('admin.submissions.index', ['task_id' => $progress['task']->id]) }}"
                                    class="font-semibold text-primary hover:underline">
                                    Lihat monitoring
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
@endsection
