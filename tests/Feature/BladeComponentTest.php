<?php

use Illuminate\Support\Facades\Blade;
use Ysfkaya\ShipLog\Enums\FabPosition;
use Ysfkaya\ShipLog\ShipLogPlugin;
use Ysfkaya\ShipLog\Support\FabSettings;

beforeEach(function (): void {
    changelogFixture('## [1.0.0] - 2025-01-01');
});

it('renders nothing for guests', function (): void {
    expect(trim(Blade::render('<x-shiplog />')))->toBe('');
});

it('renders the element for an authorised user', function (): void {
    actingAsUser();

    $html = Blade::render('<x-shiplog />');

    expect($html)->toContain('<ship-log')
        ->and($html)->toContain('position="bottom-right"')
        ->and($html)->toContain(route('shiplog.feed'))
        ->and($html)->toContain('fi-shiplog.js');
});

it('uses the position the plugin configured', function (): void {
    actingAsUser();

    ShipLogPlugin::make()->fab(FabPosition::TopLeft);

    expect(Blade::render('<x-shiplog />'))->toContain('position="top-left"');
});

it('lets an attribute win over the configured position', function (): void {
    actingAsUser();

    expect(Blade::render('<x-shiplog position="top-right" />'))->toContain('position="top-right"');
});

it('stays hidden in environments the button is not meant for', function (): void {
    actingAsUser();

    app(FabSettings::class)->environments(['staging']);

    app()['env'] = 'production';

    expect(trim(Blade::render('<x-shiplog />')))->toBe('');
});

it('stays hidden once viewing is revoked', function (): void {
    actingAsUser();

    ShipLogPlugin::make()->authorizeView(false);

    expect(trim(Blade::render('<x-shiplog />')))->toBe('');
});

it('carries a signature so the button can flag unread releases', function (): void {
    actingAsUser();

    expect(Blade::render('<x-shiplog />'))->toMatch('/signature="\d+"/');
});
