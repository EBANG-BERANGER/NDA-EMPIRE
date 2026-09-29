@extends('layout', ['title' => $client->name])

@section('content')
<div class="wrap stack" style="gap:24px">
    @include('admin.nav')
    <div>
        <h1 style="font-size:2.4rem;margin:0">{{ $client->name }}</h1>
        <p class="muted">Inscrite le {{ $client->created_at->translatedFormat('j F Y') }} ·
            <a href="https://wa.me/{{ preg_replace('/\D/', '', $client->phone) }}">{{ $client->phone }}</a> ·
            <a href="mailto:{{ $client->email }}">{{ $client->email }}</a></p>
        <div class="stats">
            <div><strong>{{ $bookings->where('status', 'done')->count() }}</strong>visites</div>
            <div><strong>{{ $bookings->where('status', 'no_show')->count() }}</strong>absences</div>
            <div><strong>{{ $orders->whereIn('status', ['pending', 'ready', 'collected'])->count() }}</strong>commandes</div>
        </div>
    </div>

    <div class="panel">
        <h2 style="font-size:1.6rem">Rendez-vous</h2>
        @forelse ($bookings as $b)
            @if ($loop->first)<ul class="list">@endif
            <li><div class="grow">{{ ucfirst($b->starts_at->translatedFormat('j F Y à H:i')) }} · {{ $b->service->name }}</div><span class="status {{ $b->status }}">{{ $b->statusLabel() }}</span></li>
            @if ($loop->last)</ul>@endif
        @empty
            <p class="muted" style="margin:0">Aucun rendez-vous.</p>
        @endforelse
    </div>

    <div class="panel">
        <h2 style="font-size:1.6rem">Commandes</h2>
        @forelse ($orders as $o)
            @if ($loop->first)<ul class="list">@endif
            <li><div class="grow">{{ $o->created_at->translatedFormat('j F Y') }} · {{ $o->wig->name }} · {{ number_format($o->price, 0, ',', ' ') }} RWF</div><span class="status {{ $o->status }}">{{ $o->statusLabel() }}</span></li>
            @if ($loop->last)</ul>@endif
        @empty
            <p class="muted" style="margin:0">Aucune commande.</p>
        @endforelse
    </div>

    @if ($tryons->isNotEmpty())
        <div class="panel">
            <h2 style="font-size:1.6rem">Ses essais en cabine</h2>
            <div class="gallery">
                @foreach ($tryons as $t)
                    <figure><img src="{{ route('tryon.image', $t) }}" alt="Essai {{ $t->wig->name }}" loading="lazy"><figcaption>{{ $t->wig->name }}</figcaption></figure>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
