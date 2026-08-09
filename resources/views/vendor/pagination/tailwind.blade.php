@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination" class="flex flex-col items-center gap-3 sm:flex-row sm:items-center sm:justify-between">
        {{-- Info --}}
        <div class="text-sm text-pearl/50">
            Menampilkan
            @if ($paginator->firstItem())
                <span class="font-semibold text-pearl">{{ $paginator->firstItem() }}</span>–<span class="font-semibold text-pearl">{{ $paginator->lastItem() }}</span>
            @else
                {{ $paginator->count() }}
            @endif
            dari <span class="font-semibold text-pearl">{{ $paginator->total() }}</span> data
        </div>

        {{-- Tombol halaman --}}
        <div class="flex items-center gap-1.5">
            {{-- Prev --}}
            @if ($paginator->onFirstPage())
                <span class="inline-flex h-9 w-9 items-center justify-center rounded-2xl border border-white/10 text-pearl/30">
                    <x-icon name="chevron-left" class="h-4 w-4" />
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" data-page="{{ $paginator->currentPage() - 1 }}"
                    class="inline-flex h-9 w-9 items-center justify-center rounded-2xl border border-white/10 text-pearl/70 transition hover:bg-white/5 hover:text-azure-soft">
                    <x-icon name="chevron-left" class="h-4 w-4" />
                </a>
            @endif

            {{-- Nomor halaman --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="px-1 text-pearl/30">…</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="inline-flex h-9 min-w-9 items-center justify-center rounded-2xl bg-azure-soft px-2 text-sm font-bold text-navy-deep shadow-tactile">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" data-page="{{ $page }}"
                                class="inline-flex h-9 min-w-9 items-center justify-center rounded-2xl border border-white/10 px-2 text-sm text-pearl/70 transition hover:bg-white/5 hover:text-azure-soft">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" data-page="{{ $paginator->currentPage() + 1 }}"
                    class="inline-flex h-9 w-9 items-center justify-center rounded-2xl border border-white/10 text-pearl/70 transition hover:bg-white/5 hover:text-azure-soft">
                    <x-icon name="chevron-right" class="h-4 w-4" />
                </a>
            @else
                <span class="inline-flex h-9 w-9 items-center justify-center rounded-2xl border border-white/10 text-pearl/30">
                    <x-icon name="chevron-right" class="h-4 w-4" />
                </span>
            @endif
        </div>
    </nav>
@endif