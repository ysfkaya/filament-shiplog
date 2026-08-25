<?php

namespace Ysfkaya\ShipLog\Concerns;

use Ysfkaya\ShipLog\Support\Settings;

trait HasDriver
{
    public function driver(string $driver): static
    {
        $this->settings()->driver = $driver;

        return $this;
    }

    public function usingMarkdown(?string $path = null): static
    {
        $this->settings()->driver = 'markdown';
        $this->settings()->markdownPath = $path ?? $this->settings()->markdownPath;

        return $this;
    }

    /**
     * @param  class-string|null  $model
     */
    public function usingDatabase(?string $model = null, ?string $table = null): static
    {
        $this->settings()->driver = 'database';

        if ($model !== null) {
            $this->settings()->model = $model;
        }

        if ($table !== null) {
            $this->settings()->table = $table;
        }

        return $this;
    }

    public function allowRawHtml(bool $condition = true): static
    {
        $this->settings()->allowRawHtml = $condition;

        return $this;
    }

    public function cache(bool $condition = true, ?int $ttl = null, ?string $store = null): static
    {
        $this->settings()->cacheEnabled = $condition;
        $this->settings()->cacheTtl = $ttl ?? $this->settings()->cacheTtl;
        $this->settings()->cacheStore = $store ?? $this->settings()->cacheStore;

        return $this;
    }

    public function perPage(int $perPage): static
    {
        $this->settings()->perPage = $perPage;

        return $this;
    }

    /**
     * @param  array<int, string>  $middleware
     */
    public function feedRoute(?string $prefix = null, ?array $middleware = null, bool $enabled = true): static
    {
        $this->settings()->routesEnabled = $enabled;
        $this->settings()->routePrefix = $prefix ?? $this->settings()->routePrefix;
        $this->settings()->routeMiddleware = $middleware ?? $this->settings()->routeMiddleware;

        return $this;
    }

    public function gates(?string $view = null, ?string $manage = null): static
    {
        $this->settings()->viewGate = $view ?? $this->settings()->viewGate;
        $this->settings()->manageGate = $manage ?? $this->settings()->manageGate;

        return $this;
    }

    protected function settings(): Settings
    {
        return app(Settings::class);
    }
}
