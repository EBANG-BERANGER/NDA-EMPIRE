@extends('layout', ['title' => __('Créer mon compte')])

@section('content')
<section>
    <div class="wrap narrow">
        <h1 style="font-size:2.6rem">{{ __('Créer mon compte') }}</h1>
        <p class="muted">{{ __('Pour réserver, commander et essayer les perruques sur ta photo.') }}</p>
        <form method="post" action="{{ route('register') }}" class="panel stack">
            @csrf
            <label>{{ __('Prénom et nom') }} <input name="name" value="{{ old('name') }}" autocomplete="name" required maxlength="80"></label>
            <label>{{ __('Téléphone (WhatsApp)') }} <input type="tel" name="phone" value="{{ old('phone') }}" autocomplete="tel" placeholder="+250 7…" required maxlength="30"></label>
            <label>{{ __('Email') }} <input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required></label>
            <label>{{ __('Mot de passe (8 caractères minimum)') }} <input type="password" name="password" autocomplete="new-password" required minlength="8"></label>
            <label>{{ __('Confirme le mot de passe') }} <input type="password" name="password_confirmation" autocomplete="new-password" required minlength="8"></label>
            <button class="btn">{{ __('Créer mon compte') }}</button>
        </form>
        <p style="margin-top:20px">{{ __('Déjà un compte ?') }} <a href="{{ route('login') }}">{{ __('Connexion') }}</a></p>
    </div>
</section>
@endsection
