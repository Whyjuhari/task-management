@extends('layouts.app')

@section('title', $pageTitle)

@section('content')
    <x-page-header title="Monitoring Pengumpulan"
        description="Pilih tugas untuk memantau status pengumpulan seluruh peserta." />

    @if ($errors->any())
        <div role="alert" class="mb-6 rounded-xl border border-danger/20 bg-danger/5 px-4 py-3 text-sm text-danger">
            {{ $errors->first() }}
        </div>
    @endif

    @if ($tasks->isEmpty())
        <x-empty-state title="Belum ada tugas"
            description="Buat tugas terlebih dahulu sebelum membuka monitoring pengumpulan." />
    @else
        <form method="GET" action="{{ route('admin.submissions.index') }}"
            class="ui-filter-panel mb-6 grid gap-4 p-4 sm:p-5 md:grid-cols-[minmax(0,1fr)_auto] md:items-end">
            <div>
                <label for="task_id" class="ui-label">Pilih tugas</label>
                <select id="task_id" name="task_id" required
                    class="ui-control">
                    <option value="">Pilih tugas yang akan dipantau</option>
                    @foreach ($tasks as $task)
                        <option value="{{ $task->id }}" @selected($selectedTask?->is($task))>
                            {{ $task->title }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit"
                class="ui-button ui-button-primary px-5">
                Tampilkan Monitoring
            </button>
        </form>

        @if ($selectedTask === null)
            <x-empty-state title="Pilih tugas untuk dipantau"
                description="Ringkasan dan data peserta akan tampil setelah satu tugas dipilih." />
        @else
            <section class="ui-surface mb-6 p-5 sm:p-6">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-secondary">Tugas terpilih</p>
                        <h2 class="mt-1 text-xl font-semibold text-navy">{{ $selectedTask->title }}</h2>
                        <p class="mt-1 text-sm text-secondary">
                            Deadline:
                            {{ $selectedTask->deadline->copy()->locale('id')->translatedFormat('d F Y, H:i') }}
                        </p>
                        <div class="mt-3">
                            <x-deadline-indicator :deadline="$selectedTask->deadline" />
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        <x-status-badge :status="$selectedTask->status" />
                        <a href="{{ route('admin.submissions.export', array_filter([
                            'task_id' => $selectedTask->id,
                            'search' => $search,
                            'status' => $status,
                        ], fn ($value) => $value !== null && $value !== '')) }}"
                            class="ui-button bg-success-strong text-white hover:bg-success focus:ring-success/25">
                            Ekspor CSV
                        </a>
                    </div>
                </div>
            </section>

            <section aria-label="Ringkasan pengumpulan" class="ui-stat-grid mb-6 sm:grid-cols-2 xl:grid-cols-5">
                <x-stat-item label="Total peserta" :value="$summary['total']" />
                <x-stat-item label="Sudah mengumpulkan" :value="$summary['submitted']" tone="success" />
                <x-stat-item label="Belum mengumpulkan" :value="$summary['not_submitted']" />
                <x-stat-item label="Terlambat" :value="$summary['late']" tone="warning" />
                <x-stat-item label="Persentase pengumpulan"
                    :value="number_format($summary['percentage'], 1, ',', '.').'%'" tone="primary" />
            </section>

            <form method="GET" action="{{ route('admin.submissions.index') }}"
                class="ui-filter-panel mb-6 grid gap-4 p-4 sm:p-5 md:grid-cols-[minmax(0,1fr)_220px_auto] md:items-end">
                <input type="hidden" name="task_id" value="{{ $selectedTask->id }}">

                <div>
                    <label for="search" class="ui-label">Cari peserta</label>
                    <input id="search" name="search" type="search" value="{{ $search }}"
                        placeholder="Nama atau email peserta"
                        class="ui-control">
                </div>

                <div>
                    <label for="status" class="ui-label">Filter status</label>
                    <select id="status" name="status"
                        class="ui-control">
                        <option value="">Semua status</option>
                        <option value="submitted" @selected($status === 'submitted')>Sudah Mengumpulkan</option>
                        <option value="not_submitted" @selected($status === 'not_submitted')>Belum Mengumpulkan</option>
                        <option value="late" @selected($status === 'late')>Terlambat</option>
                    </select>
                </div>

                <div class="flex flex-wrap gap-2">
                    <button type="submit"
                        class="ui-button ui-button-primary">
                        Terapkan
                    </button>
                    @if ($search !== '' || $status !== null)
                        <a href="{{ route('admin.submissions.index', ['task_id' => $selectedTask->id]) }}"
                            class="ui-button ui-button-secondary">
                            Reset
                        </a>
                    @endif
                </div>
            </form>

            @if ($participants->isEmpty())
                <x-empty-state :title="$search !== '' || $status !== null ? 'Peserta tidak ditemukan' : 'Belum ada peserta'"
                    :description="$search !== '' || $status !== null
                        ? 'Coba ubah kata pencarian atau filter status yang digunakan.'
                        : 'Belum ada pengguna dengan peran peserta yang dapat ditampilkan.'" />
            @else
                <p class="mb-2 hidden text-xs text-secondary xl:block 2xl:hidden">
                    Geser tabel secara horizontal untuk melihat seluruh informasi pengumpulan.
                </p>
                <div class="ui-surface hidden overflow-hidden xl:block">
                    <div class="overflow-x-auto" tabindex="0"
                        aria-label="Tabel monitoring pengumpulan dapat digeser secara horizontal">
                        <table class="w-full min-w-[1180px] text-left text-sm">
                            <caption class="sr-only">Monitoring pengumpulan seluruh peserta</caption>
                            <thead class="border-b border-border bg-slate-50 text-xs uppercase tracking-wider text-secondary">
                                <tr>
                                    <th scope="col" class="px-4 py-4 font-semibold">Nama</th>
                                    <th scope="col" class="px-4 py-4 font-semibold">Email</th>
                                    <th scope="col" class="px-4 py-4 font-semibold">Status</th>
                                    <th scope="col" class="px-4 py-4 font-semibold">Waktu pengumpulan</th>
                                    <th scope="col" class="px-4 py-4 font-semibold">Nama file</th>
                                    <th scope="col" class="px-4 py-4 font-semibold">Tautan</th>
                                    <th scope="col" class="px-4 py-4 font-semibold">Catatan</th>
                                    <th scope="col" class="px-4 py-4 text-right font-semibold">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border">
                                @foreach ($participants as $participant)
                                    @php($submission = $participant->submissions->first())
                                    <tr class="align-top transition hover:bg-slate-50/70">
                                        <td class="whitespace-nowrap px-4 py-4 font-semibold text-navy">{{ $participant->name }}</td>
                                        <td class="whitespace-nowrap px-4 py-4 text-secondary">{{ $participant->email }}</td>
                                        <td class="whitespace-nowrap px-4 py-4">
                                            @if ($submission)
                                                <x-status-badge :status="$submission->status"
                                                    :label="$submission->status === \App\Models\Submission::STATUS_LATE
                                                        ? 'Terlambat'
                                                        : 'Sudah Mengumpulkan'" />
                                            @else
                                                <x-status-badge status="not_submitted" label="Belum Mengumpulkan" />
                                            @endif
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-4 text-secondary">
                                            {{ $submission?->submitted_at?->copy()->locale('id')->translatedFormat('d F Y, H:i') ?? '-' }}
                                        </td>
                                        <td class="max-w-52 break-words px-4 py-4 text-navy">
                                            {{ $submission?->original_file_name ?? '-' }}
                                        </td>
                                        <td class="max-w-52 break-all px-4 py-4">
                                            @if ($submission?->submission_link)
                                                <a href="{{ $submission->submission_link }}" target="_blank" rel="noopener noreferrer"
                                                    class="font-semibold text-primary underline decoration-primary/30 underline-offset-4">
                                                    Buka tautan
                                                </a>
                                            @else
                                                <span class="text-secondary">-</span>
                                            @endif
                                        </td>
                                        <td class="max-w-64 break-words px-4 py-4 text-secondary">
                                            {{ $submission?->note ?? '-' }}
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="flex justify-end gap-2">
                                                @if ($submission)
                                                    <a href="{{ route('admin.submissions.show', $submission) }}"
                                                        class="inline-flex min-h-11 items-center rounded-lg border border-border px-3 py-2 font-semibold text-navy transition hover:bg-slate-100">
                                                        Detail
                                                    </a>
                                                    @if ($submission->file_path)
                                                        <a href="{{ route('admin.submissions.download', $submission) }}"
                                                            class="inline-flex min-h-11 items-center rounded-lg border border-primary/25 px-3 py-2 font-semibold text-primary transition hover:bg-primary/5">
                                                            Unduh
                                                        </a>
                                                    @endif
                                                @else
                                                    <span class="inline-flex min-h-11 items-center text-secondary">-</span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="space-y-4 xl:hidden">
                    @foreach ($participants as $participant)
                        @php($submission = $participant->submissions->first())
                        <article class="ui-surface p-5">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <h3 class="font-semibold text-navy">{{ $participant->name }}</h3>
                                    <p class="mt-1 break-all text-sm text-secondary">{{ $participant->email }}</p>
                                </div>
                                @if ($submission)
                                    <x-status-badge :status="$submission->status"
                                        :label="$submission->status === \App\Models\Submission::STATUS_LATE
                                            ? 'Terlambat'
                                            : 'Sudah Mengumpulkan'" />
                                @else
                                    <x-status-badge status="not_submitted" label="Belum Mengumpulkan" />
                                @endif
                            </div>

                            <dl class="mt-4 space-y-3 border-y border-border py-4 text-sm">
                                <div>
                                    <dt class="text-secondary">Waktu pengumpulan</dt>
                                    <dd class="mt-1 font-semibold text-navy">
                                        {{ $submission?->submitted_at?->copy()->locale('id')->translatedFormat('d F Y, H:i') ?? '-' }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-secondary">Nama file</dt>
                                    <dd class="mt-1 break-words font-semibold text-navy">
                                        {{ $submission?->original_file_name ?? '-' }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-secondary">Tautan</dt>
                                    <dd class="mt-1 break-all">
                                        @if ($submission?->submission_link)
                                            <a href="{{ $submission->submission_link }}" target="_blank" rel="noopener noreferrer"
                                                class="font-semibold text-primary underline decoration-primary/30 underline-offset-4">
                                                Buka tautan
                                            </a>
                                        @else
                                            <span class="text-secondary">-</span>
                                        @endif
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-secondary">Catatan</dt>
                                    <dd class="mt-1 whitespace-pre-line text-navy">{{ $submission?->note ?? '-' }}</dd>
                                </div>
                            </dl>

                            @if ($submission)
                                <div class="mt-4 grid gap-2 sm:grid-cols-2">
                                    <a href="{{ route('admin.submissions.show', $submission) }}"
                                        class="inline-flex min-h-11 items-center justify-center rounded-lg border border-border px-3 py-2 text-sm font-semibold text-navy">
                                        Detail
                                    </a>
                                    @if ($submission->file_path)
                                        <a href="{{ route('admin.submissions.download', $submission) }}"
                                            class="inline-flex min-h-11 items-center justify-center rounded-lg border border-primary/25 px-3 py-2 text-sm font-semibold text-primary">
                                            Unduh File
                                        </a>
                                    @endif
                                </div>
                            @endif
                        </article>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $participants->links('components.pagination') }}
                </div>
            @endif
        @endif
    @endif
@endsection
