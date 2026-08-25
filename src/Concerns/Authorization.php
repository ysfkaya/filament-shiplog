<?php

namespace Ysfkaya\ShipLog\Concerns;

use Closure;
use Ysfkaya\ShipLog\Support\Authorizer;

trait Authorization
{
    /**
     * Sets both abilities at once. Passing a single condition applies it to
     * viewing and managing alike.
     */
    public function authorize(Closure | bool $view = true, Closure | bool | null $manage = null): static
    {
        return $this
            ->authorizeView($view)
            ->authorizeManage($manage ?? $view);
    }

    public function authorizeView(Closure | bool $condition = true): static
    {
        $this->authorizer()->view($condition);

        return $this;
    }

    public function authorizeManage(Closure | bool $condition = true): static
    {
        $this->authorizer()->manage($condition);

        return $this;
    }

    public function canView(): bool
    {
        return $this->authorizer()->canView();
    }

    public function canManage(): bool
    {
        return $this->authorizer()->canManage();
    }

    protected function authorizer(): Authorizer
    {
        return app(Authorizer::class);
    }
}
