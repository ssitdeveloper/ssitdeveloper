@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" style="display: flex; justify-content: center; align-items: center; margin: var(--spacing-4) 0;">
        <div style="display: flex; gap: var(--spacing-1); flex-wrap: wrap; justify-content: center;">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span style="padding: 6px 12px; background-color: var(--color-gray-100); color: var(--color-gray-400); border-radius: var(--radius-lg); cursor: not-allowed; font-size: 12px; font-weight: var(--font-weight-medium);">
                    <span style="font-size: 10px;">‹</span> Prev
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" style="padding: 6px 12px; background-color: var(--color-primary); color: white; border-radius: var(--radius-lg); text-decoration: none; cursor: pointer; font-size: 12px; font-weight: var(--font-weight-medium); transition: all var(--transition-fast);"
                   onmouseover="this.style.backgroundColor='#0d47a1'"
                   onmouseout="this.style.backgroundColor='var(--color-primary)'">
                    <span style="font-size: 10px;">‹</span> Prev
                </a>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span style="padding: 6px 8px; color: var(--color-gray-500); font-size: 12px;">{{ $element }}</span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span style="padding: 6px 10px; background-color: var(--color-primary); color: white; border-radius: var(--radius-lg); font-size: 12px; font-weight: var(--font-weight-bold); min-width: 32px; text-align: center; display: inline-flex; align-items: center; justify-content: center;">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" style="padding: 6px 10px; background-color: var(--color-gray-100); color: var(--color-primary); border-radius: var(--radius-lg); text-decoration: none; font-size: 12px; font-weight: var(--font-weight-medium); transition: all var(--transition-fast); min-width: 32px; text-align: center; display: inline-flex; align-items: center; justify-content: center;"
                               onmouseover="this.style.backgroundColor='var(--color-primary)'; this.style.color='white'"
                               onmouseout="this.style.backgroundColor='var(--color-gray-100)'; this.style.color='var(--color-primary)'">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" style="padding: 6px 12px; background-color: var(--color-primary); color: white; border-radius: var(--radius-lg); text-decoration: none; cursor: pointer; font-size: 12px; font-weight: var(--font-weight-medium); transition: all var(--transition-fast);"
                   onmouseover="this.style.backgroundColor='#0d47a1'"
                   onmouseout="this.style.backgroundColor='var(--color-primary)'">
                    Next <span style="font-size: 10px;">›</span>
                </a>
            @else
                <span style="padding: 6px 12px; background-color: var(--color-gray-100); color: var(--color-gray-400); border-radius: var(--radius-lg); cursor: not-allowed; font-size: 12px; font-weight: var(--font-weight-medium);">
                    Next <span style="font-size: 10px;">›</span>
                </span>
            @endif
        </div>
    </nav>
@endif
