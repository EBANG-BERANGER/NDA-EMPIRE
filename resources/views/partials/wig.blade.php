<article class="wig">
    <div class="media" @if($wig->video_path) data-video @endif>
        <img src="{{ $wig->imageUrl() }}" alt="{{ $wig->name }}" loading="lazy">
        @if ($wig->video_path)<video src="{{ $wig->videoUrl() }}" muted loop playsinline preload="none"></video>@endif
        @unless ($wig->in_stock)<span class="tag">{{ __('Épuisée') }}</span>@elseif (! $wig->isWig())<span class="tag">{{ __('Mèches') }}</span>@endunless
    </div>
    <h3>{{ $wig->name }}</h3>
    <div class="price">{{ number_format($wig->price, 0, ',', ' ') }} RWF</div>
    @if ($wig->description)<p class="muted small" style="margin:0">{{ $wig->description }}</p>@endif
    <div class="btn-row">
        @auth
            @if ($wig->isWig())<form method="post" action="{{ route('wig.try', $wig) }}" data-tryon data-busy="{{ __('Essayage en cours…') }}">@csrf<button class="btn small">{{ __('Essayer') }}</button></form>@endif
            @if ($wig->in_stock)
                <form method="post" action="{{ route('wig.order', $wig) }}" data-confirm="{{ __('Commander « :wig » ? Tu paies au salon, au retrait.', ['wig' => $wig->name]) }}">@csrf<button class="btn small ghost">{{ __('Commander') }}</button></form>
            @endif
        @elseif ($wig->isWig())
            <a class="btn small" href="{{ route('login') }}">{{ __('Essayer') }}</a>
        @endauth
    </div>
</article>
