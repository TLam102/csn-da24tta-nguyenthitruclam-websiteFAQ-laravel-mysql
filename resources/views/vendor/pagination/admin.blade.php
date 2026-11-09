@if ($paginator->hasPages())
    <nav class="adm-pager" role="navigation" aria-label="Phân trang">
        <ul class="adm-pager-list">
            {{-- Trang trước (chỉ hiện khi không phải trang đầu) --}}
            @unless ($paginator->onFirstPage())
                <li>
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Trang trước">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                    </a>
                </li>
            @endunless

            {{-- Các số trang --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <li class="dots"><span>{{ $element }}</span></li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="active" aria-current="page"><span>{{ $page }}</span></li>
                        @else
                            <li><a href="{{ $url }}" aria-label="Trang {{ $page }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Trang sau (chỉ hiện khi còn trang) --}}
            @if ($paginator->hasMorePages())
                <li>
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Trang sau">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                    </a>
                </li>
            @endif
        </ul>
    </nav>
@endif