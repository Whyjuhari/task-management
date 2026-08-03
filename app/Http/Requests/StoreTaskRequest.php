<?php

namespace App\Http\Requests;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === User::ROLE_ADMIN;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'instructions' => ['nullable', 'string'],
            'start_date' => ['nullable', 'date'],
            'deadline' => ['required', 'date', 'after_or_equal:start_date'],
            'submission_type' => ['required', Rule::in(Task::SUBMISSION_TYPES)],
            'status' => ['required', Rule::in(Task::STATUSES)],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul tugas wajib diisi.',
            'title.max' => 'Judul tugas maksimal 255 karakter.',
            'category.max' => 'Kategori maksimal 255 karakter.',
            'description.required' => 'Deskripsi wajib diisi.',
            'start_date.date' => 'Tanggal mulai tidak valid.',
            'deadline.required' => 'Deadline wajib diisi.',
            'deadline.date' => 'Deadline tidak valid.',
            'deadline.after_or_equal' => 'Deadline tidak boleh sebelum tanggal mulai.',
            'submission_type.required' => 'Jenis pengumpulan wajib dipilih.',
            'submission_type.in' => 'Jenis pengumpulan tidak valid.',
            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status tidak valid.',
        ];
    }
}
