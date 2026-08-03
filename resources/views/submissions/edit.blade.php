@extends('layouts.app')

@section('title', $pageTitle)

@section('content')
    <x-page-header title="Perbarui Pengumpulan" :description="$task->title">
        <a href="{{ route('submissions.show', $submission) }}"
            class="ui-button ui-button-secondary">
            Kembali ke Detail Pengumpulan
        </a>
    </x-page-header>

    @if ($task->deadline->isPast())
        <div role="status" class="mb-6 rounded-xl border border-warning/25 bg-warning/10 px-4 py-3 text-sm text-amber-800">
            Deadline telah berakhir. Waktu pengumpulan akan diperbarui dan status menjadi terlambat.
        </div>
    @endif

    @include('submissions._form', [
        'action' => route('submissions.update', $submission),
        'method' => 'PUT',
        'submitLabel' => 'Simpan Perubahan',
        'cancelUrl' => route('submissions.show', $submission),
    ])
@endsection
