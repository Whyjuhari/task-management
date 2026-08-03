@extends('layouts.app')

@section('title', $pageTitle)

@section('content')
    <x-page-header title="Edit Tugas" description="Perbarui informasi dan ketentuan tugas pelatihan." />

    <section class="mx-auto max-w-4xl">
        @include('admin.tasks._form', ['task' => $task])
    </section>
@endsection
