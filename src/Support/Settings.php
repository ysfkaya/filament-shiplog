<?php

namespace Ysfkaya\ShipLog\Support;

use Ysfkaya\ShipLog\Models\Release;

/**
 * Every Ship Log setting, owned by the plugin.
 *
 * There is no config file: the plugin is the single place things are
 * configured, and this object is where those calls land so the manager,
 * repositories, gates and routes can all read the same answers.
 */
class Settings
{
    public string $driver = 'markdown';

    public ?string $markdownPath = null;

    public bool $allowRawHtml = false;

    /** @var class-string */
    public string $model = Release::class;

    public string $table = 'shiplog_releases';

    public int $perPage = 15;

    public bool $cacheEnabled = false;

    public ?string $cacheStore = null;

    public int $cacheTtl = 3600;

    public string $cacheKey = 'shiplog.releases';

    public string $viewGate = 'shiplog.view';

    public string $manageGate = 'shiplog.manage';

    public bool $routesEnabled = true;

    public string $routePrefix = 'shiplog';

    /** @var array<int, string> */
    public array $routeMiddleware = ['web'];

    public function changelogPath(): string
    {
        return $this->markdownPath ?? base_path('CHANGELOG.md');
    }
}
