@php($statusFormId = 'task-status-'.$context.'-'.$task->id)

<form method="POST" action="{{ route('admin.tasks.status', $task) }}" class="flex flex-wrap items-center gap-2">
    @csrf
    @method('PATCH')

    <label for="{{ $statusFormId }}" class="sr-only">Status {{ $task->title }}</label>
    <select id="{{ $statusFormId }}" name="status"
        class="min-h-11 min-w-32 rounded-lg border border-border bg-card px-3 py-2 text-sm text-navy outline-none focus:border-primary focus:ring-3 focus:ring-primary/15">
        <option value="draft" @selected($task->status === 'draft')>Draf</option>
        <option value="active" @selected($task->status === 'active')>Aktif</option>
        <option value="closed" @selected($task->status === 'closed')>Ditutup</option>
    </select>
    <button type="submit"
        class="min-h-11 cursor-pointer rounded-lg border border-primary px-3 py-2 text-sm font-semibold text-primary transition hover:bg-primary hover:text-white focus:outline-none focus:ring-3 focus:ring-primary/20">
        Ubah
    </button>
</form>
