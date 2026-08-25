<?php

namespace Ysfkaya\ShipLog\Support;

use Ysfkaya\ShipLog\Enums\FabPosition;

/**
 * Floating button settings shared by the plugin and the frontend component.
 *
 * The button renders outside any panel, so its configuration cannot live on
 * the plugin instance alone. Anything left unset falls back to config.
 */
class FabSettings
{
    protected ?bool $enabled = null;

    protected ?FabPosition $position = null;

    protected ?string $label = null;

    /**
     * @var array<int, string>|null
     */
    protected ?array $environments = null;

    public function enabled(bool $enabled = true): static
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function position(FabPosition | string $position): static
    {
        $this->position = is_string($position) ? FabPosition::from($position) : $position;

        return $this;
    }

    public function label(?string $label): static
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @param  array<int, string>  $environments
     */
    public function environments(array $environments): static
    {
        $this->environments = $environments;

        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->enabled ?? (bool) config('shiplog.fab.enabled', true);
    }

    public function isEnabledIn(string $environment): bool
    {
        if (! $this->isEnabled()) {
            return false;
        }

        $environments = $this->getEnvironments();

        return $environments === [] || in_array($environment, $environments, strict: true);
    }

    public function getPosition(): FabPosition
    {
        if ($this->position instanceof FabPosition) {
            return $this->position;
        }

        $configured = config('shiplog.fab.position', FabPosition::BottomRight);

        return $configured instanceof FabPosition ? $configured : FabPosition::from((string) $configured);
    }

    public function getLabel(): ?string
    {
        return $this->label ?? config('shiplog.fab.label');
    }

    /**
     * @return array<int, string>
     */
    public function getEnvironments(): array
    {
        return $this->environments ?? config('shiplog.fab.environments', []);
    }
}
