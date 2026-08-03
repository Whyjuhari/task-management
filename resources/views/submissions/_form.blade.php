@php
    $acceptsFile = in_array($task->submission_type, [
        \App\Models\Task::SUBMISSION_TYPE_FILE,
        \App\Models\Task::SUBMISSION_TYPE_FILE_OR_LINK,
    ], true);
    $acceptsLink = in_array($task->submission_type, [
        \App\Models\Task::SUBMISSION_TYPE_LINK,
        \App\Models\Task::SUBMISSION_TYPE_FILE_OR_LINK,
    ], true);
@endphp

@if ($errors->any())
    <div role="alert" class="mx-auto mb-6 max-w-4xl rounded-xl border border-l-4 border-danger/20 border-l-danger bg-danger/5 px-4 py-3 text-sm text-danger">
        <p class="font-semibold">Pengumpulan belum dapat disimpan.</p>
        <p class="mt-1">Periksa kembali data yang ditandai di bawah ini.</p>
    </div>
@endif

<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="mx-auto max-w-4xl space-y-5">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <fieldset class="ui-surface p-5 sm:p-6">
        <legend class="sr-only">Hasil tugas</legend>
        <div class="ui-section-heading mb-6">
            <h2>Hasil tugas</h2>
            <p>Lampirkan hasil sesuai jenis pengumpulan yang ditentukan instruktur.</p>
        </div>

        <div class="space-y-6">
            @if ($acceptsFile)
                <div>
                    <label for="file" class="ui-label">
                        File pengumpulan
                        @if ($task->submission_type === \App\Models\Task::SUBMISSION_TYPE_FILE && ! $submission->file_path)
                            <span class="text-danger">*</span>
                        @endif
                    </label>

                    @if ($submission->file_path)
                        <div class="ui-surface-muted mb-3 flex items-start gap-3 p-3 text-sm">
                            <svg class="mt-0.5 size-5 shrink-0 text-primary" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Zm0 0v6h6" />
                            </svg>
                            <p class="min-w-0 text-secondary">
                                File saat ini:
                                <span class="break-words font-semibold text-navy">{{ $submission->original_file_name }}</span>
                            </p>
                        </div>
                    @endif

                    <input id="file" name="file" type="file" accept=".pdf,.doc,.docx,.zip,.png,.jpg,.jpeg"
                        @error('file') aria-invalid="true" @enderror
                        @class([
                            'min-h-11 max-w-full rounded-xl border border-border bg-card px-3 py-2.5 text-sm text-navy file:mr-4 file:rounded-lg file:border-0 file:bg-primary/10 file:px-3 file:py-2 file:font-semibold file:text-primary hover:file:bg-primary/15 focus:outline-none focus:ring-3 focus:ring-primary/15',
                            'border-danger' => $errors->has('file'),
                        ])>
                    <p class="mt-2 text-xs leading-5 text-secondary">
                        Format: PDF, DOC, DOCX, ZIP, PNG, JPG, atau JPEG. Maksimal 5 MB.
                        @if ($submission->file_path)
                            Biarkan kosong jika file tidak ingin diganti.
                        @endif
                    </p>
                    @error('file')
                        <p role="alert" class="mt-2 text-sm text-danger">{{ $message }}</p>
                    @enderror
                </div>
            @endif

            @if ($acceptsLink)
                <div>
                    <label for="submission_link" class="ui-label">
                        Tautan pengumpulan
                        @if ($task->submission_type === \App\Models\Task::SUBMISSION_TYPE_LINK)
                            <span class="text-danger">*</span>
                        @endif
                    </label>
                    <input id="submission_link" name="submission_link" type="url"
                        value="{{ old('submission_link', $submission->submission_link) }}"
                        placeholder="https://contoh.com/hasil-tugas"
                        @error('submission_link') aria-invalid="true" @enderror
                        @class(['ui-control', 'border-danger focus:border-danger' => $errors->has('submission_link')])>
                    @error('submission_link')
                        <p role="alert" class="mt-2 text-sm text-danger">{{ $message }}</p>
                    @enderror
                </div>
            @endif

            @if ($task->submission_type === \App\Models\Task::SUBMISSION_TYPE_FILE_OR_LINK)
                <div class="rounded-xl border border-primary/15 bg-primary/5 px-4 py-3 text-sm leading-6 text-primary">
                    Unggah file atau isi tautan. Minimal salah satu wajib tersedia.
                </div>
            @endif
        </div>
    </fieldset>

    <fieldset class="ui-surface p-5 sm:p-6">
        <legend class="sr-only">Catatan tambahan</legend>
        <div class="ui-section-heading mb-6">
            <h2>Catatan tambahan</h2>
            <p>Sampaikan informasi yang perlu diketahui instruktur bila diperlukan.</p>
        </div>

        <label for="note" class="ui-label">
            Catatan <span class="font-normal text-secondary">(opsional)</span>
        </label>
        <textarea id="note" name="note" rows="5" placeholder="Tambahkan informasi yang perlu diketahui instruktur"
            @error('note') aria-invalid="true" @enderror
            @class(['ui-control resize-y', 'border-danger focus:border-danger' => $errors->has('note')])>{{ old('note', $submission->note) }}</textarea>
        @error('note')
            <p role="alert" class="mt-2 text-sm text-danger">{{ $message }}</p>
        @enderror
    </fieldset>

    <div class="ui-surface flex flex-col-reverse gap-3 p-4 sm:flex-row sm:items-center sm:justify-end">
        <a href="{{ $cancelUrl }}" class="ui-button ui-button-secondary">Batal</a>
        <button type="submit" class="ui-button ui-button-primary px-5">{{ $submitLabel }}</button>
    </div>
</form>
