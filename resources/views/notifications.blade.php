@extends('layout', ['title' => 'Notifications'])

@section('content')
<section>
    <div class="wrap" style="max-width:720px">
        <h1 style="font-size:clamp(2rem,5vw,3rem)">Notifications</h1>
        <div class="panel">
            @forelse ($notifications as $n)
                @if ($loop->first)<ul class="list">@endif
                <li>
                    <div class="grow">
                        <strong>{{ $n->data['title'] }}</strong> @unless($n->read_at)<span class="status pending">Nouveau</span>@endunless<br>
                        {{ $n->data['body'] }}<br>
                        <span class="muted small">{{ $n->created_at->diffForHumans() }}</span>
                    </div>
                    <a class="btn small ghost" href="{{ $n->data['url'] }}">Voir</a>
                </li>
                @if ($loop->last)</ul>@endif
            @empty
                <p class="muted" style="margin:0">Rien de nouveau pour l'instant. Tes confirmations de rendez-vous et de commandes arriveront ici.</p>
            @endforelse
        </div>
    </div>
</section>
@endsection
