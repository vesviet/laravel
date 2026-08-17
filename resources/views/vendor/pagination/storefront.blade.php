@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="text-sm text-[#888888] cursor-not-allowed">Trang trước</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="text-sm text-[#1a1a1a] hover:opacity-60 transition-opacity">Trang trước</a>
        @endif

        {{-- Pagination Elements --}}
        <div class="hidden sm:flex items-center gap-4">
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="text-sm text-[#888888]">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="text-sm text-[#1a1a1a] font-medium border-b border-[#1a1a1a] pb-0.5">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="text-sm text-[#888888] hover:text-[#1a1a1a] transition-colors pb-0.5">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach
        </div>

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="text-sm text-[#1a1a1a] hover:opacity-60 transition-opacity">Trang sau</a>
        @else
            <span class="text-sm text-[#888888] cursor-not-allowed">Trang sau</span>
        @endif
    </nav>
@endif
