@if ($paginator->hasPages())
<nav class="pagination" role="navigation" aria-label="Pagination">

    {{-- Previous Page Link --}}
    @if ($paginator->onFirstPage())
        <span aria-disabled="true">‹</span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Previous">‹</a>
    @endif

    {{-- Page Numbers --}}
    @foreach ($elements as $element)
        {{-- "Three Dots" Separator --}}
        @if (is_string($element))
            <span aria-disabled="true">{{ $element }}</span>
        @endif

        {{-- Array Of Links --}}
        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span class="active" aria-current="page">{{ $page }}</span>
                @else
                    <a href="{{ $url }}">{{ $page }}</a>
                @endif
            @endforeach
        @endif
    @endforeach

    {{-- Next Page Link --}}
    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Next">›</a>
    @else
        <span aria-disabled="true">›</span>
    @endif

</nav>
@endif
