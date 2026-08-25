<?php

use Ysfkaya\ShipLog\ShipLogPlugin;

beforeEach(function (): void {
    changelogFixture("## [2.0.0] - 2026-01-01 — Two\n### Added\n- A thing\n\n## [1.0.0] - 2025-01-01");
});

it('refuses guests', function (): void {
    $this->getJson('/shiplog/feed')->assertForbidden();
});

it('returns releases for an authorised user', function (): void {
    actingAsUser();

    $this->getJson('/shiplog/feed')
        ->assertSuccessful()
        ->assertJsonPath('releases.0.version', '2.0.0')
        ->assertJsonPath('releases.0.title', 'Two')
        ->assertJsonPath('releases.0.changes.0.type', 'added')
        ->assertJsonCount(2, 'releases');
});

it('refuses a user the plugin has locked out', function (): void {
    actingAsUser();

    ShipLogPlugin::make()->authorizeView(false);

    $this->getJson('/shiplog/feed')->assertForbidden();
});

it('leaves out releases meant for another environment', function (): void {
    changelogFixture(<<<'MD'
    ## [2.0.0] - 2026-01-01
    <!-- shiplog: environments: staging -->

    ## [1.0.0] - 2025-01-01
    MD);

    config()->set('app.env', 'production');

    actingAsUser();

    $this->getJson('/shiplog/feed')
        ->assertSuccessful()
        ->assertJsonCount(1, 'releases')
        ->assertJsonPath('releases.0.version', '1.0.0');
});

it('sends rendered html rather than raw markdown', function (): void {
    changelogFixture("## [1.0.0] - 2025-01-01\n\n> [!WARNING]\n> Careful.");

    actingAsUser();

    expect($this->getJson('/shiplog/feed')->json('releases.0.body'))
        ->toContain('sl-alert--warning');
});
