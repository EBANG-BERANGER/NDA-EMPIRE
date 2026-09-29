@extends('layout', ['title' => 'Réalisations'])

@section('content')
<section>
    <div class="wrap">
        <h1 style="font-size:clamp(2rem,5vw,3.2rem)">Réalisations</h1>
        <p class="muted">Poses, coiffages et cils réalisés au salon.</p>
        @if ($items->isEmpty())
            <p class="panel">Les photos arrivent bientôt. En attendant, <a href="https://wa.me/{{ config('salon.whatsapp') }}">demande-nous des exemples sur WhatsApp</a>.</p>
        @else
            <div class="gallery" style="margin-top:28px">
                @foreach ($items as $item)
                    <figure><img src="{{ $item->imageUrl() }}" alt="{{ $item->caption ?: 'Réalisation NDA EMPIRE' }}" loading="lazy">@if($item->caption)<figcaption>{{ $item->caption }}</figcaption>@endif</figure>
                @endforeach
            </div>
            {{ $items->links() }}
        @endif
    </div>
</section>
@endsection
