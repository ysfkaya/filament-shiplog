<?php

namespace Ysfkaya\ShipLog\View;

use Illuminate\View\Component;
use Ysfkaya\ShipLog\Support\Authorizer;
use Ysfkaya\ShipLog\Support\FabSettings;

class ShipLog extends Component
{
    public function __construct(
        public ?string $position = null,
        public ?string $label = null,
        public ?string $heading = null,
        public ?string $subheading = null,
        public ?string $mode = null,
    ) {}

    /**
     * Nothing is rendered for visitors who cannot view the changelog, so the
     * button never hints at updates they are not allowed to read.
     */
    public function shouldRender(): bool
    {
        if (! app(Authorizer::class)->canView()) {
            return false;
        }

        return app(FabSettings::class)->isEnabledIn(app()->environment());
    }

    public function resolvedPosition(): string
    {
        return $this->position ?? app(FabSettings::class)->getPosition()->value;
    }

    public function resolvedLabel(): string
    {
        return $this->label
            ?? app(FabSettings::class)->getLabel()
            ?? __('shiplog::shiplog.timeline.label');
    }

    public function render(): string
    {
        return 'shiplog::fab';
    }
}
