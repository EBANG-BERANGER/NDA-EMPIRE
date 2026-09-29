<li>
    <div class="grow">
        <strong>{{ $b->starts_at->format('H:i') }}–{{ $b->ends_at->format('H:i') }}</strong>
        <span class="muted">{{ $b->starts_at->translatedFormat('D j M') }}</span> · {{ $b->service->name }}<br>
        <a href="{{ route('admin.client', $b->user) }}">{{ $b->user->name }}</a> · <a href="https://wa.me/{{ preg_replace('/\D/', '', $b->user->phone) }}">{{ $b->user->phone }}</a>
        @if ($b->note)<br><span class="muted small">« {{ $b->note }} »</span>@endif
    </div>
    <span class="status {{ $b->status }}">{{ $b->statusLabel() }}</span>
    @if ($actions)
        <form method="post" action="{{ route('admin.booking', $b) }}" class="inline">@csrf @method('patch')
            @foreach ($actions as $status => $label)
                <button class="btn small {{ in_array($status, ['cancelled', 'no_show']) ? 'danger' : ($loop->first ? '' : 'ghost') }}" name="status" value="{{ $status }}">{{ $label }}</button>
            @endforeach
        </form>
    @endif
</li>
