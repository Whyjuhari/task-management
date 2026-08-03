@php($statusFormId = 'task-status-'.$context.'-'.$task->id)

<form method="POST" action="{{ route('admin.tasks.status', $task) }}" class="flex flex-wrap items-center gap-2">
    @csrf
    @method('PATCH')

    <label for="{{ $statusFormId }}" class="sr-only">Status {{ $task->title }}</label>
    <select id="{{ $statusFormId }}" name="status"
        class="ui-control min-w-32">
        <option value="draft" @selected($task->status === 'draft')>Draf</option>
        <option value="active" @selected($task->status === 'active')>Aktif</option>
        <option value="closed" @selected($task->status === 'closed')>Ditutup</option>
    </select>
    <button type="submit"
        class="ui-button border-primary px-3 text-primary hover:bg-primary hover:text-white">
        Ubah
    </button>
</form>
