@if ($paginator->hasPages())
    <nav>
        <ul class="pagination d-flex justify-content-between">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link">
                        <i class="bi bi-chevron-left me-2 fs-6"></i>
                        Previous
                    </span>
                </li>
            @else
                @if (method_exists($paginator,'getCursorName'))
                    <li class="page-item">
                        <button dusk="previousPage" type="button" class="page-link" id="pagination-{{$paginator->getPageName()}}-previous" wire:click="setPage('{{$paginator->previousCursor()->encode()}}','{{ $paginator->getCursorName() }}')" wire:loading.attr="disabled" rel="prev">
                            <i class="bi bi-chevron-left me-2 fs-6"></i>
                            Previous
                        </button>
                    </li>
                @else
                    <li class="page-item">
                        <button type="button" dusk="previousPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}" class="page-link" id="pagination-{{$paginator->getPageName()}}-previous" wire:click="previousPage('{{ $paginator->getPageName() }}')" wire:loading.attr="disabled" rel="prev">
                            <i class="bi bi-chevron-left me-2 fs-6"></i>
                            Previous
                        </button>
                    </li>
                @endif
            @endif

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                @if (method_exists($paginator,'getCursorName'))
                    <li class="page-item">
                        <button dusk="nextPage" type="button" class="page-link" id="pagination-{{$paginator->getPageName()}}-next" wire:click="setPage('{{$paginator->nextCursor()->encode()}}','{{ $paginator->getCursorName() }}')" wire:loading.attr="disabled" rel="next">
                            Next
                            <i class="bi bi-chevron-right ms-2 fs-6"></i>
                        </button>
                    </li>
                @else
                    <li class="page-item">
                        <button type="button" dusk="nextPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}" class="page-link" id="pagination-{{$paginator->getPageName()}}-next" wire:click="nextPage('{{ $paginator->getPageName() }}')" wire:loading.attr="disabled" rel="next">
                            Next
                            <i class="bi bi-chevron-right ms-2 fs-6"></i>
                        </button>
                    </li>
                @endif
            @else
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link">
                        Next
                        <i class="bi bi-chevron-right ms-2 fs-6"></i>
                    </span>
                </li>
            @endif
        </ul>
    </nav>
@endif
