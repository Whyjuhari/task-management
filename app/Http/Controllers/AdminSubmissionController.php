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

        $participants = $participantQuery
            ->with([
                'submissions' => fn ($query) => $query
                    ->where('task_id', $selectedTask->getKey()),
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
                        ->where('task_id', $selectedTask->getKey())
                        ->where('status', Submission::STATUS_SUBMITTED),
                ),
            )
            ->when(
                $status === Submission::STATUS_LATE,
                fn (Builder $query) => $query->whereHas(
                    'submissions',
                    fn (Builder $query) => $query
                        ->where('task_id', $selectedTask->getKey())
                        ->where('status', Submission::STATUS_LATE),
                ),
            )
            ->when(
                $status === self::STATUS_NOT_SUBMITTED,
                fn (Builder $query) => $query->whereDoesntHave(
                    'submissions',
                    fn (Builder $query) => $query
                        ->where('task_id', $selectedTask->getKey()),
                ),
            )
            ->orderBy('name')
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

    public function download(Submission $submission): StreamedResponse
    {
        $this->ensureParticipantSubmission($submission);
        $filePath = $submission->file_path;

        abort_unless(
            is_string($filePath) && Str::startsWith($filePath, 'submissions/'),
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
}
