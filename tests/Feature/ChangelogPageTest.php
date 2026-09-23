<?php

use Ysfkaya\ShipLog\Filament\Pages\Changelog;
use Ysfkaya\ShipLog\ShipLogPlugin;

use function Pest\Livewire\livewire;

it('is hidden from guests', function (): void {
    expect(Changelog::canAccess())->toBeFalse();

    $this->get('/admin/changelog')->assertForbidden();
});

it('opens for an authorised user', function (): void {
    actingAsUser();

    $this->get('/admin/changelog')->assertSuccessful();
});

it('is forbidden once viewing is revoked', function (): void {
    actingAsUser();

    ShipLogPlugin::make()->authorizeView(false);

    expect(Changelog::canAccess())->toBeFalse();

    $this->get('/admin/changelog')->assertForbidden();
});

it('renders the timeline element pointed at the feed', function (): void {
    actingAsUser();

    $this->get('/admin/changelog')
        ->assertSee('<ship-log', escape: false)
        ->assertSee('mode="inline"', escape: false)
        ->assertSee('theme="class"', escape: false)
        ->assertSee(route('shiplog.feed'), escape: false);
});

it('offers a cache action only while caching is on', function (): void {
    actingAsUser();

    livewire(Changelog::class)->assertActionHidden('flush');

    settings()->cacheEnabled = true;

    livewire(Changelog::class)->assertActionVisible('flush');
});

it('clears the cache from the page', function (): void {
    settings()->cacheEnabled = true;

    changelogFixture('## [1.0.0] - 2025-01-01');

    actingAsUser();

    livewire(Changelog::class)
        ->callAction('flush')
        ->assertNotified();
});

it('links to the resource when releases live in the database', function (): void {
    actingAsUser();

    livewire(Changelog::class)->assertActionVisible('manage');
});

it('uses the slug and labels the plugin was given', function (): void {
    ShipLogPlugin::make()
        ->slug('whats-new')
        ->navigationLabel('Updates')
        ->pageTitle('Product updates');

    expect(Changelog::getSlug())->toBe('whats-new')
        ->and(Changelog::getNavigationLabel())->toBe('Updates');
});
