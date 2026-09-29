@extends('layout', ['title' => 'Réalisations'])

@section('content')
<div class="wrap stack" style="gap:24px">
    @include('admin.nav')
    <h1 style="font-size:2.4rem;margin:0">Réalisations</h1>

    <form method="post" action="{{ route('admin.portfolio.store') }}" enctype="multipart/form-data" class="panel stack" style="max-width:560px">
        @csrf
        <label>Photos (jusqu'à 20 à la fois) <input type="file" name="images[]" accept="image/*" multiple required></label>
        <label>Légende (facultatif) <input name="caption" maxlength="160" placeholder="Pose lace frontale, coiffage wavy"></label>
        <button class="btn">Publier</button>
    </form>

    <div class="gallery">
        @forelse ($items as $item)
            <figure>
                <img src="{{ $item->imageUrl() }}" alt="{{ $item->caption }}" loading="lazy">
                <figcaption>
                    {{ $item->caption }}
                    <form method="post" action="{{ route('admin.portfolio.delete', $item) }}" data-confirm="Retirer cette photo ?">@csrf @method('delete')<button class="linkish small">Retirer</button></form>
                </figcaption>
            </figure>
        @empty
            <p class="muted">Aucune photo publiée.</p>
        @endforelse
    </div>
</div>
@endsection
