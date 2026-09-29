@if ($paginator->hasPages())
    <nav aria-label="{{ __('Pages') }}">
        <ul class="pagination">
            @if ($paginator->previousPageUrl())<li><a href="{{ $paginator->previousPageUrl() }}">{{ __('Précédent') }}</a></li>@endif
            <li class="muted">{{ __('Page :n', ['n' => $paginator->currentPage()]) }}</li>
            @if ($paginator->nextPageUrl())<li><a href="{{ $paginator->nextPageUrl() }}">{{ __('Suivant') }}</a></li>@endif
        </ul>
    </nav>
@endif
