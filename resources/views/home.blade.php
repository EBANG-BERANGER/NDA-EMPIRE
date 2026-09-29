@extends('layout')

@section('content')
<section class="hero">
    <div class="wrap">
        <div class="lockup">
            <span class="foil" role="img" aria-label="NDA"></span>
            <span class="wordmark">EMPIRE</span>
            <span class="by">by Niomba</span>
        </div>
        <h1>{{ __('Vois ta prochaine perruque sur toi, avant de la porter.') }}</h1>
        <p class="lead">{{ __('Des poses parfaites, à Kigali. Perruques, mèches, tresses et cils par Niomba.') }}</p>
        <div class="btn-row">
            <a class="btn" href="{{ route('booking') }}">{{ __('Réserver un rendez-vous') }}</a>
            <a class="btn ghost" href="{{ route('shop') }}">{{ __('Essayer une perruque') }}</a>
        </div>
    </div>
</section>

<section class="cabin-band">
    <div class="wrap">
        <div class="mirror">
            <div class="glass"><img src="{{ asset('img/models.jpg') }}" alt="{{ __('Quatre perruques de la maison : body wave noir, frange rideau, ombré cendré, balayage miel') }}" width="1100" height="843" loading="lazy"></div>
        </div>
        <div>
            <h2>{{ __('La cabine d\'essayage') }}</h2>
            <p class="muted">{{ __('Avant de choisir, vois le résultat sur ton propre visage.') }}</p>
            <ol class="steps">
                <li><div><strong>{{ __('Ajoute ta photo') }}</strong><span>{{ __('De face, avec une bonne lumière. Elle reste privée.') }}</span></div></li>
                <li><div><strong>{{ __('Choisis une perruque') }}</strong><span>{{ __('Dans le catalogue de Niomba, filmé au salon.') }}</span></div></li>
                <li><div><strong>{{ __('Vois-toi avec') }}</strong><span>{{ __('La cabine crée ta photo avec la perruque en quelques secondes.') }}</span></div></li>
            </ol>
            <a class="btn" href="{{ route('shop') }}#cabine">{{ __('Ouvrir la cabine') }}</a>
        </div>
    </div>
</section>

@if ($wigs->isNotEmpty())
<section>
    <div class="wrap">
        <h2>{{ __('Dans la cabine en ce moment') }}</h2>
        <p class="muted">{{ __('Touche « Essayer » : la cabine te montre avec la perruque, à partir de ta photo.') }}</p>
        <div class="wigs" style="margin-top:28px">
            @foreach ($wigs as $wig)
                @include('partials.wig', ['wig' => $wig])
            @endforeach
        </div>
        <p style="margin-top:28px"><a class="btn ghost" href="{{ route('shop') }}">{{ __('Voir toutes les perruques') }}</a></p>
    </div>
</section>
@endif

<section id="prestations">
    <div class="wrap">
        <h2>{{ __('Prestations') }}</h2>
        <p class="muted">{{ __('Prix en RWF, à régler au salon. Touche une prestation pour choisir ton créneau.') }}</p>
        <div class="menu" style="margin-top:32px">
            @foreach ($services as $category => $items)
                <div>
                    <h3>{{ __($category) }}</h3>
                    <ul>
                        @foreach ($items as $service)
                            <li><a href="{{ route('booking', ['service' => $service->id]) }}">
                                <span class="name">{{ __($service->name) }}</span>
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
        <h2>{{ __('Réalisations') }}</h2>
        <div class="gallery" style="margin-top:28px">
            @foreach ($portfolio as $item)
                <figure><img src="{{ $item->imageUrl() }}" alt="{{ $item->caption ?: __('Réalisation NDA EMPIRE') }}" loading="lazy">@if($item->caption)<figcaption>{{ $item->caption }}</figcaption>@endif</figure>
            @endforeach
        </div>
        <p style="margin-top:28px"><a href="{{ route('portfolio') }}">{{ __('Toutes les réalisations') }}</a></p>
    </div>
</section>
@endif
@endsection
