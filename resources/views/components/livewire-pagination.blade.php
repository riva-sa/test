@php
$pageName = method_exists($paginator, 'getPageName') ? $paginator->getPageName() : 'page';
$isLengthAware = method_exists($paginator, 'lastPage');
$currentPage = method_exists($paginator, 'currentPage') ? $paginator->currentPage() : null;
$lastPage = $isLengthAware ? $paginator->lastPage() : null;
// نافذة ترقيم بسيطة حول الصفحة الحالية
$window = 2;
@endphp
<nav aria-label="Pagination">
        <ul class="pagination justify-content-center" wire:loading.class="opacity-50 pe-none">
            <li class="page-item {{ $paginator->onFirstPage() ? 'disabled' : '' }}">
                @php
                    $prevUrl = $isLengthAware
                        ? ($currentPage && $currentPage > 1 ? $paginator->url($currentPage - 1) : null)
                        : (method_exists($paginator, 'previousPageUrl') ? $paginator->previousPageUrl() : null);
                    $prevHref = $prevUrl ?? request()->fullUrlWithQuery([$pageName => max(1, ($currentPage ?? 2) - 1)]);
                @endphp
                <a href="{{ $prevHref }}" class="page-link lw-prev-link" wire:click.prevent="previousPage('{{ $pageName }}')" aria-disabled="{{ $paginator->onFirstPage() ? 'true' : 'false' }}" aria-label="السابق">السابق</a>
            </li>
            @if ($isLengthAware && $currentPage && $lastPage)
                @php
                    $start = max(1, $currentPage - $window);
                    $end = min($lastPage, $currentPage + $window);
                @endphp
                @if ($start > 1)
                    <li class="page-item">
                        <a href="{{ $paginator->url(1) }}" class="page-link lw-page-link" wire:click.prevent="gotoPage(1, '{{ $pageName }}')">1</a>
                    </li>
                    @if ($start > 2)
                        <li class="page-item disabled" aria-disabled="true"><span class="page-link">…</span></li>
                    @endif
                @endif
                @for ($page = $start; $page <= $end; $page++)
                    @if ($page == $currentPage)
                        <li class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span></li>
                    @else
                        <li class="page-item">
                            <a href="{{ $paginator->url($page) }}" class="page-link lw-page-link" wire:click.prevent="gotoPage({{ $page }}, '{{ $pageName }}')">{{ $page }}</a>
                        </li>
                    @endif
                @endfor
                @if ($end < $lastPage)
                    @if ($end < $lastPage - 1)
                        <li class="page-item disabled" aria-disabled="true"><span class="page-link">…</span></li>
                    @endif
                    <li class="page-item">
                        <a href="{{ $paginator->url($lastPage) }}" class="page-link lw-page-link" wire:click.prevent="gotoPage({{ $lastPage }}, '{{ $pageName }}')">{{ $lastPage }}</a>
                    </li>
                @endif
            @endif
            <li class="page-item {{ $paginator->hasMorePages() ? '' : 'disabled' }}">
                @php
                    $nextUrl = $isLengthAware
                        ? ($currentPage && $currentPage < $lastPage ? $paginator->url($currentPage + 1) : null)
                        : (method_exists($paginator, 'nextPageUrl') ? $paginator->nextPageUrl() : null);
                    $nextHref = $nextUrl ?? request()->fullUrlWithQuery([$pageName => ($currentPage ?? 1) + 1]);
                @endphp
                <a href="{{ $nextHref }}" class="page-link lw-next-link" wire:click.prevent="nextPage('{{ $pageName }}')" aria-disabled="{{ $paginator->hasMorePages() ? 'false' : 'true' }}" aria-label="التالي">التالي</a>
            </li>
        </ul>
        <div class="text-center mt-2" wire:loading>
            <div class="spinner-border spinner-border-sm" role="status"><span class="visually-hidden">Loading...</span></div>
        </div>
    </nav>
    <script>
        (function () {
            var pageName = '{{ $pageName }}';
            document.addEventListener('click', function (e) {
                var link = e.target.closest('.lw-page-link, .lw-prev-link, .lw-next-link');
                if (!link) return;
                e.preventDefault();
                var href = link.getAttribute('href');
                if (href) {
                    try {
                        history.pushState({}, '', href);
                    } catch (err) {
                        console.error('History update failed', err);
                    }
                }
            }, true);
            window.addEventListener('popstate', function () {
                try {
                    var params = new URLSearchParams(location.search);
                    var p = params.get(pageName);
                    if (p) {
                        @this.setPage(Number(p), pageName);
                    }
                } catch (err) {
                    console.error('Popstate handling failed', err);
                }
            });
        })();
    </script>
