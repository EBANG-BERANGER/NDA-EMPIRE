@extends('layout', ['title' => 'Mon compte'])

@section('content')
<section>
    <div class="wrap stack" style="gap:28px">
        <div>
            <h1 style="font-size:clamp(2rem,5vw,3.2rem)">Bonjour {{ auth()->user()->name }}</h1>
            <p class="muted">Cliente depuis le {{ auth()->user()->created_at->translatedFormat('j F Y') }}.</p>
            <div class="btn-row"><a class="btn" href="{{ route('booking') }}">Réserver</a><a class="btn ghost" href="{{ route('shop') }}#cabine">Ma cabine</a></div>
        </div>

        <div class="panel">
            <h2 style="font-size:1.7rem">Mes prochains rendez-vous</h2>
            @forelse ($upcoming as $b)
                @if ($loop->first)<ul class="list">@endif
                <li>
                    <div class="grow"><strong>{{ ucfirst($b->starts_at->translatedFormat('l j F à H:i')) }}</strong><br><span class="muted">{{ $b->service->name }}</span></div>
                    <span class="status {{ $b->status }}">{{ $b->statusLabel() }}</span>
                    <form method="post" action="{{ route('booking.cancel', $b) }}" data-confirm="Annuler ce rendez-vous ?">@csrf<button class="linkish small">Annuler</button></form>
                </li>
                @if ($loop->last)</ul>@endif
            @empty
                <p class="muted" style="margin:0">Aucun rendez-vous prévu. <a href="{{ route('booking') }}">Choisis un créneau</a>.</p>
            @endforelse
        </div>

        <div class="panel">
            <h2 style="font-size:1.7rem">Mes commandes</h2>
            @forelse ($orders as $o)
                @if ($loop->first)<ul class="list">@endif
                <li>
                    <div class="grow"><strong>{{ $o->wig->name }}</strong><br><span class="muted">{{ number_format($o->price, 0, ',', ' ') }} RWF · {{ $o->created_at->translatedFormat('j F Y') }}</span></div>
                    <span class="status {{ $o->status }}">{{ $o->statusLabel() }}</span>
                </li>
                @if ($loop->last)</ul>@endif
            @empty
                <p class="muted" style="margin:0">Pas encore de commande. <a href="{{ route('shop') }}">Voir les perruques</a>.</p>
            @endforelse
        </div>

        @if ($history->isNotEmpty())
            <div class="panel">
                <h2 style="font-size:1.7rem">Mes visites passées</h2>
                <ul class="list">
                    @foreach ($history as $b)
                        <li><div class="grow">{{ ucfirst($b->starts_at->translatedFormat('j F Y')) }} · {{ $b->service->name }}</div><span class="status {{ $b->status }}">{{ $b->statusLabel() }}</span></li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</section>
@endsection
