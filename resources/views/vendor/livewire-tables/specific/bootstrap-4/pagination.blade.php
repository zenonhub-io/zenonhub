<div>
    @if ($paginator->hasPages())
        @php(isset($this->numberOfPaginatorsRendered[$paginator->getPageName()]) ? $this->numberOfPaginatorsRendered[$paginator->getPageName()]++ : $this->numberOfPaginatorsRendered[$paginator->getPageName()] = 1)

        <ul class="pagination justify-content-center justify-content-lg-end">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true">
                    <a class="page-link" href="#" tabindex="-1">
                        <i class="bi bi-chevron-left fs-6"></i>
                    </a>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link js-scroll" id="pagination-{{$paginator->getPageName()}}-previous" dusk="previousPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}" wire:click="previousPage('{{ $paginator->getPageName() }}')" rel="prev" aria-label="@lang('pagination.previous')">
                        <i class="bi bi-chevron-left fs-6"></i>
                    </a>
                </li>
            @endif

            @if($paginator->currentPage() > 3)
                <li class="page-item {{ ($paginator->currentPage() !== $paginator->lastPage() ? 'd-none d-sm-block' : '') }}" wire:key="paginator-{{ $paginator->getPageName() }}-{{ $this->numberOfPaginatorsRendered[$paginator->getPageName()] }}-page-1">
                    <a class="page-link js-scroll" id="pagination-{{$paginator->getPageName()}}-first" wire:click="gotoPage(1, '{{ $paginator->getPageName() }}')">1</a>
                </li>
            @endif
            @if($paginator->currentPage() > 4)
                <li class="page-item disabled">
                    <span class="page-link">...</span>
                </li>
            @endif

            @foreach(range(1, $paginator->lastPage()) as $page)
                @if($page >= $paginator->currentPage() - 1 && $page <= $paginator->currentPage() + 1)
                    @if ($page == $paginator->currentPage())
                        <li class="page-item active" wire:key="paginator-{{ $paginator->getPageName() }}-{{ $this->numberOfPaginatorsRendered[$paginator->getPageName()] }}-page-{{ $page }}" aria-current="page">
                            <span class="page-link">{{ number_format($page) }}</span>
                        </li>
                    @else
                        <li class="page-item {{ ($page === $paginator->currentPage() - 2 || $page === $paginator->currentPage() + 2 ? 'd-none d-md-block' : '') }}" wire:key="paginator-{{ $paginator->getPageName() }}-{{ $this->numberOfPaginatorsRendered[$paginator->getPageName()] }}-page-{{ $page }}">
                            <a class="page-link js-scroll" id="pagination-{{$paginator->getPageName()}}-page-{{ $page }}" wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')">{{ number_format($page) }}</a>
                        </li>
                    @endif
                @endif
            @endforeach

            @if($paginator->currentPage() < $paginator->lastPage() - 3)
                <li class="page-item disabled">
                    <span class="page-link">...</span>
                </li>
            @endif
            @if($paginator->currentPage() < $paginator->lastPage() - 2)
                <li class="page-item {{ ($paginator->currentPage() !== 1 ? 'd-none d-sm-block' : '') }}" wire:key="paginator-{{ $paginator->getPageName() }}-{{ $this->numberOfPaginatorsRendered[$paginator->getPageName()] }}-page-{{ $paginator->lastPage() }}">
                    <a class="page-link js-scroll" id="pagination-{{$paginator->getPageName()}}-last" wire:click="gotoPage({{$paginator->lastPage()}}, '{{ $paginator->getPageName() }}')">{{ number_format($paginator->lastPage()) }}</a>
                </li>
            @endif

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link js-scroll" id="pagination-{{$paginator->getPageName()}}-next" dusk="nextPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}" wire:click="nextPage('{{ $paginator->getPageName() }}')" rel="next" aria-label="@lang('pagination.next')">
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
    @endif
</div>
