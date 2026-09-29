@extends('layout')

@section('content')
<section class="hero">
    <div class="wrap">
        <div>
            <h1>Vois ta prochaine perruque sur toi, avant de la porter.</h1>
            <p class="lead">Perruques, poses, tresses et cils à Kigali, par Niomba. Envoie une photo de toi, choisis une perruque, et la cabine te montre le résultat.</p>
            <div class="btn-row">
                <a class="btn" href="{{ route('booking') }}">Réserver un rendez-vous</a>
                <a class="btn ghost" href="{{ route('shop') }}">Essayer une perruque</a>
            </div>
        </div>
        <div class="mirror">
            <div class="glass"><img src="{{ asset('img/logo-round.jpg') }}" alt="NDA EMPIRE by Niomba : quatre modèles portant des perruques de la maison"></div>
        </div>
    </div>
</section>

@if ($wigs->isNotEmpty())
<section>
    <div class="wrap">
        <h2>Dans la cabine en ce moment</h2>
        <p class="muted">Touche « Essayer » : la cabine te montre avec la perruque, à partir de ta photo.</p>
        <div class="wigs" style="margin-top:28px">
            @foreach ($wigs as $wig)
                @include('partials.wig', ['wig' => $wig])
            @endforeach
        </div>
        <p style="margin-top:28px"><a class="btn ghost" href="{{ route('shop') }}">Voir toutes les perruques</a></p>
    </div>
</section>
@endif

<section id="prestations">
    <div class="wrap">
        <h2>Prestations</h2>
        <p class="muted">Prix en RWF, à régler au salon. Touche une prestation pour choisir ton créneau.</p>
        <div class="menu" style="margin-top:32px">
            @foreach ($services as $category => $items)
                <div>
                    <h3>{{ $category }}</h3>
                    <ul>
                        @foreach ($items as $service)
                            <li><a href="{{ route('booking', ['service' => $service->id]) }}">
                                <span class="name">{{ $service->name }}</span>
                                <span class="price">{{ number_format($service->price, 0, ',', ' ') }}</span>
                                <span class="dur">{{ intdiv($service->duration_minutes, 60) ? intdiv($service->duration_minutes, 60).' h ' : '' }}{{ $service->duration_minutes % 60 ? ($service->duration_minutes % 60).' min' : '' }}</span>
                            </a></li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
    </div>
</section>

@if ($portfolio->isNotEmpty())
<section>
    <div class="wrap">
        <h2>Réalisations</h2>
        <div class="gallery" style="margin-top:28px">
            @foreach ($portfolio as $item)
                <figure><img src="{{ $item->imageUrl() }}" alt="{{ $item->caption ?: 'Réalisation NDA EMPIRE' }}" loading="lazy">@if($item->caption)<figcaption>{{ $item->caption }}</figcaption>@endif</figure>
            @endforeach
        </div>
        <p style="margin-top:28px"><a href="{{ route('portfolio') }}">Toutes les réalisations</a></p>
    </div>
</section>
@endif
@endsection
