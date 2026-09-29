@if ($paginator->hasPages())
    <nav aria-label="Pages">
        <ul class="pagination">
            @if ($paginator->previousPageUrl())<li><a href="{{ $paginator->previousPageUrl() }}">Précédent</a></li>@endif
            <li class="muted">Page {{ $paginator->currentPage() }}</li>
            @if ($paginator->nextPageUrl())<li><a href="{{ $paginator->nextPageUrl() }}">Suivant</a></li>@endif
        </ul>
    </nav>
@endif
