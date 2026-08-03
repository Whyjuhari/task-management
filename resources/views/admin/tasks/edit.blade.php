@extends('layouts.app')

@section('title', $pageTitle)

@section('content')
    <x-page-header title="Edit Tugas" description="Perbarui informasi dan ketentuan tugas pelatihan." />

    <section class="mx-auto max-w-5xl rounded-xl border border-border bg-card p-5 shadow-sm sm:p-8">
        @include('admin.tasks._form', ['task' => $task])
    </section>
@endsection
