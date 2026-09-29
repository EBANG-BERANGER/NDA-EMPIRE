@extends('layout', ['title' => __('Réalisations')])

@section('content')
<section>
    <div class="wrap">
        <h1 style="font-size:clamp(2rem,5vw,3.2rem)">{{ __('Réalisations') }}</h1>
        <p class="muted">{{ __('Poses, coiffages et cils réalisés au salon.') }} <a href="{{ config('salon.tiktok') }}">{{ __('Plus de vidéos sur TikTok') }}</a></p>
        @if ($items->isEmpty())
            <p class="panel">{{ __('Les photos arrivent bientôt.') }} <a href="https://wa.me/{{ config('salon.whatsapp') }}">{{ __('Demande-nous des exemples sur WhatsApp.') }}</a></p>
        @else
            <div class="gallery" style="margin-top:28px">
                @foreach ($items as $item)
                    <figure><img src="{{ $item->imageUrl() }}" alt="{{ $item->caption ?: __('Réalisation NDA EMPIRE') }}" loading="lazy">@if($item->caption)<figcaption>{{ $item->caption }}</figcaption>@endif</figure>
                @endforeach
            </div>
            {{ $items->links() }}
        @endif
    </div>
</section>
@endsection
