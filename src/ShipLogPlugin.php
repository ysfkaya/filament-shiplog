<?php

namespace Ysfkaya\ShipLog;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Ysfkaya\ShipLog\Filament\Resources\Releases\ReleaseResource;

class ShipLogPlugin implements Plugin
{
    public const ID = 'fi-shiplog';

    use Concerns\Authorization;
    use Concerns\HasDriver;
    use Concerns\HasFab;
    use Concerns\HasPage;

    protected ?bool $registersResource = null;

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static */
        return filament(static::ID);
    }

    public function getId(): string
    {
        return static::ID;
    }

    /**
     * The editing resource only makes sense when releases actually live in
     * the database, so by default it follows the active driver.
     */
    public function resource(bool $condition = true): static
    {
        $this->registersResource = $condition;

        return $this;
    }

    public function hasResource(): bool
    {
        return $this->registersResource ?? $this->settings()->driver === 'database';
    }

    public function register(Panel $panel): void
    {
        $panel->pages([$this->getPage()]);

        if ($this->hasResource()) {
            $panel->resources([ReleaseResource::class]);
        }
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
