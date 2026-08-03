<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate(
            [
                'search' => ['nullable', 'string', 'max:255'],
                'status' => ['nullable', Rule::in(Task::STATUSES)],
            ],
            [
                'search.max' => 'Pencarian maksimal 255 karakter.',
                'status.in' => 'Filter status tidak valid.',
            ],
        );

        $search = trim($filters['search'] ?? '');
        $status = $filters['status'] ?? null;

        $tasks = Task::query()
            ->with('creator')
            ->when(
                $search !== '',
                fn (Builder $query) => $query->where('title', 'like', "%{$search}%"),
            )
            ->when(
                $status !== null,
                fn (Builder $query) => $query->where('status', $status),
            )
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.tasks.index', [
            'pageTitle' => 'Kelola Tugas',
            'tasks' => $tasks,
            'search' => $search,
            'status' => $status,
        ]);
    }

    public function create(): View
    {
        return view('admin.tasks.create', [
            'pageTitle' => 'Buat Tugas',
        ]);
    }

    public function store(StoreTaskRequest $request): RedirectResponse
    {
        $task = $request->user()->createdTasks()->create($request->validated());

        return redirect()
            ->route('admin.tasks.show', $task)
            ->with('success', 'Tugas berhasil dibuat.');
    }

    public function show(Task $task): View
    {
        $task->load('creator');

        return view('admin.tasks.show', [
            'pageTitle' => 'Detail Tugas',
            'task' => $task,
        ]);
    }

    public function edit(Task $task): View
    {
        return view('admin.tasks.edit', [
            'pageTitle' => 'Edit Tugas',
            'task' => $task,
        ]);
    }

    public function update(UpdateTaskRequest $request, Task $task): RedirectResponse
    {
        $task->update($request->validated());

        return redirect()
            ->route('admin.tasks.show', $task)
            ->with('success', 'Tugas berhasil diperbarui.');
    }

    public function destroy(Task $task): RedirectResponse
    {
        $task->delete();

        return redirect()
            ->route('admin.tasks.index')
            ->with('success', 'Tugas berhasil dihapus.');
    }

    public function updateStatus(Request $request, Task $task): RedirectResponse
    {
        $validated = $request->validate(
            ['status' => ['required', Rule::in(Task::STATUSES)]],
            [
                'status.required' => 'Status wajib dipilih.',
                'status.in' => 'Status tidak valid.',
            ],
        );

        $task->update($validated);

        return back()->with('success', 'Status tugas berhasil diperbarui.');
    }
}
