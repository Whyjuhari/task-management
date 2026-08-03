@extends('layouts.app')

@section('title', $pageTitle)

@section('content')
    <x-page-header title="Detail Pengumpulan Peserta" :description="$task->title">
        <a href="{{ route('admin.submissions.index', ['task_id' => $task->id]) }}"
            class="inline-flex min-h-11 items-center rounded-xl border border-border bg-card px-4 py-2.5 text-sm font-semibold text-navy transition hover:bg-slate-50 focus:outline-none focus:ring-3 focus:ring-primary/15">
            Kembali ke Monitoring
        </a>
    </x-page-header>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_340px]">
        <section class="rounded-2xl border border-border bg-card p-5 shadow-sm sm:p-8">
            <div class="flex flex-col gap-4 border-b border-border pb-5 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <p class="text-sm text-secondary">Peserta</p>
                    <h2 class="mt-1 text-xl font-semibold text-navy">{{ $participant->name }}</h2>
                    <p class="mt-1 text-sm text-secondary">{{ $participant->email }}</p>
                </div>
                <x-status-badge :status="$submission->status"
                    :label="$submission->status === \App\Models\Submission::STATUS_LATE
                        ? 'Terlambat'
                        : 'Sudah Mengumpulkan'" />
            </div>

            <dl class="mt-6 space-y-6">
                <div>
                    <dt class="text-sm text-secondary">Nama file</dt>
                    <dd class="mt-1 break-words text-sm font-semibold text-navy">
                        {{ $submission->original_file_name ?: 'Tidak ada file' }}
                    </dd>
                    @if ($submission->file_path)
                        <a href="{{ route('admin.submissions.download', $submission) }}"
                            class="mt-3 inline-flex min-h-11 items-center rounded-xl border border-primary/25 px-4 py-2.5 text-sm font-semibold text-primary transition hover:bg-primary/5 focus:outline-none focus:ring-3 focus:ring-primary/15">
                            Unduh File
                        </a>
                    @endif
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

        <aside class="h-fit rounded-2xl border border-border bg-card p-5 shadow-sm sm:p-6">
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
                <div>
                    <dt class="text-sm text-secondary">Status tugas</dt>
                    <dd class="mt-2"><x-status-badge :status="$task->status" /></dd>
                </div>
            </dl>
        </aside>
    </div>
@endsection
