<?php

namespace Ysfkaya\ShipLog\Facades;

use Illuminate\Support\Facades\Facade;
use Ysfkaya\ShipLog\ShipLogManager;

/**
 * @method static \Illuminate\Support\Collection<int, \Ysfkaya\ShipLog\Data\Release> releases(?string $environment = null)
 * @method static ?\Ysfkaya\ShipLog\Data\Release latest(?string $environment = null)
 * @method static ?\Ysfkaya\ShipLog\Data\Release find(string $version, ?string $environment = null)
 * @method static \Ysfkaya\ShipLog\Contracts\ChangelogRepository driver(?string $driver = null)
 * @method static \Ysfkaya\ShipLog\ShipLogManager extend(string $driver, \Closure $callback)
 * @method static void flush()
 *
 * @see ShipLogManager
 */
class ShipLog extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ShipLogManager::class;
    }
}
