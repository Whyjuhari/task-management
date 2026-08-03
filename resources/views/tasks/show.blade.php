@extends('layouts.app')

@section('title', $pageTitle)

@php
    $participant = auth()->user();
    $submission = $task->submissionFor($participant);
    $personalStatus = $task->personalStatusFor($participant);
    $submissionTypeLabels = [
        'file' => 'File',
        'link' => 'Tautan',
        'file_or_link' => 'File atau tautan',
    ];
@endphp

@section('content')
    <x-page-header :title="$task->title" description="Informasi lengkap tugas pelatihan.">
        <a href="{{ route('tasks.index') }}"
            class="inline-flex min-h-11 items-center rounded-xl border border-border bg-card px-4 py-2.5 text-sm font-semibold text-navy transition hover:bg-slate-50 focus:outline-none focus:ring-3 focus:ring-primary/15">
            Kembali ke Daftar
        </a>
    </x-page-header>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_340px]">
        <div class="space-y-6">
            <section class="rounded-2xl border border-border bg-card p-5 shadow-sm sm:p-8">
                <div class="flex flex-wrap gap-2 border-b border-border pb-5">
                    <x-status-badge :status="$task->status" />
                    <x-status-badge :status="$personalStatus" />
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
                            {{ $task->start_date?->copy()->locale('id')->translatedFormat('d F Y, H:i') ?? 'Tidak ditentukan' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm text-secondary">Deadline</dt>
                        <dd class="mt-1 text-sm font-semibold text-navy">
                            {{ $task->deadline->copy()->locale('id')->translatedFormat('d F Y, H:i') }}
                        </dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-sm text-secondary">Sisa waktu</dt>
                        <dd @class([
                            'mt-1 text-sm font-semibold',
                            'text-danger' => !$task->canBeSubmitted(),
                            'text-primary' => $task->canBeSubmitted(),
                        ])>
                            {{ $task->remainingTime() }}
                        </dd>
                    </div>
                </dl>
            </section>

            <section class="rounded-2xl border border-border bg-card p-5 shadow-sm sm:p-8">
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

        <aside class="h-fit rounded-2xl border border-border bg-card p-5 shadow-sm sm:p-6">
            <p class="text-xs font-semibold uppercase tracking-wider text-secondary">Status pengumpulan Anda</p>
            <div class="mt-3">
                <x-status-badge :status="$personalStatus" />
            </div>

            @if ($submission)
                <p class="mt-4 text-sm leading-6 text-secondary">
                    Tercatat pada
                    <span class="font-semibold text-navy">
                        {{ $submission->submitted_at->copy()->locale('id')->translatedFormat('d F Y, H:i') }}
                    </span>.
                </p>
            @endif

            <div class="mt-5 border-t border-border pt-5">
                <p class="text-sm font-semibold text-navy">{{ $task->remainingTime() }}</p>

                <button type="button" disabled
                    class="mt-4 min-h-11 w-full cursor-not-allowed rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white opacity-60">
                    Kumpulkan Tugas
                </button>

                <p class="mt-3 text-xs leading-5 text-secondary">
                    @if ($submission)
                        Pengumpulan untuk tugas ini sudah tercatat.
                    @elseif ($task->status === \App\Models\Task::STATUS_CLOSED)
                        Tugas telah ditutup dan tidak dapat dikumpulkan.
                    @elseif ($task->deadline->isPast())
                        Deadline telah berakhir dan tugas tidak dapat dikumpulkan.
                    @elseif ($task->start_date?->isFuture())
                        Tugas belum memasuki tanggal mulai.
                    @else
                        Form pengumpulan akan tersedia pada tahap berikutnya.
                    @endif
                </p>
            </div>
        </aside>
    </div>
@endsection
