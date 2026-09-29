<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ isset($title) ? $title.' | ' : '' }}NDA EMPIRE by Niomba · Kigali</title>
    <meta name="description" content="{{ __('Perruques, pose de perruques, tresses et extensions de cils à Kigali. Réserve en ligne et essaie les perruques sur ta photo.') }}">
    <meta name="theme-color" content="#ffffff">
    <meta property="og:title" content="NDA EMPIRE by Niomba">
    <meta property="og:description" content="{{ __('Perruques, pose de perruques, tresses et extensions de cils à Kigali. Réserve en ligne et essaie les perruques sur ta photo.') }}">
    <meta property="og:image" content="{{ asset('img/logo-round.jpg') }}">
    <link rel="icon" href="{{ asset('img/icon.jpg') }}">
    <link rel="apple-touch-icon" href="{{ asset('img/icon.jpg') }}">
    @foreach (\App\Http\Middleware\SetLocale::LOCALES as $l)
        <link rel="alternate" hreflang="{{ $l }}" href="{{ request()->fullUrlWithQuery(['lang' => $l]) }}">
    @endforeach
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bodoni+Moda:ital,opsz,wght@0,6..96,400..600;1,6..96,400..600&family=Jost:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v=3">
    <script src="{{ asset('js/app.js') }}?v=2" defer></script>
</head>
<body>
@php($unread = auth()->check() ? auth()->user()->unreadNotifications()->count() : 0)
<header class="site-head">
    <div class="wrap">
        <a class="mark" href="{{ route('home') }}" aria-label="NDA EMPIRE, {{ __('accueil') }}">
            <span class="foil mark-foil" role="img" aria-hidden="true"></span>
            <span>EMPIRE</span>
        </a>
        <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="nav">{{ __('Menu') }}</button>
        <nav class="nav" id="nav">
            <a href="{{ route('shop') }}" @if(request()->routeIs('shop')) aria-current="page" @endif>{{ __('Perruques & mèches') }}</a>
            <a href="{{ route('home') }}#prestations">{{ __('Prestations') }}</a>
            <a href="{{ route('portfolio') }}" @if(request()->routeIs('portfolio')) aria-current="page" @endif>{{ __('Réalisations') }}</a>
            <a href="{{ route('booking') }}" @if(request()->routeIs('booking')) aria-current="page" @endif>{{ __('Réserver') }}</a>
            @auth
                @can('admin')<a href="{{ route('admin') }}" @if(request()->routeIs('admin*')) aria-current="page" @endif>{{ __('Gestion') }}</a>@endcan
                <a href="{{ route('account') }}" @if(request()->routeIs('account')) aria-current="page" @endif>{{ __('Mon compte') }}</a>
                <a class="bell" href="{{ route('notifications') }}">{{ __('Notifications') }} @if($unread)<span class="dot">{{ $unread }}</span>@endif</a>
                <form method="post" action="{{ route('logout') }}">@csrf<button class="linkish">{{ __('Déconnexion') }}</button></form>
            @else
                <a href="{{ route('login') }}">{{ __('Connexion') }}</a>
            @endauth
            <span class="lang">
                @foreach (['fr' => 'FR', 'en' => 'EN'] as $l => $label)
                    <a href="{{ request()->fullUrlWithQuery(['lang' => $l]) }}" hreflang="{{ $l }}" lang="{{ $l }}" @if(app()->getLocale() === $l) aria-current="true" @endif>{{ $label }}</a>
                @endforeach
            </span>
        </nav>
    </div>
</header>

<main>
    @if (session('status') || $errors->any())
        <div class="wrap">
            @if (session('status'))<p class="flash" role="status">{{ session('status') }}</p>@endif
            @if ($errors->any())<div class="flash bad" role="alert">@foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>@endif
        </div>
    @endif
    @yield('content')
</main>

<footer class="site-foot">
    <div class="wrap">
        <div class="foot-brand">
            <span class="foil" style="width:104px" role="img" aria-label="NDA"></span>
            <p class="wordmark">EMPIRE</p>
            <p class="muted small">by Niomba · Kigali</p>
        </div>
        <div>
            <h3>{{ __('Nous joindre') }}</h3>
            <p><a href="https://wa.me/{{ config('salon.whatsapp') }}">WhatsApp {{ config('salon.phone') }}</a></p>
            <p><a href="tel:+{{ config('salon.whatsapp') }}">{{ __('Appeler') }}</a></p>
            @if (config('salon.instagram'))<p><a href="{{ config('salon.instagram') }}">Instagram</a></p>@endif
            @if (config('salon.tiktok'))<p><a href="{{ config('salon.tiktok') }}">TikTok</a></p>@endif
        </div>
        <div>
            <h3>{{ __('Horaires') }}</h3>
            <p>{{ __('Lundi au samedi, 9h à 19h') }}</p>
            <p class="muted">{{ __('Sur rendez-vous, paiement au salon.') }}</p>
        </div>
    </div>
</footer>

<a class="wa" href="https://wa.me/{{ config('salon.whatsapp') }}" aria-label="{{ __('Écrire sur WhatsApp') }}">
    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2zm0 18.2c-1.5 0-3-.4-4.3-1.2l-.3-.2-3 .8.8-2.9-.2-.3A8.2 8.2 0 1 1 12 20.2zm4.5-6.1c-.2-.1-1.5-.7-1.7-.8s-.4-.1-.6.1-.7.8-.8 1-.3.2-.5.1a6.7 6.7 0 0 1-3.3-2.9c-.2-.4.2-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5a1 1 0 0 0-.7.3 3 3 0 0 0-.9 2.2 5.2 5.2 0 0 0 1.1 2.7 11.8 11.8 0 0 0 4.5 4c1.7.7 2.3.8 3.2.6.5-.1 1.5-.6 1.7-1.2s.2-1.1.2-1.2-.2-.2-.5-.3z"/></svg>
</a>
</body>
</html>
