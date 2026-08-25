<?php

use Ysfkaya\ShipLog\Models\Release;
use Ysfkaya\ShipLog\Repositories\DatabaseChangelogRepository;
use Ysfkaya\ShipLog\ShipLogPlugin;

it('switches to the database driver', function (): void {
    ShipLogPlugin::make()->usingDatabase();

    expect(settings()->driver)->toBe('database')
        ->and(settings()->model)->toBe(Release::class)
        ->and(shiplog()->driver())->toBeInstanceOf(DatabaseChangelogRepository::class);
});

it('points the markdown driver at a file', function (): void {
    ShipLogPlugin::make()->usingMarkdown('/tmp/notes.md');

    expect(settings()->driver)->toBe('markdown')
        ->and(settings()->markdownPath)->toBe('/tmp/notes.md');
});

it('registers the resource once the driver is switched', function (): void {
    ShipLogPlugin::make()->usingDatabase();

    expect(ShipLogPlugin::make()->hasResource())->toBeTrue();
});

it('configures caching and page size', function (): void {
    ShipLogPlugin::make()
        ->cache(true, ttl: 60, store: 'array')
        ->perPage(5)
        ->allowRawHtml();

    expect(settings()->cacheEnabled)->toBeTrue()
        ->and(settings()->cacheTtl)->toBe(60)
        ->and(settings()->cacheStore)->toBe('array')
        ->and(settings()->perPage)->toBe(5)
        ->and(settings()->allowRawHtml)->toBeTrue();
});
