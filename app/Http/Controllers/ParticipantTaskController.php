<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ParticipantTaskController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate(
            [
                'search' => ['nullable', 'string', 'max:255'],
                'category' => ['nullable', 'string', 'max:255'],
            ],
            [
                'search.max' => 'Pencarian maksimal 255 karakter.',
                'category.max' => 'Filter kategori maksimal 255 karakter.',
            ],
        );

        $search = trim($filters['search'] ?? '');
        $category = $filters['category'] ?? null;
        $userId = $request->user()->getKey();

        $tasks = Task::query()
            ->visibleToParticipants()
            ->with([
                'submissions' => fn ($query) => $query->where('user_id', $userId),
            ])
            ->when(
                $search !== '',
                fn (Builder $query) => $query->where('title', 'like', "%{$search}%"),
            )
            ->when(
                $category !== null,
                fn (Builder $query) => $query->where('category', $category),
            )
            ->orderByRaw('CASE WHEN status = ? THEN 0 ELSE 1 END', [Task::STATUS_ACTIVE])
            ->orderBy('deadline')
            ->paginate(9)
            ->withQueryString();

        $categories = Task::query()
            ->visibleToParticipants()
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view('tasks.index', [
            'pageTitle' => 'Daftar Tugas',
            'tasks' => $tasks,
            'categories' => $categories,
            'search' => $search,
            'category' => $category,
        ]);
    }

    public function show(Request $request, Task $task): View
    {
        abort_unless(
            in_array($task->status, Task::PARTICIPANT_VISIBLE_STATUSES, true),
            404,
        );

        $userId = $request->user()->getKey();

        $task->load([
            'creator',
            'submissions' => fn ($query) => $query->where('user_id', $userId),
        ]);

        return view('tasks.show', [
            'pageTitle' => 'Detail Tugas',
            'task' => $task,
        ]);
    }
}
