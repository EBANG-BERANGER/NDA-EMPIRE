@extends('layout', ['title' => __('Nouveau mot de passe')])

@section('content')
<section>
    <div class="wrap narrow">
        <h1 style="font-size:2.6rem">{{ __('Nouveau mot de passe') }}</h1>
        <form method="post" action="{{ route('password.update') }}" class="panel stack">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <label>{{ __('Email') }} <input type="email" name="email" value="{{ old('email', request('email')) }}" required></label>
            <label>{{ __('Nouveau mot de passe') }} <input type="password" name="password" autocomplete="new-password" required minlength="8"></label>
            <label>{{ __('Confirme le mot de passe') }} <input type="password" name="password_confirmation" autocomplete="new-password" required minlength="8"></label>
            <button class="btn">{{ __('Changer mon mot de passe') }}</button>
        </form>
    </div>
</section>
@endsection
