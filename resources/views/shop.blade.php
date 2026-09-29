@extends('layout', ['title' => 'Perruques et cabine d\'essayage'])

@section('content')
<div class="wrap shop">
    <aside class="cabin" id="cabine" aria-label="Ta cabine d'essayage">
        <div>
            <h2 style="margin-bottom:.2em">Ta cabine</h2>
            <p class="muted small">Ta photo reste privée : seules toi et Niomba pouvez la voir.</p>
        </div>

        <div class="mirror" data-mirror>
            <div class="glass">
                @guest
                    <p>Crée ton compte pour ajouter ta photo et essayer les perruques.</p>
                @elseif ($current)
                    <img src="{{ route('tryon.image', $current) }}" alt="Toi avec la perruque {{ $current->wig->name }}">
                @elseif (auth()->user()->selfie_path)
                    <img src="{{ route('selfie.show') }}" alt="Ta photo">
                @else
                    <p>Ajoute une photo de toi, de face, cheveux attachés si possible, avec une bonne lumière.</p>
                @endguest
            </div>
            @if ($current)<span class="caption">{{ $current->wig->name }}</span>@endif
        </div>

        @guest
            <div class="btn-row"><a class="btn" href="{{ route('register') }}">Créer mon compte</a><a class="btn ghost" href="{{ route('login') }}">Connexion</a></div>
        @else
            @unless ($aiReady)<p class="flash bad small">La cabine IA sera ouverte très bientôt. Tu peux déjà ajouter ta photo.</p>@endunless

            @if ($tryons->isNotEmpty())
                <div class="thumbs" aria-label="Tes essais">
                    @foreach ($tryons as $t)
                        <a href="{{ route('shop', ['essai' => $t->id]) }}#cabine" @if($current && $current->id === $t->id) aria-current="true" @endif><img src="{{ route('tryon.image', $t) }}" alt="Essai {{ $t->wig->name }}"></a>
                    @endforeach
                </div>
            @endif

            <form method="post" action="{{ route('selfie.store') }}" enctype="multipart/form-data" class="stack">
                @csrf
                <label>{{ auth()->user()->selfie_path ? 'Changer ma photo' : 'Ma photo' }}
                    <input type="file" name="selfie" accept="image/*" capture="user" required data-autosubmit>
                </label>
                <noscript><button class="btn">Envoyer</button></noscript>
            </form>
            @if (auth()->user()->selfie_path)
                <form method="post" action="{{ route('selfie.delete') }}" data-confirm="Supprimer ta photo et tous tes essais ?">@csrf @method('delete')<button class="linkish small">Supprimer ma photo et mes essais</button></form>
            @endif
        @endguest
    </aside>

    <div>
        <h1 style="font-size:clamp(2rem,5vw,3.2rem)">Perruques</h1>
        <p class="muted">Touche « Essayer » pour te voir avec. « Commander » la réserve pour toi : tu paies au salon, au retrait.</p>
        @if ($wigs->isEmpty())
            <p class="panel" style="margin-top:24px">Les premières perruques arrivent très bientôt. Écris-nous sur <a href="https://wa.me/{{ config('salon.whatsapp') }}">WhatsApp</a> pour connaître celles disponibles.</p>
        @else
            <div class="wigs" style="margin-top:28px">
                @foreach ($wigs as $wig)
                    @include('partials.wig', ['wig' => $wig])
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
