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
        protected readonly Settings $settings,
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
        return $this->decide($this->view, $this->settings->viewGate, $user);
    }

    public function canManage(?Authenticatable $user = null): bool
    {
        return $this->decide($this->manage, $this->settings->manageGate, $user);
    }

    /**
     * Defines the default gates, leaving any the application already owns
     * completely alone.
     */
    public function registerGates(): void
    {
        foreach ([$this->settings->viewGate, $this->settings->manageGate] as $ability) {
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
