<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Ysfkaya\ShipLog\Http\InjectShipLog;

beforeEach(function (): void {
    changelogFixture('## [1.0.0] - 2025-01-01');

    config()->set('inertia.testing.ensure_pages_exist', false);

    Inertia::setRootView('inertia');

    Route::middleware(['web', InjectShipLog::class])
        ->get('/inertia', fn () => Inertia::render('Dashboard'));
});

it('injects into the initial inertia page load', function (): void {
    actingAsUser();

    $html = $this->get('/inertia')->assertSuccessful()->getContent();

    expect($html)->toContain('<ship-log');
});

it('leaves inertia visit responses untouched', function (): void {
    actingAsUser();

    $response = $this->withHeaders([
        'X-Inertia' => 'true',
        'X-Inertia-Version' => '',
    ])->get('/inertia');

    $response->assertSuccessful();

    expect($response->getContent())->not->toContain('<ship-log');
});
