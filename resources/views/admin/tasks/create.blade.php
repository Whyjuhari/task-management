@extends('layouts.app')

@section('title', $pageTitle)

@section('content')
    <x-page-header title="Buat Tugas" description="Tambahkan tugas baru untuk peserta pelatihan." />

    <section class="rounded-2xl border border-border bg-card p-5 shadow-sm sm:p-8">
        @include('admin.tasks._form')
    </section>
@endsection
