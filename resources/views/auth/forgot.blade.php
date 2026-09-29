@extends('layout', ['title' => __('Mot de passe oublié')])

@section('content')
<section>
    <div class="wrap narrow">
        <h1 style="font-size:2.6rem">{{ __('Mot de passe oublié') }}</h1>
        <form method="post" action="{{ route('password.email') }}" class="panel stack">
            @csrf
            <label>{{ __('Ton email') }} <input type="email" name="email" value="{{ old('email') }}" required autofocus></label>
            <button class="btn">{{ __('Recevoir un lien') }}</button>
        </form>
        <p class="muted" style="margin-top:20px"><a href="https://wa.me/{{ config('salon.whatsapp') }}">{{ __("Pas d'email reçu ? Écris-nous sur WhatsApp.") }}</a></p>
    </div>
</section>
@endsection
