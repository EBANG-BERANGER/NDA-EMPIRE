@extends('layout', ['title' => 'Perruques'])

@section('content')
<div class="wrap stack" style="gap:24px">
    @include('admin.nav')
    <h1 style="font-size:2.4rem;margin:0">Perruques &amp; mèches</h1>

    <form method="post" action="{{ route('admin.wigs.store') }}" enctype="multipart/form-data" class="panel stack" style="max-width:640px" data-wig-form>
        @csrf
        <h2 style="font-size:1.6rem;margin:0">Ajouter une perruque ou des mèches</h2>
        <p class="muted small" style="margin:0">Filme la perruque sur sa tête de mannequin, de face, bien éclairée. La photo du catalogue est prise automatiquement dans la vidéo, ou tu peux en choisir une toi-même. C'est cette photo que l'IA utilise pour l'essayage.</p>
        <label>Vidéo (facultatif, 60 Mo max) <input type="file" name="video" accept="video/*" data-video-input></label>
        <label>Photo <input type="file" name="image" accept="image/*" required data-image-input></label>
        <img data-preview alt="Aperçu de la photo" style="display:none;max-width:220px;border-radius:14px;border:1px solid var(--rose-soft)">
        <label>Type <select name="kind">@foreach (\App\Models\Wig::KINDS as $k => $label)<option value="{{ $k }}">{{ $label }}</option>@endforeach</select></label>
        <label>Nom <input name="name" required maxlength="120" placeholder="Body wave 24 pouces, blond miel"></label>
        <label>Description (facultatif) <textarea name="description" rows="2" maxlength="1000" placeholder="Lace HD 13x4, cheveux 100 % humains, densité 180 %"></textarea></label>
        <label>Prix en RWF <input type="number" name="price" min="0" step="500" required></label>
        <button class="btn">Ajouter au catalogue</button>
    </form>

    <div class="panel table-wrap">
        <table>
            <thead><tr><th></th><th>Nom</th><th>Prix (RWF)</th><th>En stock</th><th></th></tr></thead>
            <tbody>
            @forelse ($wigs as $w)
                <tr>
                    <td><img src="{{ $w->imageUrl() }}" alt="" style="width:52px;height:64px;object-fit:cover;border-radius:10px"></td>
                    <td><input form="w{{ $w->id }}" name="name" value="{{ $w->name }}" required aria-label="Nom"></td>
                    <td><input form="w{{ $w->id }}" type="number" name="price" value="{{ $w->price }}" min="0" step="500" required aria-label="Prix" style="width:120px"></td>
                    <td><input form="w{{ $w->id }}" type="checkbox" name="in_stock" value="1" @checked($w->in_stock) aria-label="En stock"></td>
                    <td style="white-space:nowrap">
                        <form id="w{{ $w->id }}" method="post" action="{{ route('admin.wigs.update', $w) }}" class="inline">@csrf @method('put')<button class="btn small">Enregistrer</button></form>
                        <form method="post" action="{{ route('admin.wigs.delete', $w) }}" class="inline" data-confirm="Supprimer « {{ $w->name }} » ?">@csrf @method('delete')<button class="linkish small">Supprimer</button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="muted">Aucune perruque pour l'instant. Ajoute la première avec le formulaire ci-dessus.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
