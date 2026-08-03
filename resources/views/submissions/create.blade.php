@extends('layouts.app')

@section('title', $pageTitle)

@section('content')
    <x-page-header title="Kumpulkan Tugas" :description="$task->title">
        <a href="{{ route('tasks.show', $task) }}"
            class="ui-button ui-button-secondary">
            Kembali ke Detail Tugas
        </a>
    </x-page-header>

    @if ($task->deadline->isPast())
        <div role="status" class="mb-6 rounded-xl border border-warning/25 bg-warning/10 px-4 py-3 text-sm text-amber-800">
            Deadline telah berakhir. Pengumpulan ini akan tercatat sebagai terlambat.
        </div>
    @endif

    @include('submissions._form', [
        'action' => route('submissions.store', $task),
        'method' => 'POST',
        'submitLabel' => 'Kirim Pengumpulan',
        'cancelUrl' => route('tasks.show', $task),
    ])
@endsection
