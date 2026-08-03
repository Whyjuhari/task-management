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
    <div role="alert" class="mb-6 rounded-xl border border-l-4 border-danger/20 border-l-danger bg-danger/5 px-4 py-3 text-sm text-danger">
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
    class="space-y-5">
    @csrf
    @if ($isEditing)
        @method('PUT')
    @endif

    <fieldset class="ui-surface p-5 sm:p-6">
        <legend class="sr-only">Informasi utama tugas</legend>
        <div class="ui-section-heading mb-6">
            <h2>Informasi utama</h2>
            <p>Isi judul, kategori, dan penjelasan yang akan dibaca peserta.</p>
        </div>

        <div class="grid gap-5">
            <div>
                <label for="title" class="ui-label">
                    Judul tugas <span class="text-danger" aria-hidden="true">*</span>
                </label>
                <input id="title" name="title" type="text" value="{{ old('title', $task->title ?? '') }}" required
                    maxlength="255" @class([
                        'ui-control',
                        'border-danger focus:border-danger' => $errors->has('title'),
                    ]) @error('title') aria-invalid="true" @enderror>
                @error('title')
                    <p role="alert" class="mt-2 text-sm text-danger">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="category" class="ui-label">
                    Kategori <span class="font-normal text-secondary">(opsional)</span>
                </label>
                <input id="category" name="category" type="text" value="{{ old('category', $task->category ?? '') }}"
                    maxlength="255" placeholder="Contoh: Web Development" @class([
                        'ui-control',
                        'border-danger focus:border-danger' => $errors->has('category'),
                    ]) @error('category') aria-invalid="true" @enderror>
                @error('category')
                    <p role="alert" class="mt-2 text-sm text-danger">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="ui-label">
                    Deskripsi <span class="text-danger" aria-hidden="true">*</span>
                </label>
                <textarea id="description" name="description" rows="5" required @class([
                    'ui-control resize-y',
                    'border-danger focus:border-danger' => $errors->has('description'),
                ]) @error('description') aria-invalid="true" @enderror>{{ old('description', $task->description ?? '') }}</textarea>
                @error('description')
                    <p role="alert" class="mt-2 text-sm text-danger">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="instructions" class="ui-label">
                    Instruksi <span class="font-normal text-secondary">(opsional)</span>
                </label>
                <textarea id="instructions" name="instructions" rows="5" @class([
                    'ui-control resize-y',
                    'border-danger focus:border-danger' => $errors->has('instructions'),
                ]) @error('instructions') aria-invalid="true" @enderror>{{ old('instructions', $task->instructions ?? '') }}</textarea>
                @error('instructions')
                    <p role="alert" class="mt-2 text-sm text-danger">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </fieldset>

    <fieldset class="ui-surface p-5 sm:p-6">
        <legend class="sr-only">Jadwal dan aturan pengumpulan</legend>
        <div class="ui-section-heading mb-6">
            <h2>Jadwal dan pengumpulan</h2>
            <p>Tentukan periode tugas, bentuk hasil yang diterima, dan status publikasinya.</p>
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="start_date" class="ui-label">
                    Tanggal mulai <span class="font-normal text-secondary">(opsional)</span>
                </label>
                <input id="start_date" name="start_date" type="datetime-local" value="{{ $startDateValue }}"
                    @class(['ui-control', 'border-danger focus:border-danger' => $errors->has('start_date')])
                    @error('start_date') aria-invalid="true" @enderror>
                @error('start_date')
                    <p role="alert" class="mt-2 text-sm text-danger">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="deadline" class="ui-label">
                    Deadline <span class="text-danger" aria-hidden="true">*</span>
                </label>
                <input id="deadline" name="deadline" type="datetime-local" value="{{ $deadlineValue }}" required
                    @class(['ui-control', 'border-danger focus:border-danger' => $errors->has('deadline')])
                    @error('deadline') aria-invalid="true" @enderror>
                @error('deadline')
                    <p role="alert" class="mt-2 text-sm text-danger">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="submission_type" class="ui-label">
                    Jenis pengumpulan <span class="text-danger" aria-hidden="true">*</span>
                </label>
                <select id="submission_type" name="submission_type" required @class([
                    'ui-control',
                    'border-danger focus:border-danger' => $errors->has('submission_type'),
                ]) @error('submission_type') aria-invalid="true" @enderror>
                    <option value="">Pilih jenis pengumpulan</option>
                    <option value="file" @selected(old('submission_type', $task->submission_type ?? '') === 'file')>File</option>
                    <option value="link" @selected(old('submission_type', $task->submission_type ?? '') === 'link')>Tautan</option>
                    <option value="file_or_link" @selected(old('submission_type', $task->submission_type ?? '') === 'file_or_link')>File atau tautan</option>
                </select>
                @error('submission_type')
                    <p role="alert" class="mt-2 text-sm text-danger">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="status" class="ui-label">
                    Status <span class="text-danger" aria-hidden="true">*</span>
                </label>
                <select id="status" name="status" required @class([
                    'ui-control',
                    'border-danger focus:border-danger' => $errors->has('status'),
                ]) @error('status') aria-invalid="true" @enderror>
                    <option value="draft" @selected(old('status', $task->status ?? 'draft') === 'draft')>Draf</option>
                    <option value="active" @selected(old('status', $task->status ?? 'draft') === 'active')>Aktif</option>
                    <option value="closed" @selected(old('status', $task->status ?? 'draft') === 'closed')>Ditutup</option>
                </select>
                @error('status')
                    <p role="alert" class="mt-2 text-sm text-danger">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </fieldset>

    <div class="ui-surface flex flex-col-reverse gap-3 p-4 sm:flex-row sm:items-center sm:justify-end">
        <a href="{{ $isEditing ? route('admin.tasks.show', $task) : route('admin.tasks.index') }}"
            class="ui-button ui-button-secondary">Batal</a>
        <button type="submit" class="ui-button ui-button-primary px-5">
            {{ $isEditing ? 'Simpan Perubahan' : 'Buat Tugas' }}
        </button>
    </div>
</form>
