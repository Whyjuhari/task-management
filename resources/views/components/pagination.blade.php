@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi halaman"
        class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <p class="text-sm text-secondary">
            Menampilkan
            <span class="font-semibold text-navy">{{ $paginator->firstItem() ?? 0 }}</span>
            sampai
            <span class="font-semibold text-navy">{{ $paginator->lastItem() ?? 0 }}</span>
            dari
            <span class="font-semibold text-navy">{{ $paginator->total() }}</span>
            data
        </p>

        <div class="flex flex-wrap items-center gap-2">
            @if ($paginator->onFirstPage())
                <span aria-disabled="true"
                    class="inline-flex min-h-11 items-center justify-center rounded-lg border border-border bg-slate-50 px-4 text-sm font-semibold text-secondary opacity-70">
                    Sebelumnya
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                    class="inline-flex min-h-11 items-center justify-center rounded-lg border border-border bg-card px-4 text-sm font-semibold text-navy transition hover:bg-slate-50 focus:outline-none focus:ring-3 focus:ring-primary/20">
                    Sebelumnya
                </a>
            @endif

            <div class="hidden items-center gap-1 sm:flex">
                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span class="inline-flex min-h-11 min-w-11 items-center justify-center px-2 text-sm text-secondary">
                            {{ $element }}
                        </span>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page === $paginator->currentPage())
                                <span aria-current="page"
                                    class="inline-flex min-h-11 min-w-11 items-center justify-center rounded-lg bg-primary px-3 text-sm font-semibold text-white">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}" aria-label="Buka halaman {{ $page }}"
                                    class="inline-flex min-h-11 min-w-11 items-center justify-center rounded-lg border border-border bg-card px-3 text-sm font-semibold text-navy transition hover:bg-slate-50 focus:outline-none focus:ring-3 focus:ring-primary/20">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </div>

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                    class="inline-flex min-h-11 items-center justify-center rounded-lg border border-border bg-card px-4 text-sm font-semibold text-navy transition hover:bg-slate-50 focus:outline-none focus:ring-3 focus:ring-primary/20">
                    Berikutnya
                </a>
            @else
                <span aria-disabled="true"
                    class="inline-flex min-h-11 items-center justify-center rounded-lg border border-border bg-slate-50 px-4 text-sm font-semibold text-secondary opacity-70">
                    Berikutnya
                </span>
            @endif
        </div>
    </nav>
@endif
