@once
    <script src="{{ \Filament\Support\Facades\FilamentAsset::getScriptSrc('fi-shiplog', 'ysfkaya') }}" defer></script>
@endonce

<ship-log
    @if (\Illuminate\Support\Facades\Route::has('shiplog.feed'))
        src="{{ route('shiplog.feed') }}"
    @endif
    mode="{{ $mode ?? 'fab' }}"
    position="{{ $resolvedPosition() }}"
    label="{{ $resolvedLabel() }}"
    heading="{{ $heading ?? __('shiplog::shiplog.timeline.heading') }}"
    subheading="{{ $subheading ?? __('shiplog::shiplog.timeline.subheading') }}"
    signature="{{ \Ysfkaya\ShipLog\Facades\ShipLog::signature() }}"
    storage-key="{{ \Illuminate\Support\Str::slug(config('app.name', 'app')) }}"
    empty-text="{{ __('shiplog::shiplog.timeline.empty') }}"
    error-text="{{ __('shiplog::shiplog.timeline.error') }}"
></ship-log>
