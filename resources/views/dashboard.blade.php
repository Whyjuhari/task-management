@extends('layouts.app')

@section('title', $pageTitle)

@section('content')
    <x-page-header :title="$pageTitle" :description="$description" />

    <section class="mb-6 overflow-hidden rounded-xl border border-border bg-card shadow-sm">
        <div class="bg-linear-to-r from-primary/10 via-primary/5 to-transparent px-5 py-6 sm:px-8 sm:py-8">
            <p class="text-sm font-semibold text-primary">Selamat datang kembali</p>
            <h2 class="mt-2 text-2xl font-semibold tracking-tight text-navy">Halo, {{ $participant->name }}</h2>
            <p class="mt-3 max-w-2xl text-sm leading-6 text-secondary sm:text-base">
                Lihat tugas yang perlu diselesaikan dan perkembangan pengumpulan Anda.
            </p>
        </div>
    </section>

    <section aria-label="Statistik dasbor peserta" class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
        <article class="rounded-xl border border-border bg-card p-5 shadow-sm">
            <p class="text-sm font-medium text-secondary">Total Tugas Aktif</p>
            <p class="mt-2 text-3xl font-bold text-navy">{{ $statistics['total_active_tasks'] }}</p>
        </article>
        <article class="rounded-xl border border-success/20 bg-card p-5 shadow-sm">
            <p class="text-sm font-medium text-secondary">Sudah Dikumpulkan</p>
            <p class="mt-2 text-3xl font-bold text-success">{{ $statistics['submitted'] }}</p>
        </article>
        <article class="rounded-xl border border-border bg-card p-5 shadow-sm">
            <p class="text-sm font-medium text-secondary">Belum Dikumpulkan</p>
            <p class="mt-2 text-3xl font-bold text-secondary">{{ $statistics['not_submitted'] }}</p>
        </article>
        <article class="rounded-xl border border-warning/25 bg-card p-5 shadow-sm">
            <p class="text-sm font-medium text-secondary">Terlambat</p>
            <p class="mt-2 text-3xl font-bold text-amber-700">{{ $statistics['late'] }}</p>
        </article>
        <article class="rounded-xl border border-primary/20 bg-card p-5 shadow-sm">
            <p class="text-sm font-medium text-secondary">Deadline Terdekat</p>
            @if ($nearestDeadlineTask)
                <p class="mt-2 text-base font-bold text-primary">
                    {{ $nearestDeadlineTask->deadline->copy()->locale('id')->translatedFormat('d F Y, H:i') }}
                </p>
                <p class="mt-1 line-clamp-2 text-xs text-secondary">{{ $nearestDeadlineTask->title }}</p>
            @else
                <p class="mt-2 text-xl font-bold text-secondary">Tidak ada</p>
            @endif
        </article>
    </section>

    <section class="mb-6 rounded-xl border border-border bg-card p-5 shadow-sm sm:p-6">
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
            <div class="h-full rounded-full bg-primary transition-all"
                style="width: {{ min(100, $statistics['completion_percentage']) }}%"></div>
        </div>
        <p class="mt-2 text-xs text-secondary">
            {{ $statistics['submitted'] + $statistics['late'] }} dari {{ $statistics['total_active_tasks'] }} tugas aktif
        </p>
    </section>

    <div class="grid gap-6 xl:grid-cols-2">
        <section class="rounded-xl border border-border bg-card p-5 shadow-sm sm:p-6">
            <div class="flex items-center justify-between gap-4 border-b border-border pb-4">
                <div>
                    <h2 class="text-lg font-semibold text-navy">Perlu Segera Dikerjakan</h2>
                    <p class="mt-1 text-sm text-secondary">Tugas aktif yang belum Anda kumpulkan.</p>
                </div>
                <a href="{{ route('tasks.index') }}" class="text-sm font-semibold text-primary hover:underline">
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

        <section class="rounded-xl border border-border bg-card p-5 shadow-sm sm:p-6">
            <div class="border-b border-border pb-4">
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
