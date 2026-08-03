@extends('layouts.app')

@section('title', $pageTitle)

@section('content')
    <x-page-header :title="$pageTitle" :description="$description" />

    <section class="overflow-hidden rounded-2xl border border-border bg-card shadow-sm">
        <div
            class="border-b border-border bg-linear-to-r from-primary/10 via-primary/5 to-transparent px-5 py-6 sm:px-8 sm:py-8">
            <p class="text-sm font-semibold text-primary">Selamat datang kembali</p>
            <h2 class="mt-2 text-2xl font-semibold tracking-tight text-navy">
                {{ auth()->user()->name }}
            </h2>
            <p class="mt-3 max-w-2xl text-sm leading-6 text-secondary sm:text-base">
                Gunakan navigasi di samping untuk berpindah halaman.
            </p>
        </div>

        <div class="grid gap-4 p-5 sm:grid-cols-2 sm:p-8">
            <div class="rounded-xl border border-border bg-background p-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-secondary">Email</p>
                <p class="mt-2 break-all text-sm font-semibold text-navy">{{ auth()->user()->email }}</p>
            </div>
            <div class="rounded-xl border border-border bg-background p-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-secondary">Peran</p>
                <p class="mt-2 text-sm font-semibold text-navy">
                    {{ auth()->user()->role === \App\Models\User::ROLE_ADMIN ? 'Admin / Instruktur' : 'Peserta' }}
                </p>
            </div>
        </div>
    </section>
@endsection
