@props([
    'paginator',
    'ariaLabel' => 'Pagination',
    'containerClass' => 'mt-4',
    'alignClass' => 'justify-content-center',
    'prevLabel' => 'Trước',
    'nextLabel' => 'Sau',
])

@php
    $isPaginator = $paginator instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator;
    $hasPages = $isPaginator && $paginator->lastPage() > 1;
    $navClasses = trim('pagination-nav ' . ($containerClass ?? ''));
@endphp

@if ($hasPages)
    <nav aria-label="{{ $ariaLabel }}" class="{{ $navClasses }}">
        <ul class="pagination {{ $alignClass }}">
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link">{{ $prevLabel }}</span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">{{ $prevLabel }}</a>
                </li>
            @endif

            @for ($page = 1; $page <= $paginator->lastPage(); $page++)
                @php $isCurrent = $page === $paginator->currentPage(); @endphp
                <li class="page-item {{ $isCurrent ? 'active' : '' }}" @if($isCurrent) aria-current="page" @endif>
                    @if ($isCurrent)
                        <span class="page-link">{{ $page }}</span>
                    @else
                        <a class="page-link" href="{{ $paginator->url($page) }}">{{ $page }}</a>
                    @endif
                </li>
            @endfor

            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">{{ $nextLabel }}</a>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link">{{ $nextLabel }}</span>
                </li>
            @endif
        </ul>
    </nav>
@endif
