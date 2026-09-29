<article class="wig">
    <div class="media" @if($wig->video_path) data-video @endif>
        <img src="{{ $wig->imageUrl() }}" alt="{{ $wig->name }}" loading="lazy">
        @if ($wig->video_path)<video src="{{ $wig->videoUrl() }}" muted loop playsinline preload="none"></video>@endif
        @unless ($wig->in_stock)<span class="tag">Épuisée</span>@endunless
    </div>
    <h3>{{ $wig->name }}</h3>
    <div class="price">{{ number_format($wig->price, 0, ',', ' ') }} RWF</div>
    @if ($wig->description)<p class="muted small" style="margin:0">{{ $wig->description }}</p>@endif
    <div class="btn-row">
        @auth
            <form method="post" action="{{ route('wig.try', $wig) }}" data-tryon>@csrf<button class="btn small">Essayer</button></form>
            @if ($wig->in_stock)
                <form method="post" action="{{ route('wig.order', $wig) }}" data-confirm="Commander « {{ $wig->name }} » ? Tu paies au salon, au retrait.">@csrf<button class="btn small ghost">Commander</button></form>
            @endif
        @else
            <a class="btn small" href="{{ route('login') }}">Essayer</a>
        @endauth
    </div>
</article>
