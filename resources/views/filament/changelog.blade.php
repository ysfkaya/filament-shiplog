<x-filament-panels::page>
    <ship-log
        mode="inline"
        @if (\Illuminate\Support\Facades\Route::has('shiplog.feed'))
            src="{{ route('shiplog.feed') }}"
        @endif
        empty-text="{{ __('shiplog::shiplog.timeline.empty') }}"
        error-text="{{ __('shiplog::shiplog.timeline.error') }}"
    ></ship-log>
</x-filament-panels::page>
