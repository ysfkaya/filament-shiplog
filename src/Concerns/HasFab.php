<?php

namespace Ysfkaya\ShipLog\Concerns;

use Ysfkaya\ShipLog\Enums\FabPosition;
use Ysfkaya\ShipLog\Support\FabSettings;

trait HasFab
{
    public function fab(FabPosition | string | null $position = null, bool $enabled = true): static
    {
        $settings = $this->fabSettings()->enabled($enabled);

        if ($position !== null) {
            $settings->position($position);
        }

        return $this;
    }

    public function fabLabel(?string $label): static
    {
        $this->fabSettings()->label($label);

        return $this;
    }

    /**
     * Restricts the floating button to specific application environments, so
     * a staging only changelog never leaks into production.
     *
     * @param  array<int, string>  $environments
     */
    public function fabEnvironments(array $environments): static
    {
        $this->fabSettings()->environments($environments);

        return $this;
    }

    protected function fabSettings(): FabSettings
    {
        return app(FabSettings::class);
    }
}
