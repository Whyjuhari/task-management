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
    <div role="alert" class="mb-6 rounded-xl border border-danger/20 bg-danger/5 px-4 py-3 text-sm text-danger">
        <p class="font-semibold">Pengumpulan belum dapat disimpan.</p>
        <p class="mt-1">Periksa kembali data yang ditandai di bawah ini.</p>
    </div>
@endif

<form method="POST" action="{{ $action }}" enctype="multipart/form-data"
    class="rounded-2xl border border-border bg-card p-5 shadow-sm sm:p-8">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="space-y-6">
        @if ($acceptsFile)
            <div>
                <label for="file" class="block text-sm font-semibold text-navy">
                    File pengumpulan
                    @if ($task->submission_type === \App\Models\Task::SUBMISSION_TYPE_FILE && ! $submission->file_path)
                        <span class="text-danger">*</span>
                    @endif
                </label>

                @if ($submission->file_path)
                    <p class="mt-1 text-sm text-secondary">
                        File saat ini: <span class="font-semibold text-navy">{{ $submission->original_file_name }}</span>
                    </p>
                @endif

                <input id="file" name="file" type="file" accept=".pdf,.doc,.docx,.zip,.png,.jpg,.jpeg"
                    class="mt-2 min-h-11 w-full rounded-xl border border-border bg-card px-3 py-2.5 text-sm text-navy file:mr-4 file:rounded-lg file:border-0 file:bg-primary/10 file:px-3 file:py-2 file:font-semibold file:text-primary hover:file:bg-primary/15 focus:outline-none focus:ring-3 focus:ring-primary/15">
                <p class="mt-2 text-xs leading-5 text-secondary">
                    Format: PDF, DOC, DOCX, ZIP, PNG, JPG, atau JPEG. Maksimal 5 MB.
                    @if ($submission->file_path)
                        Biarkan kosong jika file tidak ingin diganti.
                    @endif
                </p>
                @error('file')
                    <p class="mt-2 text-sm text-danger">{{ $message }}</p>
                @enderror
            </div>
        @endif

        @if ($acceptsLink)
            <div>
                <label for="submission_link" class="block text-sm font-semibold text-navy">
                    Tautan pengumpulan
                    @if ($task->submission_type === \App\Models\Task::SUBMISSION_TYPE_LINK)
                        <span class="text-danger">*</span>
                    @endif
                </label>
                <input id="submission_link" name="submission_link" type="url"
                    value="{{ old('submission_link', $submission->submission_link) }}"
                    placeholder="https://contoh.com/hasil-tugas"
                    class="mt-2 min-h-11 w-full rounded-xl border border-border bg-card px-4 py-3 text-sm text-navy placeholder:text-secondary/70 focus:border-primary focus:outline-none focus:ring-3 focus:ring-primary/15">
                @error('submission_link')
                    <p class="mt-2 text-sm text-danger">{{ $message }}</p>
                @enderror
            </div>
        @endif

        @if ($task->submission_type === \App\Models\Task::SUBMISSION_TYPE_FILE_OR_LINK)
            <p class="rounded-xl border border-primary/15 bg-primary/5 px-4 py-3 text-sm text-primary">
                Unggah file atau isi tautan. Minimal salah satu wajib tersedia.
            </p>
        @endif

        <div>
            <label for="note" class="block text-sm font-semibold text-navy">Catatan</label>
            <textarea id="note" name="note" rows="5" placeholder="Tambahkan informasi yang perlu diketahui instruktur"
                class="mt-2 w-full rounded-xl border border-border bg-card px-4 py-3 text-sm text-navy placeholder:text-secondary/70 focus:border-primary focus:outline-none focus:ring-3 focus:ring-primary/15">{{ old('note', $submission->note) }}</textarea>
            @error('note')
                <p class="mt-2 text-sm text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="mt-8 flex flex-col-reverse gap-3 border-t border-border pt-6 sm:flex-row sm:justify-end">
        <a href="{{ $cancelUrl }}"
            class="inline-flex min-h-11 items-center justify-center rounded-xl border border-border bg-card px-5 py-2.5 text-sm font-semibold text-navy transition hover:bg-slate-50 focus:outline-none focus:ring-3 focus:ring-primary/15">
            Batal
        </a>
        <button type="submit"
            class="inline-flex min-h-11 items-center justify-center rounded-xl bg-primary px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 focus:outline-none focus:ring-3 focus:ring-primary/25">
            {{ $submitLabel }}
        </button>
    </div>
</form>
