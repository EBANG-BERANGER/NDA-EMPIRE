@extends('layout', ['title' => 'Connexion'])

@section('content')
<section>
    <div class="wrap narrow">
        <h1 style="font-size:2.6rem">Connexion</h1>
        <form method="post" action="{{ route('login') }}" class="panel stack">
            @csrf
            <label>Email <input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required autofocus></label>
            <label>Mot de passe <input type="password" name="password" autocomplete="current-password" required></label>
            <button class="btn">Me connecter</button>
            <p class="small" style="margin:0"><a href="{{ route('password.request') }}">Mot de passe oublié ?</a></p>
        </form>
        <p style="margin-top:20px">Pas encore de compte ? <a href="{{ route('register') }}">Créer mon compte</a></p>
    </div>
</section>
@endsection
