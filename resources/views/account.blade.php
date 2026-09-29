@extends('layout', ['title' => __('Mon compte')])

@section('content')
<section>
    <div class="wrap stack" style="gap:28px">
        <div>
            <h1 style="font-size:clamp(2rem,5vw,3.2rem)">{{ __('Bonjour :name', ['name' => auth()->user()->name]) }}</h1>
            <p class="muted">{{ __('Cliente depuis le :date.', ['date' => auth()->user()->created_at->translatedFormat(__('j F Y'))]) }}</p>
            <div class="btn-row"><a class="btn" href="{{ route('booking') }}">{{ __('Réserver') }}</a><a class="btn ghost" href="{{ route('shop') }}#cabine">{{ __('Ma cabine') }}</a></div>
        </div>

        <div class="panel">
            <h2 style="font-size:1.7rem">{{ __('Mes prochains rendez-vous') }}</h2>
            @forelse ($upcoming as $b)
                @if ($loop->first)<ul class="list">@endif
                <li>
                    <div class="grow"><strong>{{ ucfirst($b->starts_at->translatedFormat(__('l j F à H:i'))) }}</strong><br><span class="muted">{{ __($b->service->name) }}</span></div>
                    <span class="status {{ $b->status }}">{{ $b->statusLabel() }}</span>
                    <form method="post" action="{{ route('booking.cancel', $b) }}" data-confirm="{{ __('Annuler ce rendez-vous ?') }}">@csrf<button class="linkish small">{{ __('Annuler') }}</button></form>
                </li>
                @if ($loop->last)</ul>@endif
            @empty
                <p class="muted" style="margin:0">{{ __('Aucun rendez-vous prévu.') }} <a href="{{ route('booking') }}">{{ __('Choisis un créneau') }}</a>.</p>
            @endforelse
        </div>

        <div class="panel">
            <h2 style="font-size:1.7rem">{{ __('Mes commandes') }}</h2>
            @forelse ($orders as $o)
                @if ($loop->first)<ul class="list">@endif
                <li>
                    <div class="grow"><strong>{{ $o->wig->name }}</strong><br><span class="muted">{{ number_format($o->price, 0, ',', ' ') }} RWF · {{ $o->created_at->translatedFormat(__('j F Y')) }}</span></div>
                    <span class="status {{ $o->status }}">{{ $o->statusLabel() }}</span>
                </li>
                @if ($loop->last)</ul>@endif
            @empty
                <p class="muted" style="margin:0">{{ __('Pas encore de commande.') }} <a href="{{ route('shop') }}">{{ __('Voir les perruques') }}</a>.</p>
            @endforelse
        </div>

        @if ($history->isNotEmpty())
            <div class="panel">
                <h2 style="font-size:1.7rem">{{ __('Mes visites passées') }}</h2>
                <ul class="list">
                    @foreach ($history as $b)
                        <li><div class="grow">{{ ucfirst($b->starts_at->translatedFormat(__('j F Y'))) }} · {{ __($b->service->name) }}</div><span class="status {{ $b->status }}">{{ $b->statusLabel() }}</span></li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</section>
@endsection
