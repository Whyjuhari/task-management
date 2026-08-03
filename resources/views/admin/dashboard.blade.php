@extends('layouts.app')

@section('title', $pageTitle)

@section('content')
    <x-page-header :title="$pageTitle"
        :description="'Selamat datang, '.auth()->user()->name.'. '.$description" />

    <section aria-label="Statistik dasbor admin"
        class="ui-stat-grid mb-6 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-6">
        <x-stat-item label="Total Tugas" :value="$statistics['total_tasks']" />
        <x-stat-item label="Tugas Aktif" :value="$statistics['active_tasks']" tone="primary" />
        <x-stat-item label="Total Peserta" :value="$statistics['total_participants']" />
        <x-stat-item label="Total Pengumpulan" :value="$statistics['total_submissions']" tone="success" />
        <x-stat-item label="Pengumpulan Terlambat" :value="$statistics['late_submissions']" tone="warning" />
        <x-stat-item label="Mendekati Deadline" :value="$statistics['near_deadline_tasks']" tone="danger" />
    </section>

    <div class="grid gap-6 xl:grid-cols-2">
        <section class="ui-surface p-5 sm:p-6">
            <div class="ui-section-heading flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-navy">Tugas Terbaru</h2>
                    <p class="mt-1 text-sm text-secondary">Lima tugas yang terakhir dibuat.</p>
                </div>
                <a href="{{ route('admin.tasks.index') }}" class="inline-flex min-h-11 items-center text-sm font-semibold text-primary hover:underline">
                    Lihat semua
                </a>
            </div>

            @if ($latestTasks->isEmpty())
                <div class="pt-5">
                    <x-empty-state title="Belum ada tugas" description="Tugas terbaru akan tampil setelah tugas dibuat." />
                </div>
            @else
                <div class="divide-y divide-border">
                    @foreach ($latestTasks as $task)
                        <article class="flex flex-col gap-3 py-4 sm:flex-row sm:items-center sm:justify-between">
                            <div class="min-w-0">
                                <a href="{{ route('admin.tasks.show', $task) }}"
                                    class="font-semibold text-navy transition hover:text-primary">
                                    {{ $task->title }}
                                </a>
                                <p class="mt-1 text-sm text-secondary">
                                    Dibuat {{ $task->created_at->copy()->locale('id')->translatedFormat('d F Y, H:i') }}
                                    oleh {{ $task->creator->name }}
                                </p>
                            </div>
                            <x-status-badge :status="$task->status" />
                        </article>
                    @endforeach
                </div>
            @endif
        </section>

        <section class="ui-surface p-5 sm:p-6">
            <div class="ui-section-heading">
                <h2 class="text-lg font-semibold text-navy">Deadline Terdekat</h2>
                <p class="mt-1 text-sm text-secondary">Tugas aktif dengan deadline yang akan datang.</p>
            </div>

            @if ($nearestDeadlineTasks->isEmpty())
                <div class="pt-5">
                    <x-empty-state title="Tidak ada deadline mendatang"
                        description="Belum ada tugas aktif dengan deadline yang akan datang." />
                </div>
            @else
                <div class="divide-y divide-border">
                    @foreach ($nearestDeadlineTasks as $task)
                        <article class="flex flex-col gap-3 py-4 sm:flex-row sm:items-center sm:justify-between">
                            <div class="min-w-0">
                                <a href="{{ route('admin.tasks.show', $task) }}"
                                    class="font-semibold text-navy transition hover:text-primary">
                                    {{ $task->title }}
                                </a>
                                <p class="mt-1 text-sm text-secondary">
                                    {{ $task->deadline->copy()->locale('id')->translatedFormat('d F Y, H:i') }}
                                </p>
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
            <div class="ui-section-heading flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-navy">Aktivitas Pengumpulan Terbaru</h2>
                    <p class="mt-1 text-sm text-secondary">Lima pengumpulan terakhir dari peserta.</p>
                </div>
                <a href="{{ route('admin.submissions.index') }}"
                    class="inline-flex min-h-11 items-center text-sm font-semibold text-primary hover:underline">
                    Monitoring
                </a>
            </div>

            @if ($latestSubmissions->isEmpty())
                <div class="pt-5">
                    <x-empty-state title="Belum ada pengumpulan"
                        description="Aktivitas peserta akan tampil setelah tugas dikumpulkan." />
                </div>
            @else
                <div class="divide-y divide-border">
                    @foreach ($latestSubmissions as $submission)
                        <article class="flex flex-col gap-3 py-4 sm:flex-row sm:items-center sm:justify-between">
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
                            <x-status-badge :status="$submission->status"
                                :label="$submission->status === \App\Models\Submission::STATUS_LATE
                                    ? 'Terlambat'
                                    : 'Sudah Mengumpulkan'" />
                        </article>
                    @endforeach
                </div>
            @endif
        </section>

        <section class="ui-surface p-5 sm:p-6">
            <div class="ui-section-heading">
                <h2 class="text-lg font-semibold text-navy">Progres Pengumpulan</h2>
                <p class="mt-1 text-sm text-secondary">Perbandingan peserta yang mengumpulkan pada tugas aktif.</p>
            </div>

            @if ($taskProgress->isEmpty())
                <div class="pt-5">
                    <x-empty-state title="Belum ada progres"
                        description="Progres akan tersedia setelah terdapat tugas aktif." />
                </div>
            @else
                <div class="space-y-5 pt-5">
                    @foreach ($taskProgress as $progress)
                        <article>
                            <div class="flex items-start justify-between gap-4">
                                <a href="{{ route('admin.submissions.index', ['task_id' => $progress['task']->id]) }}"
                                    class="min-w-0 font-semibold text-navy transition hover:text-primary">
                                    {{ $progress['task']->title }}
                                </a>
                                <span class="shrink-0 text-sm font-semibold text-primary">
                                    {{ number_format($progress['percentage'], 1, ',', '.') }}%
                                </span>
                            </div>
                            <div class="mt-3 h-2.5 overflow-hidden rounded-full bg-slate-100" role="progressbar"
                                aria-label="Progres {{ $progress['task']->title }}"
                                aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $progress['percentage'] }}">
                                <div class="h-full rounded-full bg-primary transition-[width] duration-300"
                                    style="width: {{ min(100, $progress['percentage']) }}%"></div>
                            </div>
                            <p class="mt-2 text-xs text-secondary">
                                {{ $progress['submitted_count'] }} dari {{ $statistics['total_participants'] }} peserta
                            </p>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
@endsection
