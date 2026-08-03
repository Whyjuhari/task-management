@extends('layouts.app')

@section('title', $pageTitle)

@section('content')
    <x-page-header title="Data Peserta"
        description="Ringkasan pengumpulan tugas untuk seluruh peserta pelatihan." />

    @if ($participants->isEmpty())
        <x-empty-state title="Belum ada peserta"
            description="Data akan tampil setelah pengguna dengan role peserta tersedia." />
    @else
        <div class="hidden overflow-hidden rounded-2xl border border-border bg-card shadow-sm md:block">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[940px] text-left text-sm">
                    <thead class="border-b border-border bg-slate-50 text-xs uppercase tracking-wider text-secondary">
                        <tr>
                            <th scope="col" class="px-5 py-4 font-semibold">Nama</th>
                            <th scope="col" class="px-5 py-4 font-semibold">Email</th>
                            <th scope="col" class="px-5 py-4 text-center font-semibold">Jumlah Tugas</th>
                            <th scope="col" class="px-5 py-4 text-center font-semibold">Sudah Dikumpulkan</th>
                            <th scope="col" class="px-5 py-4 text-center font-semibold">Belum Dikumpulkan</th>
                            <th scope="col" class="px-5 py-4 text-center font-semibold">Terlambat</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @foreach ($participants as $participant)
                            @php($notSubmittedCount = max(0, $totalTasks - $participant->submitted_count - $participant->late_count))
                            <tr class="transition hover:bg-slate-50/70">
                                <td class="whitespace-nowrap px-5 py-4 font-semibold text-navy">{{ $participant->name }}</td>
                                <td class="whitespace-nowrap px-5 py-4 text-secondary">{{ $participant->email }}</td>
                                <td class="px-5 py-4 text-center font-semibold text-navy">{{ $totalTasks }}</td>
                                <td class="px-5 py-4 text-center font-semibold text-success">{{ $participant->submitted_count }}</td>
                                <td class="px-5 py-4 text-center font-semibold text-secondary">{{ $notSubmittedCount }}</td>
                                <td class="px-5 py-4 text-center font-semibold text-amber-700">{{ $participant->late_count }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="space-y-4 md:hidden">
            @foreach ($participants as $participant)
                @php($notSubmittedCount = max(0, $totalTasks - $participant->submitted_count - $participant->late_count))
                <article class="rounded-2xl border border-border bg-card p-5 shadow-sm">
                    <h2 class="font-semibold text-navy">{{ $participant->name }}</h2>
                    <p class="mt-1 break-all text-sm text-secondary">{{ $participant->email }}</p>

                    <dl class="mt-4 grid grid-cols-2 gap-3 border-t border-border pt-4 text-sm">
                        <div class="rounded-xl bg-background p-3">
                            <dt class="text-secondary">Jumlah tugas</dt>
                            <dd class="mt-1 text-xl font-bold text-navy">{{ $totalTasks }}</dd>
                        </div>
                        <div class="rounded-xl bg-success/5 p-3">
                            <dt class="text-secondary">Sudah dikumpulkan</dt>
                            <dd class="mt-1 text-xl font-bold text-success">{{ $participant->submitted_count }}</dd>
                        </div>
                        <div class="rounded-xl bg-background p-3">
                            <dt class="text-secondary">Belum dikumpulkan</dt>
                            <dd class="mt-1 text-xl font-bold text-secondary">{{ $notSubmittedCount }}</dd>
                        </div>
                        <div class="rounded-xl bg-warning/5 p-3">
                            <dt class="text-secondary">Terlambat</dt>
                            <dd class="mt-1 text-xl font-bold text-amber-700">{{ $participant->late_count }}</dd>
                        </div>
                    </dl>
                </article>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $participants->links() }}
        </div>
    @endif
@endsection
