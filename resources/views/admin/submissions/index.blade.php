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
            class="mb-6 grid gap-4 rounded-2xl border border-border bg-card p-4 shadow-sm sm:p-5 md:grid-cols-[minmax(0,1fr)_auto] md:items-end">
            <div>
                <label for="task_id" class="mb-2 block text-sm font-semibold text-navy">Pilih tugas</label>
                <select id="task_id" name="task_id" required
                    class="min-h-11 w-full rounded-xl border border-border bg-card px-4 py-3 text-sm text-navy outline-none transition focus:border-primary focus:ring-3 focus:ring-primary/15">
                    <option value="">Pilih tugas yang akan dipantau</option>
                    @foreach ($tasks as $task)
                        <option value="{{ $task->id }}" @selected($selectedTask?->is($task))>
                            {{ $task->title }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit"
                class="min-h-11 cursor-pointer rounded-xl bg-primary px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-navy focus:outline-none focus:ring-3 focus:ring-primary/30">
                Tampilkan Monitoring
            </button>
        </form>

        @if ($selectedTask === null)
            <x-empty-state title="Pilih tugas untuk dipantau"
                description="Ringkasan dan data peserta akan tampil setelah satu tugas dipilih." />
        @else
            <section class="mb-6 rounded-2xl border border-border bg-card p-5 shadow-sm sm:p-6">
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
                            class="inline-flex min-h-11 items-center justify-center rounded-xl bg-success px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-green-700 focus:outline-none focus:ring-3 focus:ring-success/25">
                            Export CSV
                        </a>
                    </div>
                </div>
            </section>

            <section aria-label="Ringkasan pengumpulan" class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
                <article class="rounded-2xl border border-border bg-card p-5 shadow-sm">
                    <p class="text-sm font-medium text-secondary">Total peserta</p>
                    <p class="mt-2 text-3xl font-bold text-navy">{{ $summary['total'] }}</p>
                </article>
                <article class="rounded-2xl border border-success/20 bg-card p-5 shadow-sm">
                    <p class="text-sm font-medium text-secondary">Sudah mengumpulkan</p>
                    <p class="mt-2 text-3xl font-bold text-success">{{ $summary['submitted'] }}</p>
                </article>
                <article class="rounded-2xl border border-border bg-card p-5 shadow-sm">
                    <p class="text-sm font-medium text-secondary">Belum mengumpulkan</p>
                    <p class="mt-2 text-3xl font-bold text-secondary">{{ $summary['not_submitted'] }}</p>
                </article>
                <article class="rounded-2xl border border-warning/25 bg-card p-5 shadow-sm">
                    <p class="text-sm font-medium text-secondary">Terlambat</p>
                    <p class="mt-2 text-3xl font-bold text-amber-700">{{ $summary['late'] }}</p>
                </article>
                <article class="rounded-2xl border border-primary/20 bg-card p-5 shadow-sm">
                    <p class="text-sm font-medium text-secondary">Persentase pengumpulan</p>
                    <p class="mt-2 text-3xl font-bold text-primary">
                        {{ number_format($summary['percentage'], 1, ',', '.') }}%
                    </p>
                </article>
            </section>

            <form method="GET" action="{{ route('admin.submissions.index') }}"
                class="mb-6 grid gap-4 rounded-2xl border border-border bg-card p-4 shadow-sm sm:p-5 md:grid-cols-[minmax(0,1fr)_220px_auto] md:items-end">
                <input type="hidden" name="task_id" value="{{ $selectedTask->id }}">

                <div>
                    <label for="search" class="mb-2 block text-sm font-semibold text-navy">Cari peserta</label>
                    <input id="search" name="search" type="search" value="{{ $search }}"
                        placeholder="Nama atau email peserta"
                        class="min-h-11 w-full rounded-xl border border-border bg-card px-4 py-3 text-sm text-navy outline-none transition placeholder:text-slate-400 focus:border-primary focus:ring-3 focus:ring-primary/15">
                </div>

                <div>
                    <label for="status" class="mb-2 block text-sm font-semibold text-navy">Filter status</label>
                    <select id="status" name="status"
                        class="min-h-11 w-full rounded-xl border border-border bg-card px-4 py-3 text-sm text-navy outline-none transition focus:border-primary focus:ring-3 focus:ring-primary/15">
                        <option value="">Semua status</option>
                        <option value="submitted" @selected($status === 'submitted')>Sudah Mengumpulkan</option>
                        <option value="not_submitted" @selected($status === 'not_submitted')>Belum Mengumpulkan</option>
                        <option value="late" @selected($status === 'late')>Terlambat</option>
                    </select>
                </div>

                <div class="flex flex-wrap gap-2">
                    <button type="submit"
                        class="min-h-11 cursor-pointer rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-navy focus:outline-none focus:ring-3 focus:ring-primary/30">
                        Terapkan
                    </button>
                    @if ($search !== '' || $status !== null)
                        <a href="{{ route('admin.submissions.index', ['task_id' => $selectedTask->id]) }}"
                            class="inline-flex min-h-11 items-center rounded-xl border border-border px-4 py-2.5 text-sm font-semibold text-navy transition hover:bg-slate-50 focus:outline-none focus:ring-3 focus:ring-primary/15">
                            Reset
                        </a>
                    @endif
                </div>
            </form>

            @if ($participants->isEmpty())
                <x-empty-state :title="$search !== '' || $status !== null ? 'Peserta tidak ditemukan' : 'Belum ada peserta'"
                    :description="$search !== '' || $status !== null
                        ? 'Coba ubah kata pencarian atau filter status yang digunakan.'
                        : 'Belum ada pengguna dengan role peserta yang dapat ditampilkan.'" />
            @else
                <div class="hidden overflow-hidden rounded-2xl border border-border bg-card shadow-sm md:block">
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[1420px] text-left text-sm">
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

                <div class="space-y-4 md:hidden">
                    @foreach ($participants as $participant)
                        @php($submission = $participant->submissions->first())
                        <article class="rounded-2xl border border-border bg-card p-5 shadow-sm">
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
                    {{ $participants->links() }}
                </div>
            @endif
        @endif
    @endif
@endsection
