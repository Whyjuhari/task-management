@php
    $isEditing = isset($task);
    $startDateValue = old(
        'start_date',
        $isEditing && $task->start_date ? $task->start_date->format('Y-m-d\TH:i') : '',
    );
    $deadlineValue = old(
        'deadline',
        $isEditing && $task->deadline ? $task->deadline->format('Y-m-d\TH:i') : '',
    );
@endphp

@if ($errors->any())
    <div role="alert" class="mb-6 rounded-xl border border-danger/20 bg-danger/5 px-4 py-3 text-sm text-danger">
        <p class="font-semibold">Periksa kembali data tugas.</p>
        <ul class="mt-2 list-disc space-y-1 pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST"
    action="{{ $isEditing ? route('admin.tasks.update', $task) : route('admin.tasks.store') }}"
    class="space-y-6">
    @csrf
    @if ($isEditing)
        @method('PUT')
    @endif

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="lg:col-span-2">
            <label for="title" class="mb-2 block text-sm font-semibold text-navy">
                Judul tugas <span class="text-danger" aria-hidden="true">*</span>
            </label>
            <input id="title" name="title" type="text" value="{{ old('title', $task->title ?? '') }}" required
                maxlength="255" @class([
                    'min-h-11 w-full rounded-xl border bg-card px-4 py-3 text-sm text-navy outline-none transition focus:ring-3 focus:ring-primary/15',
                    'border-danger focus:border-danger' => $errors->has('title'),
                    'border-border focus:border-primary' => !$errors->has('title'),
                ])>
            @error('title')
                <p class="mt-2 text-sm text-danger">{{ $message }}</p>
            @enderror
        </div>

        <div class="lg:col-span-2">
            <label for="category" class="mb-2 block text-sm font-semibold text-navy">
                Kategori <span class="font-normal text-secondary">(opsional)</span>
            </label>
            <input id="category" name="category" type="text" value="{{ old('category', $task->category ?? '') }}"
                maxlength="255" placeholder="Contoh: Web Development" @class([
                    'min-h-11 w-full rounded-xl border bg-card px-4 py-3 text-sm text-navy outline-none transition placeholder:text-slate-400 focus:ring-3 focus:ring-primary/15',
                    'border-danger focus:border-danger' => $errors->has('category'),
                    'border-border focus:border-primary' => !$errors->has('category'),
                ])>
            @error('category')
                <p class="mt-2 text-sm text-danger">{{ $message }}</p>
            @enderror
        </div>

        <div class="lg:col-span-2">
            <label for="description" class="mb-2 block text-sm font-semibold text-navy">
                Deskripsi <span class="text-danger" aria-hidden="true">*</span>
            </label>
            <textarea id="description" name="description" rows="5" required @class([
                'w-full rounded-xl border bg-card px-4 py-3 text-sm text-navy outline-none transition focus:ring-3 focus:ring-primary/15',
                'border-danger focus:border-danger' => $errors->has('description'),
                'border-border focus:border-primary' => !$errors->has('description'),
            ])>{{ old('description', $task->description ?? '') }}</textarea>
            @error('description')
                <p class="mt-2 text-sm text-danger">{{ $message }}</p>
            @enderror
        </div>

        <div class="lg:col-span-2">
            <label for="instructions" class="mb-2 block text-sm font-semibold text-navy">
                Instruksi <span class="font-normal text-secondary">(opsional)</span>
            </label>
            <textarea id="instructions" name="instructions" rows="5" @class([
                'w-full rounded-xl border bg-card px-4 py-3 text-sm text-navy outline-none transition focus:ring-3 focus:ring-primary/15',
                'border-danger focus:border-danger' => $errors->has('instructions'),
                'border-border focus:border-primary' => !$errors->has('instructions'),
            ])>{{ old('instructions', $task->instructions ?? '') }}</textarea>
            @error('instructions')
                <p class="mt-2 text-sm text-danger">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="start_date" class="mb-2 block text-sm font-semibold text-navy">
                Tanggal mulai <span class="font-normal text-secondary">(opsional)</span>
            </label>
            <input id="start_date" name="start_date" type="datetime-local" value="{{ $startDateValue }}"
                @class([
                    'min-h-11 w-full rounded-xl border bg-card px-4 py-3 text-sm text-navy outline-none transition focus:ring-3 focus:ring-primary/15',
                    'border-danger focus:border-danger' => $errors->has('start_date'),
                    'border-border focus:border-primary' => !$errors->has('start_date'),
                ])>
            @error('start_date')
                <p class="mt-2 text-sm text-danger">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="deadline" class="mb-2 block text-sm font-semibold text-navy">
                Deadline <span class="text-danger" aria-hidden="true">*</span>
            </label>
            <input id="deadline" name="deadline" type="datetime-local" value="{{ $deadlineValue }}" required
                @class([
                    'min-h-11 w-full rounded-xl border bg-card px-4 py-3 text-sm text-navy outline-none transition focus:ring-3 focus:ring-primary/15',
                    'border-danger focus:border-danger' => $errors->has('deadline'),
                    'border-border focus:border-primary' => !$errors->has('deadline'),
                ])>
            @error('deadline')
                <p class="mt-2 text-sm text-danger">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="submission_type" class="mb-2 block text-sm font-semibold text-navy">
                Jenis pengumpulan <span class="text-danger" aria-hidden="true">*</span>
            </label>
            <select id="submission_type" name="submission_type" required @class([
                'min-h-11 w-full rounded-xl border bg-card px-4 py-3 text-sm text-navy outline-none transition focus:ring-3 focus:ring-primary/15',
                'border-danger focus:border-danger' => $errors->has('submission_type'),
                'border-border focus:border-primary' => !$errors->has('submission_type'),
            ])>
                <option value="">Pilih jenis pengumpulan</option>
                <option value="file" @selected(old('submission_type', $task->submission_type ?? '') === 'file')>File</option>
                <option value="link" @selected(old('submission_type', $task->submission_type ?? '') === 'link')>Tautan</option>
                <option value="file_or_link" @selected(old('submission_type', $task->submission_type ?? '') === 'file_or_link')>File atau tautan</option>
            </select>
            @error('submission_type')
                <p class="mt-2 text-sm text-danger">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="status" class="mb-2 block text-sm font-semibold text-navy">
                Status <span class="text-danger" aria-hidden="true">*</span>
            </label>
            <select id="status" name="status" required @class([
                'min-h-11 w-full rounded-xl border bg-card px-4 py-3 text-sm text-navy outline-none transition focus:ring-3 focus:ring-primary/15',
                'border-danger focus:border-danger' => $errors->has('status'),
                'border-border focus:border-primary' => !$errors->has('status'),
            ])>
                <option value="draft" @selected(old('status', $task->status ?? 'draft') === 'draft')>Draf</option>
                <option value="active" @selected(old('status', $task->status ?? 'draft') === 'active')>Aktif</option>
                <option value="closed" @selected(old('status', $task->status ?? 'draft') === 'closed')>Ditutup</option>
            </select>
            @error('status')
                <p class="mt-2 text-sm text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="flex flex-col-reverse gap-3 border-t border-border pt-6 sm:flex-row sm:justify-end">
        <a href="{{ $isEditing ? route('admin.tasks.show', $task) : route('admin.tasks.index') }}"
            class="inline-flex min-h-11 items-center justify-center rounded-xl border border-border bg-card px-5 py-2.5 text-sm font-semibold text-navy transition hover:bg-slate-50 focus:outline-none focus:ring-3 focus:ring-primary/15">
            Batal
        </a>
        <button type="submit"
            class="inline-flex min-h-11 cursor-pointer items-center justify-center rounded-xl bg-primary px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-navy focus:outline-none focus:ring-3 focus:ring-primary/30">
            {{ $isEditing ? 'Simpan Perubahan' : 'Buat Tugas' }}
        </button>
    </div>
</form>
