<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminSubmissionController extends Controller
{
    private const STATUS_NOT_SUBMITTED = 'not_submitted';

    public function index(Request $request): View
    {
        $filters = $request->validate(
            [
                'task_id' => ['nullable', 'integer', Rule::exists('tasks', 'id')],
                'search' => ['nullable', 'string', 'max:255'],
                'status' => [
                    'nullable',
                    Rule::in([
                        Submission::STATUS_SUBMITTED,
                        Submission::STATUS_LATE,
                        self::STATUS_NOT_SUBMITTED,
                    ]),
                ],
            ],
            [
                'task_id.integer' => 'Tugas yang dipilih tidak valid.',
                'task_id.exists' => 'Tugas yang dipilih tidak ditemukan.',
                'search.max' => 'Pencarian maksimal 255 karakter.',
                'status.in' => 'Filter status tidak valid.',
            ],
        );

        $tasks = Task::query()
            ->orderBy('title')
            ->get(['id', 'title', 'status', 'deadline']);
        $selectedTask = isset($filters['task_id'])
            ? $tasks->firstWhere('id', (int) $filters['task_id'])
            : null;
        $search = trim($filters['search'] ?? '');
        $status = $filters['status'] ?? null;

        $viewData = [
            'pageTitle' => 'Monitoring Pengumpulan',
            'tasks' => $tasks,
            'selectedTask' => $selectedTask,
            'participants' => null,
            'summary' => null,
            'search' => $search,
            'status' => $status,
        ];

        if ($selectedTask === null) {
            return view('admin.submissions.index', $viewData);
        }

        $participantQuery = User::query()->where('role', User::ROLE_USER);
        $totalParticipants = (clone $participantQuery)->count();
        $submittedCount = (clone $participantQuery)
            ->whereHas(
                'submissions',
                fn (Builder $query) => $query
                    ->where('task_id', $selectedTask->getKey())
                    ->where('status', Submission::STATUS_SUBMITTED),
            )
            ->count();
        $lateCount = (clone $participantQuery)
            ->whereHas(
                'submissions',
                fn (Builder $query) => $query
                    ->where('task_id', $selectedTask->getKey())
                    ->where('status', Submission::STATUS_LATE),
            )
            ->count();
        $notSubmittedCount = $totalParticipants - $submittedCount - $lateCount;

        $participants = $this->monitoringParticipantsQuery($selectedTask, $search, $status)
            ->paginate(12)
            ->withQueryString();

        $viewData['participants'] = $participants;
        $viewData['summary'] = [
            'total' => $totalParticipants,
            'submitted' => $submittedCount,
            'not_submitted' => $notSubmittedCount,
            'late' => $lateCount,
            'percentage' => $totalParticipants > 0
                ? round((($submittedCount + $lateCount) / $totalParticipants) * 100, 1)
                : 0,
        ];

        return view('admin.submissions.index', $viewData);
    }

    public function show(Submission $submission): View
    {
        $this->ensureParticipantSubmission($submission);
        $submission->load(['task', 'user']);

        return view('admin.submissions.show', [
            'pageTitle' => 'Detail Pengumpulan Peserta',
            'submission' => $submission,
            'task' => $submission->task,
            'participant' => $submission->user,
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $filters = $request->validate(
            [
                'task_id' => ['required', 'integer', Rule::exists('tasks', 'id')],
                'search' => ['nullable', 'string', 'max:255'],
                'status' => [
                    'nullable',
                    Rule::in([
                        Submission::STATUS_SUBMITTED,
                        Submission::STATUS_LATE,
                        self::STATUS_NOT_SUBMITTED,
                    ]),
                ],
            ],
            [
                'task_id.required' => 'Tugas wajib dipilih sebelum melakukan export.',
                'task_id.integer' => 'Tugas yang dipilih tidak valid.',
                'task_id.exists' => 'Tugas yang dipilih tidak ditemukan.',
                'search.max' => 'Pencarian maksimal 255 karakter.',
                'status.in' => 'Filter status tidak valid.',
            ],
        );

        $task = Task::query()->findOrFail($filters['task_id']);
        $search = trim($filters['search'] ?? '');
        $status = $filters['status'] ?? null;
        $participants = $this->monitoringParticipantsQuery($task, $search, $status)->get();
        $taskSlug = Str::slug($task->title) ?: 'tugas';
        $fileName = "monitoring-{$taskSlug}-".now()->format('Y-m-d').'.csv';

        return response()->streamDownload(
            function () use ($participants, $task): void {
                $stream = fopen('php://output', 'w');

                if ($stream === false) {
                    return;
                }

                fwrite($stream, "\xEF\xBB\xBF");
                fputcsv($stream, [
                    'Nama Peserta',
                    'Email',
                    'Judul Tugas',
                    'Status',
                    'Waktu Pengumpulan',
                    'Nama File',
                    'Tautan',
                    'Catatan',
                ], ',', '"', '', "\r\n");

                foreach ($participants as $participant) {
                    $submission = $participant->submissions->first();
                    $statusLabel = match ($submission?->status) {
                        Submission::STATUS_SUBMITTED => 'Sudah Mengumpulkan',
                        Submission::STATUS_LATE => 'Terlambat',
                        default => 'Belum Mengumpulkan',
                    };

                    fputcsv($stream, array_map($this->escapeCsvFormula(...), [
                        $participant->name,
                        $participant->email,
                        $task->title,
                        $statusLabel,
                        $submission?->submitted_at?->copy()->locale('id')->translatedFormat('d F Y, H:i') ?? '',
                        $submission?->original_file_name ?? '',
                        $submission?->submission_link ?? '',
                        $submission?->note ?? '',
                    ]), ',', '"', '', "\r\n");
                }

                fclose($stream);
            },
            $fileName,
            ['Content-Type' => 'text/csv; charset=UTF-8'],
        );
    }

    public function download(Submission $submission): StreamedResponse
    {
        $this->ensureParticipantSubmission($submission);
        $filePath = $submission->file_path;

        abort_unless(
            Submission::hasValidPrivateFilePath($filePath),
            404,
            'File pengumpulan tidak tersedia.',
        );

        $disk = Storage::disk('local');

        abort_unless(
            $disk->exists($filePath),
            404,
            'File pengumpulan tidak ditemukan.',
        );

        return $disk->download(
            $filePath,
            $submission->original_file_name ?: "pengumpulan-{$submission->getKey()}",
        );
    }

    private function ensureParticipantSubmission(Submission $submission): void
    {
        abort_unless(
            $submission->user()->where('role', User::ROLE_USER)->exists(),
            404,
        );
    }

    private function escapeCsvFormula(mixed $value): string
    {
        $value = (string) ($value ?? '');

        return preg_match('/\A[=+\-@\t\r]/u', $value) === 1
            ? "'{$value}"
            : $value;
    }

    private function monitoringParticipantsQuery(
        Task $task,
        string $search,
        ?string $status,
    ): Builder {
        return User::query()
            ->where('role', User::ROLE_USER)
            ->with([
                'submissions' => fn ($query) => $query
                    ->where('task_id', $task->getKey()),
            ])
            ->when(
                $search !== '',
                fn (Builder $query) => $query->where(
                    fn (Builder $query) => $query
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%"),
                ),
            )
            ->when(
                $status === Submission::STATUS_SUBMITTED,
                fn (Builder $query) => $query->whereHas(
                    'submissions',
                    fn (Builder $query) => $query
                        ->where('task_id', $task->getKey())
                        ->where('status', Submission::STATUS_SUBMITTED),
                ),
            )
            ->when(
                $status === Submission::STATUS_LATE,
                fn (Builder $query) => $query->whereHas(
                    'submissions',
                    fn (Builder $query) => $query
                        ->where('task_id', $task->getKey())
                        ->where('status', Submission::STATUS_LATE),
                ),
            )
            ->when(
                $status === self::STATUS_NOT_SUBMITTED,
                fn (Builder $query) => $query->whereDoesntHave(
                    'submissions',
                    fn (Builder $query) => $query
                        ->where('task_id', $task->getKey()),
                ),
            )
            ->orderBy('name');
    }
}
