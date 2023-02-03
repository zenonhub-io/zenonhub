<div class="w-100">
    <nav class="align-items-center p-1 bg-secondary rounded-2">
        @php(isset($this->numberOfPaginatorsRendered[$paginator->getPageName()]) ? $this->numberOfPaginatorsRendered[$paginator->getPageName()]++ : $this->numberOfPaginatorsRendered[$paginator->getPageName()] = 1)
        <ul class="pagination justify-content-between">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true">
                    <a class="page-link" href="#" tabindex="-1">
                        <i class="bi bi-chevron-left fs-6"></i>
                    </a>
                </li>
            @else
                <li class="page-item">
                    <a
                        class="page-link"
                        id="top-pagination-{{$paginator->getPageName()}}-previous"
                        dusk="previousPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}"
                        wire:click="previousPage('{{ $paginator->getPageName() }}')"
                        rel="prev"
                        aria-label="@lang('pagination.previous')">
                        <i class="bi bi-chevron-left fs-6"></i>
                    </a>
                </li>
            @endif
            <li class="page-item">
                <a class="page-link disabled">
                <span class="d-inline fw-light">
                    @if (! $paginator->total())
                        No results
                    @else
                        Page {{ number_format($paginator->currentPage()) }} of {{ number_format($paginator->lastPage()) }}
                    @endif
                </span>
                </a>
            </li>
            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a
                        class="page-link"
                        id="top-pagination-{{$paginator->getPageName()}}-next"
                        dusk="nextPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}"
                        wire:click="nextPage('{{ $paginator->getPageName() }}')"
                        rel="next"
                        aria-label="@lang('pagination.next')">
                        <i class="bi bi-chevron-right fs-6"></i>
                    </a>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                <span class="page-link" aria-hidden="true">
                    <i class="bi bi-chevron-right fs-6"></i>
                </span>
                </li>
            @endif
        </ul>
    </nav>
</div>
