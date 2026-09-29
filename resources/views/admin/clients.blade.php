@extends('layout', ['title' => 'Clientes'])

@section('content')
<div class="wrap">
    @include('admin.nav')
    <h1 style="font-size:2.4rem;margin-top:24px">Clientes</h1>
    <form method="get" class="inline" style="margin-bottom:18px;max-width:480px;width:100%">
        <input type="search" name="q" value="{{ $q }}" placeholder="Nom, téléphone ou email" aria-label="Rechercher une cliente" style="flex:1">
        <button class="btn small">Chercher</button>
    </form>
    <div class="panel table-wrap">
        <table>
            <thead><tr><th>Cliente</th><th>Téléphone</th><th>Inscrite le</th><th>Visites</th><th>Dernière visite</th><th>Commandes</th></tr></thead>
            <tbody>
            @forelse ($clients as $c)
                <tr>
                    <td><a href="{{ route('admin.client', $c) }}">{{ $c->name }}</a></td>
                    <td><a href="https://wa.me/{{ preg_replace('/\D/', '', $c->phone) }}">{{ $c->phone }}</a></td>
                    <td>{{ $c->created_at->translatedFormat('j M Y') }}</td>
                    <td>{{ $c->visits }}</td>
                    <td>{{ $c->last_visit ? \Carbon\Carbon::parse($c->last_visit)->translatedFormat('j M Y') : '—' }}</td>
                    <td>{{ $c->orders_count }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="muted">Aucune cliente trouvée.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $clients->links() }}
</div>
@endsection
