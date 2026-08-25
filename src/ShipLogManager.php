<?php

namespace Ysfkaya\ShipLog;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Manager;
use Ysfkaya\ShipLog\Contracts\ChangelogRepository;
use Ysfkaya\ShipLog\Data\Release;
use Ysfkaya\ShipLog\Markdown\ChangelogParser;
use Ysfkaya\ShipLog\Markdown\MarkdownRenderer;
use Ysfkaya\ShipLog\Repositories\DatabaseChangelogRepository;
use Ysfkaya\ShipLog\Repositories\MarkdownChangelogRepository;
use Ysfkaya\ShipLog\Support\Settings;

/**
 * Resolves the configured changelog driver and applies the rules that must
 * hold no matter where releases come from: environment visibility and
 * caching.
 *
 * @method ChangelogRepository driver(?string $driver = null)
 */
class ShipLogManager extends Manager
{
    public function getDefaultDriver(): string
    {
        return $this->settings()->driver;
    }

    protected function createMarkdownDriver(): ChangelogRepository
    {
        return new MarkdownChangelogRepository(
            $this->container->make(ChangelogParser::class),
            $this->container->make(Filesystem::class),
            $this->settings()->changelogPath(),
        );
    }

    protected function createDatabaseDriver(): ChangelogRepository
    {
        return new DatabaseChangelogRepository(
            $this->container->make(ChangelogParser::class),
            $this->container->make(MarkdownRenderer::class),
            $this->settings()->model,
        );
    }

    /**
     * Every release visible in the given environment, newest first.
     *
     * @return Collection<int, Release>
     */
    public function releases(?string $environment = null): Collection
    {
        $environment ??= $this->config->get('app.env', 'production');

        return $this->cached()
            ->filter(fn (Release $release): bool => $release->isVisibleIn($environment))
            ->values();
    }

    public function latest(?string $environment = null): ?Release
    {
        return $this->releases($environment)->first();
    }

    public function find(string $version, ?string $environment = null): ?Release
    {
        return $this->releases($environment)
            ->first(fn (Release $release): bool => $release->version === $version);
    }

    public function signature(): string
    {
        return $this->driver()->signature();
    }

    public function flush(): void
    {
        Cache::store($this->settings()->cacheStore)->forget($this->settings()->cacheKey);
    }

    /**
     * @return Collection<int, Release>
     */
    protected function cached(): Collection
    {
        $settings = $this->settings();

        if (! $settings->cacheEnabled) {
            return $this->driver()->all();
        }

        return Cache::store($settings->cacheStore)->remember(
            $settings->cacheKey,
            $settings->cacheTtl,
            fn (): Collection => $this->driver()->all(),
        );
    }

    public function settings(): Settings
    {
        return $this->container->make(Settings::class);
    }
}
