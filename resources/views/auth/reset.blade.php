@extends('layout', ['title' => 'Nouveau mot de passe'])

@section('content')
<section>
    <div class="wrap narrow">
        <h1 style="font-size:2.6rem">Nouveau mot de passe</h1>
        <form method="post" action="{{ route('password.update') }}" class="panel stack">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <label>Email <input type="email" name="email" value="{{ old('email', request('email')) }}" required></label>
            <label>Nouveau mot de passe <input type="password" name="password" autocomplete="new-password" required minlength="8"></label>
            <label>Confirme <input type="password" name="password_confirmation" autocomplete="new-password" required minlength="8"></label>
            <button class="btn">Changer mon mot de passe</button>
        </form>
    </div>
</section>
@endsection
