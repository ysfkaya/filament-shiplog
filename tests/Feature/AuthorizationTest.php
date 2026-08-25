<?php

use Illuminate\Support\Facades\Gate;
use Ysfkaya\ShipLog\ShipLogPlugin;
use Ysfkaya\ShipLog\Support\Authorizer;

beforeEach(function (): void {
    $this->authorizer = app(Authorizer::class);
});

it('defines default gates', function (): void {
    expect(Gate::has('shiplog.view'))->toBeTrue()
        ->and(Gate::has('shiplog.manage'))->toBeTrue();
});

it('refuses guests by default', function (): void {
    expect($this->authorizer->canView())->toBeFalse()
        ->and($this->authorizer->canManage())->toBeFalse();
});

it('allows signed in users by default', function (): void {
    actingAsUser();

    expect($this->authorizer->canView())->toBeTrue()
        ->and($this->authorizer->canManage())->toBeTrue();
});

it('honours a gate the application already defined', function (): void {
    Gate::define('shiplog.manage', fn (): bool => false);

    app(Authorizer::class)->registerGates();

    actingAsUser();

    expect($this->authorizer->canManage())->toBeFalse()
        ->and($this->authorizer->canView())->toBeTrue();
});

it('lets the plugin override viewing with a boolean', function (): void {
    actingAsUser();

    ShipLogPlugin::make()->authorizeView(false);

    expect($this->authorizer->canView())->toBeFalse()
        ->and($this->authorizer->canManage())->toBeTrue();
});

it('lets the plugin override with a closure receiving the user', function (): void {
    $user = actingAsUser();

    ShipLogPlugin::make()->authorizeManage(fn ($user): bool => $user?->email === 'ada@example.test');

    expect($this->authorizer->canManage())->toBeTrue();

    ShipLogPlugin::make()->authorizeManage(fn ($user): bool => $user?->email === 'someone@else.test');

    expect($this->authorizer->canManage())->toBeFalse();
});

it('sets both abilities at once', function (): void {
    actingAsUser();

    ShipLogPlugin::make()->authorize(false);

    expect($this->authorizer->canView())->toBeFalse()
        ->and($this->authorizer->canManage())->toBeFalse();
});

it('can allow viewing while forbidding management', function (): void {
    actingAsUser();

    ShipLogPlugin::make()->authorize(view: true, manage: false);

    expect($this->authorizer->canView())->toBeTrue()
        ->and($this->authorizer->canManage())->toBeFalse();
});
