@extends('layouts.app')

@section('title', $pageTitle)

@section('content')
    <x-page-header :title="$pageTitle" :description="$description" />

    <x-empty-state title="Fitur sedang disiapkan"
        description="Halaman ini sengaja masih berupa placeholder. Fungsinya akan dibuat pada tahap berikutnya sesuai kebutuhan proyek.">
        <x-status-badge status="draft" label="Belum tersedia" />
    </x-empty-state>
@endsection
