@extends('layouts.app')

@section('title', $pageTitle)

@section('content')
    <x-page-header title="Edit Tugas" description="Perbarui informasi dan ketentuan tugas pelatihan." />

    <section class="rounded-2xl border border-border bg-card p-5 shadow-sm sm:p-8">
        @include('admin.tasks._form', ['task' => $task])
    </section>
@endsection
