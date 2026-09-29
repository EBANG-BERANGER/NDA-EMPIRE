@extends('layout', ['title' => 'Gestion'])

@php
    $bookingActions = fn ($b) => match ($b->status) {
        'pending' => ['confirmed' => 'Confirmer', 'cancelled' => 'Refuser'],
        'confirmed' => ['done' => 'Venue', 'no_show' => 'Absente', 'cancelled' => 'Annuler'],
        default => [],
    };
@endphp

@section('content')
<div class="wrap">
    @include('admin.nav')

    <div class="stats" style="margin:28px 0">
        @foreach ($stats as $label => $value)<div><strong>{{ $value }}</strong>{{ $label }}</div>@endforeach
    </div>

    <div class="stack" style="gap:24px">
        <div class="panel">
            <h2 style="font-size:1.7rem">À confirmer</h2>
            @forelse ($pending as $b)
                @if ($loop->first)<ul class="list">@endif
                @include('admin.booking-row', ['b' => $b, 'actions' => $bookingActions($b)])
                @if ($loop->last)</ul>@endif
            @empty
                <p class="muted" style="margin:0">Aucune demande en attente.</p>
            @endforelse
        </div>

        @if ($toClose->isNotEmpty())
            <div class="panel">
                <h2 style="font-size:1.7rem">Rendez-vous passés à clôturer</h2>
                <p class="muted small">Indique si la cliente est venue : c'est ce qui compte ses visites.</p>
                <ul class="list">
                    @foreach ($toClose as $b)@include('admin.booking-row', ['b' => $b, 'actions' => $bookingActions($b)])@endforeach
                </ul>
            </div>
        @endif

        <div class="panel">
            <h2 style="font-size:1.7rem">Agenda des 14 prochains jours</h2>
            @forelse ($agenda as $day => $bookings)
                <h3 style="margin:18px 0 0;font-size:1.15rem">{{ ucfirst(\Carbon\CarbonImmutable::parse($day)->translatedFormat('l j F')) }}</h3>
                <ul class="list">
                    @foreach ($bookings as $b)@include('admin.booking-row', ['b' => $b, 'actions' => $bookingActions($b)])@endforeach
                </ul>
            @empty
                <p class="muted" style="margin:0">Aucun rendez-vous confirmé pour l'instant.</p>
            @endforelse
        </div>

        <div class="panel">
            <h2 style="font-size:1.7rem">Commandes en cours</h2>
            @forelse ($orders as $o)
                @if ($loop->first)<ul class="list">@endif
                <li>
                    <div class="grow">
                        <strong>{{ $o->wig->name }}</strong> · {{ number_format($o->price, 0, ',', ' ') }} RWF<br>
                        <a href="{{ route('admin.client', $o->user) }}">{{ $o->user->name }}</a> · <a href="https://wa.me/{{ preg_replace('/\D/', '', $o->user->phone) }}">{{ $o->user->phone }}</a>
                        @if ($o->note)<br><span class="muted small">« {{ $o->note }} »</span>@endif
                    </div>
                    <span class="status {{ $o->status }}">{{ $o->statusLabel() }}</span>
                    <form method="post" action="{{ route('admin.order', $o) }}" class="inline">@csrf @method('patch')
                        @if ($o->status === 'pending')<button class="btn small" name="status" value="ready">Prête</button>@endif
                        <button class="btn small ghost" name="status" value="collected">Récupérée</button>
                        <button class="btn small danger" name="status" value="cancelled">Annuler</button>
                    </form>
                </li>
                @if ($loop->last)</ul>@endif
            @empty
                <p class="muted" style="margin:0">Aucune commande en cours.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
