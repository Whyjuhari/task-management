<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSubmissionRequest;
use App\Models\Submission;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use RuntimeException;
use Throwable;

class SubmissionController extends Controller
{
    public function create(Request $request, Task $task): View|RedirectResponse
    {
        $this->ensureTaskAcceptsSubmissions($task);

        $submission = $task->submissions()
            ->where('user_id', $request->user()->getKey())
            ->first();

        if ($submission !== null) {
            return redirect()
                ->route('submissions.edit', $submission)
                ->with('warning', 'Anda sudah memiliki pengumpulan. Silakan perbarui data yang tersedia.');
        }

        return view('submissions.create', [
            'pageTitle' => 'Kumpulkan Tugas',
            'task' => $task,
            'submission' => new Submission,
        ]);
    }

    public function store(StoreSubmissionRequest $request, Task $task): RedirectResponse
    {
        $this->ensureTaskAcceptsSubmissions($task);

        $submission = $task->submissions()
            ->where('user_id', $request->user()->getKey())
            ->first();
        $wasExisting = $submission !== null;

        $submission = $this->persistSubmission($request, $task, $submission);

        return redirect()
            ->route('submissions.show', $submission)
            ->with(
                'success',
                $wasExisting
                    ? 'Pengumpulan berhasil diperbarui.'
                    : 'Tugas berhasil dikumpulkan.',
            );
    }

    public function show(Request $request, Submission $submission): View
    {
        $this->ensureOwnership($request, $submission);
        $submission->load('task');

        return view('submissions.show', [
            'pageTitle' => 'Detail Pengumpulan',
            'submission' => $submission,
            'task' => $submission->task,
        ]);
    }

    public function edit(Request $request, Submission $submission): View
    {
        $this->ensureOwnership($request, $submission);
        $submission->load('task');
        $this->ensureTaskAcceptsSubmissions($submission->task);

        return view('submissions.edit', [
            'pageTitle' => 'Perbarui Pengumpulan',
            'submission' => $submission,
            'task' => $submission->task,
        ]);
    }

    public function update(StoreSubmissionRequest $request, Submission $submission): RedirectResponse
    {
        $this->ensureOwnership($request, $submission);
        $submission->load('task');
        $this->ensureTaskAcceptsSubmissions($submission->task);

        $submission = $this->persistSubmission($request, $submission->task, $submission);

        return redirect()
            ->route('submissions.show', $submission)
            ->with('success', 'Pengumpulan berhasil diperbarui.');
    }

    private function persistSubmission(
        StoreSubmissionRequest $request,
        Task $task,
        ?Submission $submission,
    ): Submission {
        $validated = $request->validated();
        $disk = Storage::disk('local');
        $oldFilePath = $submission?->file_path;
        $newFilePath = null;
        $originalFileName = null;
        $submittedAt = now();

        try {
            if ($request->hasFile('file')) {
                $uploadedFile = $request->file('file');
                $newFilePath = $uploadedFile->store('submissions', 'local');

                if (! is_string($newFilePath)) {
                    throw new RuntimeException('File pengumpulan gagal disimpan.');
                }

                $originalFileName = Str::limit($uploadedFile->getClientOriginalName(), 255, '');
            }

            DB::transaction(function () use (
                &$submission,
                $request,
                $task,
                $validated,
                $submittedAt,
                $newFilePath,
                $originalFileName,
            ): void {
                if ($submission === null) {
                    $submission = new Submission;
                    $submission->task()->associate($task);
                    $submission->user()->associate($request->user());
                }

                $submission->fill([
                    'submission_link' => $task->submission_type === Task::SUBMISSION_TYPE_FILE
                        ? null
                        : ($validated['submission_link'] ?? null),
                    'note' => $validated['note'] ?? null,
                ]);

                $submission->submitted_at = $submittedAt;
                $submission->status = $submittedAt->lessThanOrEqualTo($task->deadline)
                    ? Submission::STATUS_SUBMITTED
                    : Submission::STATUS_LATE;

                if ($task->submission_type === Task::SUBMISSION_TYPE_LINK) {
                    $submission->file_path = null;
                    $submission->original_file_name = null;
                } elseif ($newFilePath !== null) {
                    $submission->file_path = $newFilePath;
                    $submission->original_file_name = $originalFileName;
                }

                $submission->save();
            });
        } catch (Throwable $exception) {
            if ($newFilePath !== null) {
                $disk->delete($newFilePath);
            }

            throw $exception;
        }

        if ($oldFilePath !== null && $oldFilePath !== $submission->file_path) {
            $disk->delete($oldFilePath);
        }

        return $submission;
    }

    private function ensureOwnership(Request $request, Submission $submission): void
    {
        abort_unless(
            $submission->user_id === $request->user()->getKey(),
            403,
            'Anda tidak memiliki akses ke pengumpulan ini.',
        );
    }

    private function ensureTaskAcceptsSubmissions(Task $task): void
    {
        abort_unless(
            $task->status === Task::STATUS_ACTIVE,
            403,
            'Tugas ini tidak menerima pengumpulan.',
        );

        abort_if(
            $task->start_date?->isFuture(),
            403,
            'Tugas belum memasuki tanggal mulai.',
        );
    }
}
