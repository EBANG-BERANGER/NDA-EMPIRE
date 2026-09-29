@extends('layout', ['title' => 'Mot de passe oublié'])

@section('content')
<section>
    <div class="wrap narrow">
        <h1 style="font-size:2.6rem">Mot de passe oublié</h1>
        <form method="post" action="{{ route('password.email') }}" class="panel stack">
            @csrf
            <label>Ton email <input type="email" name="email" value="{{ old('email') }}" required autofocus></label>
            <button class="btn">Recevoir un lien</button>
        </form>
        <p class="muted" style="margin-top:20px">Pas d'email reçu ? Écris-nous sur <a href="https://wa.me/{{ config('salon.whatsapp') }}">WhatsApp</a>.</p>
    </div>
</section>
@endsection
