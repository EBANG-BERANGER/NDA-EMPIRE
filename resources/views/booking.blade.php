@extends('layout', ['title' => __('Réserver')])

@section('content')
<section>
    <div class="wrap narrow" style="max-width:640px">
        <h1 style="font-size:clamp(2rem,5vw,3.2rem)">{{ __('Réserver') }}</h1>
        <p class="muted">{{ __('Choisis ta prestation et ton jour, puis un créneau libre. Niomba confirme ton rendez-vous et tu reçois une notification.') }}</p>

        <form method="get" action="{{ route('booking') }}" class="panel stack" style="margin-top:24px">
            <fieldset class="choices" style="border:0;padding:0;margin:0">
                <legend class="muted" style="margin-bottom:10px">{{ __('Prestation') }}</legend>
                @foreach ($services as $category => $items)
                    <div class="muted small" style="margin-top:6px">{{ __($category) }}</div>
                    @foreach ($items as $s)
                        <label><input type="radio" name="service" value="{{ $s->id }}" @checked($service?->id === $s->id) required> {{ __($s->name) }} <span class="price">{{ number_format($s->price, 0, ',', ' ') }} RWF</span></label>
                    @endforeach
                @endforeach
            </fieldset>
            <label>{{ __('Jour') }}
                <input type="date" name="date" value="{{ $date->toDateString() }}" min="{{ today()->toDateString() }}" max="{{ today()->addDays(config('salon.booking_days_ahead'))->toDateString() }}" required>
            </label>
            <button class="btn">{{ __('Voir les créneaux libres') }}</button>
        </form>

        @if ($service)
            <div class="panel stack" style="margin-top:24px" id="creneaux">
                <h2 style="font-size:1.6rem;margin:0">{{ ucfirst($date->translatedFormat(__('l j F'))) }}</h2>
                @if (empty($slots))
                    <p class="muted" style="margin:0">{{ __('Aucun créneau libre ce jour-là pour « :service ». Essaie un autre jour, ou écris-nous sur WhatsApp.', ['service' => __($service->name)]) }}</p>
                @else
                    <form method="post" action="{{ route('booking') }}" class="stack">
                        @csrf
                        <input type="hidden" name="service_id" value="{{ $service->id }}">
                        <div class="slots" role="radiogroup" aria-label="{{ __('Créneaux') }}">
                            @foreach ($slots as $slot)
                                <label><input type="radio" name="starts_at" value="{{ $slot->format('Y-m-d H:i') }}" required><span>{{ $slot->format('H:i') }}</span></label>
                            @endforeach
                        </div>
                        <label>{{ __('Un mot pour Niomba (facultatif)') }}
                            <textarea name="note" rows="2" maxlength="500" placeholder="{{ __('Longueur souhaitée, perruque à poser, allergie…') }}">{{ old('note') }}</textarea>
                        </label>
                        @auth
                            <button class="btn">{{ __('Envoyer ma réservation') }}</button>
                        @else
                            <p class="muted" style="margin:0">{{ __('Connecte-toi pour envoyer ta réservation : ton créneau reste affiché ici.') }}</p>
                            <div class="btn-row"><a class="btn" href="{{ route('login') }}">{{ __('Connexion') }}</a><a class="btn ghost" href="{{ route('register') }}">{{ __('Créer mon compte') }}</a></div>
                        @endauth
                    </form>
                @endif
            </div>
        @endif
    </div>
</section>
@endsection
