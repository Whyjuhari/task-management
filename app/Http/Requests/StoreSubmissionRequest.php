<?php

namespace App\Http\Requests;

use App\Models\Submission;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        $submission = $this->route('submission');

        if ($user?->role !== User::ROLE_USER) {
            return false;
        }

        return ! $submission instanceof Submission
            || $submission->user_id === $user->getKey();
    }

    public function rules(): array
    {
        $task = $this->task();
        $submission = $this->route('submission');
        $keepsExistingFile = $submission instanceof Submission
            && filled($submission->file_path)
            && $task?->submission_type !== Task::SUBMISSION_TYPE_LINK;

        $requiresFile = $task?->submission_type === Task::SUBMISSION_TYPE_FILE
            && ! $keepsExistingFile;
        $requiresLink = $task?->submission_type === Task::SUBMISSION_TYPE_LINK
            || (
                $task?->submission_type === Task::SUBMISSION_TYPE_FILE_OR_LINK
                && ! $keepsExistingFile
                && ! $this->hasFile('file')
            );

        return [
            'file' => [
                Rule::requiredIf($requiresFile),
                Rule::prohibitedIf($task?->submission_type === Task::SUBMISSION_TYPE_LINK),
                'nullable',
                'file',
                'mimes:pdf,doc,docx,zip,png,jpg,jpeg',
                'max:5120',
            ],
            'submission_link' => [
                Rule::requiredIf($requiresLink),
                Rule::prohibitedIf($task?->submission_type === Task::SUBMISSION_TYPE_FILE),
                'nullable',
                'string',
                'max:2048',
                'url:http,https',
            ],
            'note' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'File pengumpulan wajib diunggah.',
            'file.prohibited' => 'Tugas ini hanya menerima pengumpulan berupa tautan.',
            'file.file' => 'File pengumpulan tidak valid.',
            'file.mimes' => 'File harus berformat PDF, DOC, DOCX, ZIP, PNG, JPG, atau JPEG.',
            'file.max' => 'Ukuran file maksimal 5 MB.',
            'submission_link.required' => 'Tautan pengumpulan wajib diisi jika tidak mengunggah file.',
            'submission_link.prohibited' => 'Tugas ini hanya menerima pengumpulan berupa file.',
            'submission_link.max' => 'Tautan pengumpulan maksimal 2048 karakter.',
            'submission_link.url' => 'Tautan pengumpulan harus berupa URL HTTP atau HTTPS yang valid.',
            'note.max' => 'Catatan maksimal 5000 karakter.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'submission_link' => $this->cleanText($this->input('submission_link')),
            'note' => $this->cleanText($this->input('note')),
        ]);
    }

    private function task(): ?Task
    {
        $task = $this->route('task');

        if ($task instanceof Task) {
            return $task;
        }

        $submission = $this->route('submission');

        return $submission instanceof Submission ? $submission->task : null;
    }

    private function cleanText(mixed $value): mixed
    {
        if (! is_string($value)) {
            return $value;
        }

        $value = trim($value);

        return $value === '' ? null : $value;
    }
}
