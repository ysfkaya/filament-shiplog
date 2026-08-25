<?php

use Illuminate\Support\Facades\Route;
use Ysfkaya\ShipLog\Http\InjectShipLog;

beforeEach(function (): void {
    changelogFixture('## [1.0.0] - 2025-01-01');

    Route::middleware(['web', InjectShipLog::class])->get('/injected', fn (): string => '<html><body><h1>Hi</h1></body></html>');
    Route::middleware(['web', InjectShipLog::class])->get('/injected.json', fn (): array => ['ok' => true]);
});

it('adds the timeline before the closing body tag', function (): void {
    actingAsUser();

    $html = $this->get('/injected')->assertSuccessful()->getContent();

    expect($html)->toContain('<ship-log')
        ->and($html)->toEndWith('</body></html>')
        ->and(strpos($html, '<ship-log'))->toBeGreaterThan(strpos($html, '<h1>Hi</h1>'));
});

it('leaves the page alone for guests', function (): void {
    expect($this->get('/injected')->getContent())->not->toContain('<ship-log');
});

it('never touches a json response', function (): void {
    actingAsUser();

    $this->get('/injected.json')->assertSuccessful()->assertExactJson(['ok' => true]);
});
