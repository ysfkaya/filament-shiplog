<?php

namespace Ysfkaya\ShipLog\Concerns;

use Ysfkaya\ShipLog\Models\Release;

/**
 * Lets a panel configure the changelog source without publishing the config
 * file. Values are written into config, which every driver already reads, so
 * these methods and `config/shiplog.php` stay in sync by construction.
 */
trait HasDriver
{
    public function driver(string $driver): static
    {
        config()->set('shiplog.driver', $driver);

        return $this;
    }

    public function usingMarkdown(?string $path = null): static
    {
        config()->set('shiplog.driver', 'markdown');

        if ($path !== null) {
            config()->set('shiplog.markdown.path', $path);
        }

        return $this;
    }

    public function usingDatabase(?string $model = null): static
    {
        config()->set('shiplog.driver', 'database');
        config()->set('shiplog.model', $model ?? Release::class);

        return $this;
    }

    public function allowRawHtml(bool $condition = true): static
    {
        config()->set('shiplog.markdown.allow_html', $condition);

        return $this;
    }

    public function cache(bool $condition = true, ?int $ttl = null, ?string $store = null): static
    {
        config()->set('shiplog.cache.enabled', $condition);

        if ($ttl !== null) {
            config()->set('shiplog.cache.ttl', $ttl);
        }

        if ($store !== null) {
            config()->set('shiplog.cache.store', $store);
        }

        return $this;
    }

    public function perPage(int $perPage): static
    {
        config()->set('shiplog.per_page', $perPage);

        return $this;
    }
}
