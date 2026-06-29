@php
    $pageName = method_exists($paginator, 'getPageName') ? $paginator->getPageName() : 'page';
@endphp

@once
    <style>
            .shadcn-pagination {
                display: flex;
                justify-content: center;
                width: 100%;
            }

            .shadcn-pagination ul {
                display: flex;
                flex-direction: row;
                align-items: center;
                gap: 0.25rem;
                list-style: none;
                margin: 0;
                padding: 0;
            }

            .shadcn-pagination .pg-link,
            .shadcn-pagination .pg-ellipsis {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 0.25rem;
                min-width: 2.25rem;
                height: 2.25rem;
                padding: 0 0.625rem;
                border-radius: 0.5rem;
                border: 1px solid transparent;
                background: transparent;
                color: #122818;
                font-size: 0.875rem;
                font-weight: 500;
                line-height: 1;
                text-decoration: none;
                cursor: pointer;
                user-select: none;
                transition:
                    background-color 0.15s ease,
                    border-color 0.15s ease,
                    color 0.15s ease;
            }

            .shadcn-pagination .pg-link:hover {
                background-color: #f1f3f1;
                border-color: #e6e9e6;
            }

            .shadcn-pagination .pg-link.is-active {
                background-color: #122818;
                border-color: #122818;
                color: #ffffff;
            }

            .shadcn-pagination .pg-link.is-active:hover {
                background-color: #122818;
                color: #ffffff;
            }

            .shadcn-pagination .pg-link.is-disabled {
                opacity: 0.45;
                pointer-events: none;
            }

            .shadcn-pagination .pg-ellipsis {
                cursor: default;
                color: #6b7280;
            }

            .shadcn-pagination .pg-edge {
                padding: 0 0.75rem;
            }

            .shadcn-pagination .pg-edge svg {
                width: 1rem;
                height: 1rem;
            }

            @media (max-width: 480px) {
                .shadcn-pagination .pg-edge .pg-edge-label {
                    display: none;
                }
            }
    </style>
@endonce

@if ($paginator->hasPages())
    <nav class="shadcn-pagination" role="navigation" aria-label="Pagination" dir="ltr"
        wire:loading.class="opacity-50 pe-none">
        <ul>
            {{-- Previous --}}
            <li>
                @if ($paginator->onFirstPage())
                    <span class="pg-link pg-edge is-disabled" aria-disabled="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6" /></svg>
                        <span class="pg-edge-label">@lang('pagination.previous')</span>
                    </span>
                @else
                    <button type="button" class="pg-link pg-edge"
                        wire:click.prevent="previousPage('{{ $pageName }}')" rel="prev"
                        aria-label="@lang('pagination.previous')">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6" /></svg>
                        <span class="pg-edge-label">@lang('pagination.previous')</span>
                    </button>
                @endif
            </li>

            {{-- Page numbers --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <li><span class="pg-ellipsis">{{ $element }}</span></li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        <li>
                            @if ($page == $paginator->currentPage())
                                <span class="pg-link is-active" aria-current="page">{{ $page }}</span>
                            @else
                                <button type="button" class="pg-link"
                                    wire:click.prevent="gotoPage({{ $page }}, '{{ $pageName }}')">{{ $page }}</button>
                            @endif
                        </li>
                    @endforeach
                @endif
            @endforeach

            {{-- Next --}}
            <li>
                @if ($paginator->hasMorePages())
                    <button type="button" class="pg-link pg-edge"
                        wire:click.prevent="nextPage('{{ $pageName }}')" rel="next"
                        aria-label="@lang('pagination.next')">
                        <span class="pg-edge-label">@lang('pagination.next')</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6" /></svg>
                    </button>
                @else
                    <span class="pg-link pg-edge is-disabled" aria-disabled="true">
                        <span class="pg-edge-label">@lang('pagination.next')</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6" /></svg>
                    </span>
                @endif
            </li>
        </ul>
    </nav>
@endif
