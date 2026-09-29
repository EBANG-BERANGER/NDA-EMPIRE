<nav class="admin-nav" aria-label="Gestion">
    @foreach (['admin' => 'Agenda', 'admin.clients' => 'Clientes', 'admin.wigs' => 'Perruques', 'admin.services' => 'Prestations', 'admin.portfolio' => 'Réalisations'] as $r => $label)
        <a href="{{ route($r) }}" @if(request()->routeIs($r) || ($r === 'admin.clients' && request()->routeIs('admin.client'))) aria-current="page" @endif>{{ $label }}</a>
    @endforeach
</nav>
