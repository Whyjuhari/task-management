@extends('layouts.app')

@section('title', $pageTitle)

@section('content')
    <x-page-header :title="$pageTitle" :description="$description">
        <a href="{{ route('tasks.index') }}"
            class="ui-button ui-button-secondary">
            Lihat Daftar Tugas
        </a>
    </x-page-header>

    <section aria-label="Ringkasan pengumpulan" class="ui-stat-grid mb-6 sm:grid-cols-3">
        <x-stat-item label="Total Pengumpulan" :value="$statistics['total']" icon="submissions" />
        <x-stat-item label="Tepat Waktu" :value="$statistics['submitted']" tone="success" icon="done" />
        <x-stat-item label="Terlambat" :value="$statistics['late']" tone="warning" icon="late" />
    </section>

    @if ($submissions->isEmpty())
        <x-empty-state title="Belum ada pengumpulan"
            description="Tugas yang telah Anda kumpulkan akan ditampilkan pada halaman ini.">
            <a href="{{ route('tasks.index') }}"
                class="ui-button ui-button-primary">
                Lihat Daftar Tugas
            </a>
        </x-empty-state>
    @else
        <section aria-labelledby="submission-history-title">
            <div class="mb-4">
                <h2 id="submission-history-title" class="text-lg font-semibold text-navy">Riwayat Pengumpulan</h2>
                <p class="mt-1 text-sm text-secondary">Urutan berdasarkan waktu pengumpulan terbaru.</p>
            </div>

            <div class="ui-surface hidden overflow-hidden xl:block">
                <div class="overflow-x-auto" tabindex="0" aria-label="Tabel riwayat pengumpulan">
                    <table class="min-w-full divide-y divide-border text-left text-sm">
                        <caption class="sr-only">Riwayat pengumpulan tugas milik peserta yang sedang masuk.</caption>
                        <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wider text-secondary">
                            <tr>
                                <th scope="col" class="px-5 py-4">Tugas</th>
                                <th scope="col" class="px-5 py-4">Waktu Pengumpulan</th>
                                <th scope="col" class="px-5 py-4">Berkas</th>
                                <th scope="col" class="px-5 py-4">Status</th>
                                <th scope="col" class="px-5 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @foreach ($submissions as $submission)
                                <tr class="align-top transition hover:bg-slate-50/70">
                                    <td class="max-w-sm px-5 py-4">
                                        <a href="{{ route('tasks.show', $submission->task) }}"
                                            class="inline-flex min-h-11 items-center font-semibold text-navy hover:text-primary hover:underline">
                                            {{ $submission->task->title }}
                                        </a>
                                        <p class="mt-1 break-words text-xs text-secondary">
                                            {{ $submission->task->category ?: 'Tanpa kategori' }}
                                        </p>
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-4 text-navy">
                                        {{ $submission->submitted_at->copy()->locale('id')->translatedFormat('d F Y, H:i') }}
                                        <p class="mt-1 text-xs text-secondary">
                                            Deadline {{ $submission->task->deadline->copy()->locale('id')->translatedFormat('d F Y, H:i') }}
                                        </p>
                                    </td>
                                    <td class="max-w-xs px-5 py-4 text-navy">
                                        <p class="break-words">{{ $submission->original_file_name ?: 'Tidak ada file' }}</p>
                                        @if ($submission->submission_link)
                                            <a href="{{ $submission->submission_link }}" target="_blank"
                                                rel="noopener noreferrer"
                                                class="mt-1 inline-flex min-h-11 items-center text-sm font-semibold text-primary hover:underline">
                                                Buka tautan
                                            </a>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4">
                                        <x-status-badge :status="$submission->status" />
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('submissions.show', $submission) }}"
                                                class="inline-flex min-h-11 items-center justify-center whitespace-nowrap rounded-lg border border-border bg-card px-3 text-sm font-semibold text-navy transition hover:bg-slate-50 focus:outline-none focus:ring-3 focus:ring-primary/15">
                                                Lihat Detail
                                            </a>
                                            @if ($submission->task->canBeSubmitted())
                                                <a href="{{ route('submissions.edit', $submission) }}"
                                                    class="ui-button ui-button-primary px-3">
                                                    Perbarui
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="grid gap-4 xl:hidden">
                @foreach ($submissions as $submission)
                    <article class="ui-surface p-5">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                            <div class="min-w-0">
                                <a href="{{ route('tasks.show', $submission->task) }}"
                                    class="inline-flex min-h-11 items-center break-words text-base font-semibold text-navy hover:text-primary hover:underline">
                                    {{ $submission->task->title }}
                                </a>
                                <p class="mt-1 text-xs font-semibold uppercase tracking-wider text-secondary">
                                    {{ $submission->task->category ?: 'Tanpa kategori' }}
                                </p>
                            </div>
                            <div class="shrink-0">
                                <x-status-badge :status="$submission->status" />
                            </div>
                        </div>

                        <dl class="mt-5 grid gap-4 border-t border-border pt-4 sm:grid-cols-2">
                            <div>
                                <dt class="text-xs font-medium text-secondary">Dikumpulkan</dt>
                                <dd class="mt-1 text-sm font-semibold text-navy">
                                    {{ $submission->submitted_at->copy()->locale('id')->translatedFormat('d F Y, H:i') }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-secondary">Deadline</dt>
                                <dd class="mt-1 text-sm font-semibold text-navy">
                                    {{ $submission->task->deadline->copy()->locale('id')->translatedFormat('d F Y, H:i') }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-secondary">Nama file</dt>
                                <dd class="mt-1 break-words text-sm font-semibold text-navy">
                                    {{ $submission->original_file_name ?: 'Tidak ada file' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-secondary">Tautan</dt>
                                <dd class="mt-1 text-sm font-semibold">
                                    @if ($submission->submission_link)
                                        <a href="{{ $submission->submission_link }}" target="_blank"
                                            rel="noopener noreferrer"
                                            class="inline-flex min-h-11 items-center text-primary hover:underline">
                                            Buka tautan
                                        </a>
                                    @else
                                        <span class="text-navy">Tidak ada tautan</span>
                                    @endif
                                </dd>
                            </div>
                        </dl>

                        <div class="mt-5 flex flex-col gap-2 border-t border-border pt-4 sm:flex-row sm:justify-end">
                            <a href="{{ route('submissions.show', $submission) }}"
                                class="inline-flex min-h-11 items-center justify-center rounded-lg border border-border bg-card px-4 text-sm font-semibold text-navy transition hover:bg-slate-50 focus:outline-none focus:ring-3 focus:ring-primary/15">
                                Lihat Detail
                            </a>
                            @if ($submission->task->canBeSubmitted())
                                <a href="{{ route('submissions.edit', $submission) }}"
                                    class="ui-button ui-button-primary px-4">
                                    Perbarui Pengumpulan
                                </a>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $submissions->links('components.pagination') }}
            </div>
        </section>
    @endif
@endsection
