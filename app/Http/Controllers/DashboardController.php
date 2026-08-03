<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    private const NEAR_DEADLINE_DAYS = 3;

    public function admin(): View
    {
        $now = now();
        $totalParticipants = User::query()
            ->where('role', User::ROLE_USER)
            ->count();

        $statistics = [
            'total_tasks' => Task::query()->count(),
            'active_tasks' => Task::query()->where('status', Task::STATUS_ACTIVE)->count(),
            'total_participants' => $totalParticipants,
            'total_submissions' => Submission::query()->count(),
            'late_submissions' => Submission::query()
                ->where('status', Submission::STATUS_LATE)
                ->count(),
            'near_deadline_tasks' => Task::query()
                ->where('status', Task::STATUS_ACTIVE)
                ->whereBetween('deadline', [
                    $now,
                    $now->copy()->addDays(self::NEAR_DEADLINE_DAYS),
                ])
                ->count(),
        ];

        $latestTasks = Task::query()
            ->with('creator:id,name')
            ->latest()
            ->limit(5)
            ->get();

        $latestSubmissions = Submission::query()
            ->with([
                'task:id,title',
                'user:id,name,email',
            ])
            ->latest('submitted_at')
            ->limit(5)
            ->get();

        $nearestDeadlineTasks = Task::query()
            ->where('status', Task::STATUS_ACTIVE)
            ->where('deadline', '>=', $now)
            ->orderBy('deadline')
            ->limit(5)
            ->get();

        $taskProgress = Task::query()
            ->where('status', Task::STATUS_ACTIVE)
            ->withCount([
                'submissions as participant_submissions_count' => fn($query) => $query
                    ->whereHas(
                        'user',
                        fn(Builder $query) => $query->where('role', User::ROLE_USER),
                    ),
            ])
            ->orderBy('deadline')
            ->limit(5)
            ->get()
            ->map(fn(Task $task): array => [
                'task' => $task,
                'submitted_count' => $task->participant_submissions_count,
                'percentage' => $totalParticipants > 0
                    ? round(($task->participant_submissions_count / $totalParticipants) * 100, 1)
                    : 0,
            ]);

        return view('admin.dashboard', [
            'pageTitle' => 'Dasbor Admin',
            'description' => 'Ringkasan area kerja instruktur untuk mengelola kegiatan pelatihan.',
            'statistics' => $statistics,
            'latestTasks' => $latestTasks,
            'latestSubmissions' => $latestSubmissions,
            'nearestDeadlineTasks' => $nearestDeadlineTasks,
            'taskProgress' => $taskProgress,
        ]);
    }

    public function participant(Request $request): View
    {
        $participant = $request->user();
        $participantId = $participant->getKey();
        $activeTasks = Task::query()->where('status', Task::STATUS_ACTIVE);

        $totalActiveTasks = (clone $activeTasks)->count();
        $submittedCount = (clone $activeTasks)
            ->whereHas(
                'submissions',
                fn(Builder $query) => $query
                    ->where('user_id', $participantId)
                    ->where('status', Submission::STATUS_SUBMITTED),
            )
            ->count();
        $lateCount = (clone $activeTasks)
            ->whereHas(
                'submissions',
                fn(Builder $query) => $query
                    ->where('user_id', $participantId)
                    ->where('status', Submission::STATUS_LATE),
            )
            ->count();
        $notSubmittedCount = (clone $activeTasks)
            ->whereDoesntHave(
                'submissions',
                fn(Builder $query) => $query->where('user_id', $participantId),
            )
            ->count();

        $urgentTasksQuery = Task::query()
            ->where('status', Task::STATUS_ACTIVE)
            ->where(
                fn(Builder $query) => $query
                    ->whereNull('start_date')
                    ->orWhere('start_date', '<=', now()),
            )
            ->whereDoesntHave(
                'submissions',
                fn(Builder $query) => $query->where('user_id', $participantId),
            );

        $urgentTasks = (clone $urgentTasksQuery)
            ->orderBy('deadline')
            ->limit(5)
            ->get();

        $nearestDeadlineTask = (clone $urgentTasksQuery)
            ->where('deadline', '>=', now())
            ->orderBy('deadline')
            ->first();

        $latestSubmissions = Submission::query()
            ->where('user_id', $participantId)
            ->with('task:id,title')
            ->latest('submitted_at')
            ->limit(5)
            ->get();

        $statistics = [
            'total_active_tasks' => $totalActiveTasks,
            'submitted' => $submittedCount,
            'not_submitted' => $notSubmittedCount,
            'late' => $lateCount,
            'nearest_deadline' => $nearestDeadlineTask?->deadline,
            'completion_percentage' => $totalActiveTasks > 0
                ? round((($submittedCount + $lateCount) / $totalActiveTasks) * 100, 1)
                : 0,
        ];

        $latestSubmissionCount = $latestSubmissions->count();

        return view('dashboard', [
            'pageTitle' => 'Dasbor Peserta',
            'description' => 'Area kerja peserta untuk mengikuti tugas dan pengumpulan pelatihan.',
            'participant' => $participant,
            'statistics' => $statistics,
            'nearestDeadlineTask' => $nearestDeadlineTask,
            'urgentTasks' => $urgentTasks,
            'latestSubmissions' => $latestSubmissions,
            'latestSubmissionCount' => $latestSubmissionCount
        ]);
    }
}