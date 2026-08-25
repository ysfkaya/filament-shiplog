<?php

use Ysfkaya\ShipLog\Models\Release;
use Ysfkaya\ShipLog\Repositories\DatabaseChangelogRepository;
use Ysfkaya\ShipLog\ShipLogPlugin;

it('switches to the database driver', function (): void {
    ShipLogPlugin::make()->usingDatabase();

    expect(config('shiplog.driver'))->toBe('database')
        ->and(config('shiplog.model'))->toBe(Release::class)
        ->and(shiplog()->driver())->toBeInstanceOf(DatabaseChangelogRepository::class);
});

it('points the markdown driver at a file', function (): void {
    ShipLogPlugin::make()->usingMarkdown('/tmp/notes.md');

    expect(config('shiplog.driver'))->toBe('markdown')
        ->and(config('shiplog.markdown.path'))->toBe('/tmp/notes.md');
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

    expect(config('shiplog.cache.enabled'))->toBeTrue()
        ->and(config('shiplog.cache.ttl'))->toBe(60)
        ->and(config('shiplog.cache.store'))->toBe('array')
        ->and(config('shiplog.per_page'))->toBe(5)
        ->and(config('shiplog.markdown.allow_html'))->toBeTrue();
});
