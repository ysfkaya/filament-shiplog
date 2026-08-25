<?php

namespace Ysfkaya\ShipLog\Support;

use Closure;
use Filament\Support\Concerns\EvaluatesClosures;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;

/**
 * The single place that answers "may this user see or edit the changelog?".
 *
 * Both the Filament page and the public timeline ask this object, so a
 * `->authorizeView()` call on the plugin also hides the floating button.
 * Without an override it falls back to the configured gates.
 */
class Authorizer
{
    use EvaluatesClosures;

    protected Closure | bool | null $view = null;

    protected Closure | bool | null $manage = null;

    public function __construct(
        protected readonly Gate $gate,
    ) {}

    public function view(Closure | bool $condition = true): static
    {
        $this->view = $condition;

        return $this;
    }

    public function manage(Closure | bool $condition = true): static
    {
        $this->manage = $condition;

        return $this;
    }

    public function canView(?Authenticatable $user = null): bool
    {
        return $this->decide($this->view, config('shiplog.gates.view', 'shiplog.view'), $user);
    }

    public function canManage(?Authenticatable $user = null): bool
    {
        return $this->decide($this->manage, config('shiplog.gates.manage', 'shiplog.manage'), $user);
    }

    /**
     * Defines the default gates, leaving any the application already owns
     * completely alone.
     */
    public function registerGates(): void
    {
        foreach ([config('shiplog.gates.view'), config('shiplog.gates.manage')] as $ability) {
            if (blank($ability) || $this->gate->has($ability)) {
                continue;
            }

            $this->gate->define($ability, fn (?Authenticatable $user = null): bool => $user !== null);
        }
    }

    protected function decide(Closure | bool | null $override, ?string $ability, ?Authenticatable $user): bool
    {
        $user ??= Auth::user();

        if ($override !== null) {
            return (bool) $this->evaluate($override, ['user' => $user], [Authenticatable::class => $user]);
        }

        if (blank($ability)) {
            return true;
        }

        return $this->gate->forUser($user)->allows($ability);
    }
}
