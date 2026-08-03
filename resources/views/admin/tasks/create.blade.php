@extends('layouts.app')

@section('title', $pageTitle)

@section('content')

    <x-page-header title="Buat Tugas" description="Tambahkan tugas baru untuk peserta pelatihan." />
    <section class="mx-auto max-w-4xl shadow-2xl">
        @include('admin.tasks._form')
    </section>
@endsection
