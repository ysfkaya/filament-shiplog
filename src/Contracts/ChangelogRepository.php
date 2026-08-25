<?php

namespace Ysfkaya\ShipLog\Contracts;

use Illuminate\Support\Collection;
use Ysfkaya\ShipLog\Data\Release;

interface ChangelogRepository
{
    /**
     * Every release the driver knows about, newest first.
     *
     * @return Collection<int, Release>
     */
    public function all(): Collection;

    public function find(string $version): ?Release;
}
