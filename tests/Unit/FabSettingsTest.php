<?php

use Ysfkaya\ShipLog\Enums\FabPosition;
use Ysfkaya\ShipLog\Support\FabSettings;

it('defaults to the bottom right corner', function (): void {
    $settings = new FabSettings;

    expect($settings->getPosition())->toBe(FabPosition::BottomRight)
        ->and($settings->getLabel())->toBeNull()
        ->and($settings->isEnabled())->toBeTrue();
});

it('takes an explicit position', function (): void {
    expect((new FabSettings)->position(FabPosition::BottomLeft)->getPosition())
        ->toBe(FabPosition::BottomLeft);
});

it('accepts a position as a string', function (): void {
    expect((new FabSettings)->position('top-right')->getPosition())->toBe(FabPosition::TopRight);
});

it('shows in every environment when none are listed', function (): void {
    $settings = new FabSettings;

    expect($settings->isEnabledIn('production'))->toBeTrue()
        ->and($settings->isEnabledIn('local'))->toBeTrue();
});

it('limits itself to the listed environments', function (): void {
    $settings = (new FabSettings)->environments(['staging']);

    expect($settings->isEnabledIn('staging'))->toBeTrue()
        ->and($settings->isEnabledIn('production'))->toBeFalse();
});

it('stays hidden when disabled, whatever the environment says', function (): void {
    expect((new FabSettings)->enabled(false)->isEnabledIn('staging'))->toBeFalse();
});
