@extends('layout', ['title' => 'Prestations'])

@section('content')
<div class="wrap stack" style="gap:24px">
    @include('admin.nav')
    <h1 style="font-size:2.4rem;margin:0">Prestations</h1>

    <div class="panel table-wrap">
        <table>
            <thead><tr><th>Nom</th><th>Catégorie</th><th>Durée (min)</th><th>Prix (RWF)</th><th></th></tr></thead>
            <tbody>
            @foreach ($services as $s)
                <tr>
                    <td><input form="s{{ $s->id }}" name="name" value="{{ $s->name }}" required aria-label="Nom"></td>
                    <td><input form="s{{ $s->id }}" name="category" value="{{ $s->category }}" required list="cats" aria-label="Catégorie" style="min-width:110px"></td>
                    <td><input form="s{{ $s->id }}" type="number" name="duration_minutes" value="{{ $s->duration_minutes }}" min="15" step="15" required aria-label="Durée" style="width:90px"></td>
                    <td><input form="s{{ $s->id }}" type="number" name="price" value="{{ $s->price }}" min="0" step="500" required aria-label="Prix" style="width:110px"></td>
                    <td style="white-space:nowrap">
                        <form id="s{{ $s->id }}" method="post" action="{{ route('admin.services.update', $s) }}" class="inline">@csrf @method('put')<button class="btn small">Enregistrer</button></form>
                        <form method="post" action="{{ route('admin.services.delete', $s) }}" class="inline" data-confirm="Supprimer « {{ $s->name }} » ?">@csrf @method('delete')<button class="linkish small">Supprimer</button></form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <datalist id="cats">@foreach ($services->pluck('category')->unique() as $c)<option value="{{ $c }}">@endforeach</datalist>
    </div>

    <form method="post" action="{{ route('admin.services.store') }}" class="panel stack" style="max-width:560px">
        @csrf
        <h2 style="font-size:1.6rem;margin:0">Ajouter une prestation</h2>
        <label>Nom <input name="name" required maxlength="120"></label>
        <label>Catégorie <input name="category" list="cats" required maxlength="40" placeholder="Coiffure, Cils…"></label>
        <label>Durée en minutes <input type="number" name="duration_minutes" min="15" step="15" value="60" required></label>
        <label>Prix en RWF <input type="number" name="price" min="0" step="500" required></label>
        <button class="btn">Ajouter</button>
    </form>
</div>
@endsection
