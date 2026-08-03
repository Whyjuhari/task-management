@extends('layouts.app')

@section('title', $pageTitle)

@section('content')
    <x-page-header :title="$pageTitle"
        :description="'Halo, '.$participant->name.'. '.$description" />

    <section aria-label="Statistik dasbor peserta" class="ui-stat-grid mb-6 sm:grid-cols-2 xl:grid-cols-5">
        <x-stat-item label="Total Tugas Aktif" :value="$statistics['total_active_tasks']" />
        <x-stat-item label="Sudah Dikumpulkan" :value="$statistics['submitted']" tone="success" />
        <x-stat-item label="Belum Dikumpulkan" :value="$statistics['not_submitted']" />
        <x-stat-item label="Terlambat" :value="$statistics['late']" tone="warning" />
        <x-stat-item label="Deadline Terdekat"
            :value="$nearestDeadlineTask
                ? $nearestDeadlineTask->deadline->copy()->locale('id')->translatedFormat('d M Y, H:i')
                : 'Tidak ada'"
            tone="primary" :description="$nearestDeadlineTask?->title" />
    </section>

    <section class="ui-surface mb-6 p-5 sm:p-6">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-navy">Progres Tugas Aktif</h2>
                <p class="mt-1 text-sm text-secondary">Tugas aktif yang sudah Anda kumpulkan, termasuk yang terlambat.</p>
            </div>
            <p class="text-2xl font-bold text-primary">
                {{ number_format($statistics['completion_percentage'], 1, ',', '.') }}%
            </p>
        </div>
        <div class="mt-4 h-3 overflow-hidden rounded-full bg-slate-100" role="progressbar"
            aria-label="Progres tugas aktif" aria-valuemin="0" aria-valuemax="100"
            aria-valuenow="{{ $statistics['completion_percentage'] }}">
            <div class="h-full rounded-full bg-primary transition-[width] duration-300"
                style="width: {{ min(100, $statistics['completion_percentage']) }}%"></div>
        </div>
        <p class="mt-2 text-xs text-secondary">
            {{ $statistics['submitted'] + $statistics['late'] }} dari {{ $statistics['total_active_tasks'] }} tugas aktif
        </p>
    </section>

    <div class="grid gap-6 xl:grid-cols-2">
        <section class="ui-surface p-5 sm:p-6">
            <div class="ui-section-heading flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-navy">Perlu Segera Dikerjakan</h2>
                    <p class="mt-1 text-sm text-secondary">Tugas aktif yang belum Anda kumpulkan.</p>
                </div>
                <a href="{{ route('tasks.index') }}" class="inline-flex min-h-11 items-center text-sm font-semibold text-primary hover:underline">
                    Daftar tugas
                </a>
            </div>

            @if ($urgentTasks->isEmpty())
                <div class="pt-5">
                    <x-empty-state title="Tidak ada tugas mendesak"
                        description="Semua tugas aktif yang tersedia sudah Anda kumpulkan." />
                </div>
            @else
                <div class="divide-y divide-border">
                    @foreach ($urgentTasks as $task)
                        <article class="py-4">
                            <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                <div class="min-w-0">
                                    <a href="{{ route('tasks.show', $task) }}"
                                        class="font-semibold text-navy transition hover:text-primary">
                                        {{ $task->title }}
                                    </a>
                                    <p class="mt-1 text-sm text-secondary">{{ $task->category ?: 'Tanpa kategori' }}</p>
                                </div>
                                <div class="flex shrink-0 flex-col items-start gap-1 sm:items-end">
                                    <x-deadline-indicator :deadline="$task->deadline" />
                                    <p class="text-xs font-semibold text-secondary">{{ $task->remainingTime() }}</p>
                                </div>
                            </div>
                            <p class="mt-2 text-xs text-secondary">
                                Deadline {{ $task->deadline->copy()->locale('id')->translatedFormat('d F Y, H:i') }}
                            </p>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>

        <section class="ui-surface p-5 sm:p-6">
            <div class="ui-section-heading">
                <h2 class="text-lg font-semibold text-navy">Pengumpulan Terbaru Anda</h2>
                <p class="mt-1 text-sm text-secondary">Lima pengumpulan yang terakhir diperbarui.</p>
            </div>

            @if ($latestSubmissions->isEmpty())
                <div class="pt-5">
                    <x-empty-state title="Belum ada pengumpulan"
                        description="Pengumpulan terbaru akan tampil setelah Anda mengirim tugas." />
                </div>
            @else
                <div class="divide-y divide-border">
                    @foreach ($latestSubmissions as $submission)
                        <article class="flex flex-col gap-3 py-4 sm:flex-row sm:items-center sm:justify-between">
                            <div class="min-w-0">
                                <a href="{{ route('submissions.show', $submission) }}"
                                    class="font-semibold text-navy transition hover:text-primary">
                                    {{ $submission->task->title }}
                                </a>
                                <p class="mt-1 text-sm text-secondary">
                                    {{ $submission->submitted_at->copy()->locale('id')->translatedFormat('d F Y, H:i') }}
                                </p>
                            </div>
                            <x-status-badge :status="$submission->status" />
                        </article>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
@endsection
