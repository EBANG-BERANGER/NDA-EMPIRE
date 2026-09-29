@extends('layout', ['title' => __('Connexion')])

@section('content')
<section>
    <div class="wrap narrow">
        <h1 style="font-size:2.6rem">{{ __('Connexion') }}</h1>
        <form method="post" action="{{ route('login') }}" class="panel stack">
            @csrf
            <label>{{ __('Email') }} <input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required autofocus></label>
            <label>{{ __('Mot de passe') }} <input type="password" name="password" autocomplete="current-password" required></label>
            <button class="btn">{{ __('Me connecter') }}</button>
            <p class="small" style="margin:0"><a href="{{ route('password.request') }}">{{ __('Mot de passe oublié ?') }}</a></p>
        </form>
        <p style="margin-top:20px">{{ __('Pas encore de compte ?') }} <a href="{{ route('register') }}">{{ __('Créer mon compte') }}</a></p>
    </div>
</section>
@endsection
