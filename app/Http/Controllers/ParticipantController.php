<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;

class ParticipantController extends Controller
{
    public function index(): View
    {
        $totalTasks = Task::query()->visibleToParticipants()->count();

        $participants = User::query()
            ->where('role', User::ROLE_USER)
            ->withCount([
                'submissions as submitted_count' => fn ($query) => $query
                    ->where('status', Submission::STATUS_SUBMITTED)
                    ->whereHas(
                        'task',
                        fn (Builder $query) => $query->visibleToParticipants(),
                    ),
                'submissions as late_count' => fn ($query) => $query
                    ->where('status', Submission::STATUS_LATE)
                    ->whereHas(
                        'task',
                        fn (Builder $query) => $query->visibleToParticipants(),
                    ),
            ])
            ->orderBy('name')
            ->paginate(12);

        return view('admin.participants.index', [
            'pageTitle' => 'Data Peserta',
            'participants' => $participants,
            'totalTasks' => $totalTasks,
        ]);
    }
}
