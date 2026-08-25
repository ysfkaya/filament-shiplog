<?php

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Ysfkaya\ShipLog\Contracts\ChangelogRepository;
use Ysfkaya\ShipLog\Data\Release;
use Ysfkaya\ShipLog\Enums\ReleaseStatus;
use Ysfkaya\ShipLog\Models\Release as ReleaseModel;
use Ysfkaya\ShipLog\Repositories\DatabaseChangelogRepository;
use Ysfkaya\ShipLog\Repositories\MarkdownChangelogRepository;

it('defaults to the markdown driver', function (): void {
    expect(shiplog()->driver())->toBeInstanceOf(MarkdownChangelogRepository::class);
});

it('resolves the database driver from config', function (): void {
    config()->set('shiplog.driver', 'database');

    expect(shiplog()->driver())->toBeInstanceOf(DatabaseChangelogRepository::class);
});

it('reads releases out of a changelog file', function (): void {
    changelogFixture("## [2.0.0] - 2026-01-01 — Two\n### Added\n- Thing\n\n## [1.0.0] - 2025-01-01");

    $releases = shiplog()->releases();

    expect($releases)->toHaveCount(2)
        ->and($releases->first()->version)->toBe('2.0.0')
        ->and($releases->first()->title)->toBe('Two');
});

it('returns nothing when the changelog file is missing', function (): void {
    config()->set('shiplog.markdown.path', '/does/not/exist.md');

    expect(shiplog()->releases())->toBeEmpty();
});

it('hides releases that name a different environment', function (): void {
    changelogFixture(<<<'MD'
    ## [2.0.0] - 2026-01-01
    <!-- shiplog: environments: staging -->

    Staging only.

    ## [1.0.0] - 2025-01-01

    Everyone.
    MD);

    expect(shiplog()->releases('production')->pluck('version')->all())->toBe(['1.0.0'])
        ->and(shiplog()->releases('staging')->pluck('version')->all())->toBe(['2.0.0', '1.0.0']);
});

it('falls back to the application environment', function (): void {
    changelogFixture("## [2.0.0] - 2026-01-01\n<!-- shiplog: environments: staging -->");

    config()->set('app.env', 'production');

    expect(shiplog()->releases())->toBeEmpty();
});

it('finds a single release by version', function (): void {
    changelogFixture("## [2.0.0] - 2026-01-01\n\n## [1.0.0] - 2025-01-01");

    expect(shiplog()->find('1.0.0')?->version)->toBe('1.0.0')
        ->and(shiplog()->find('9.9.9'))->toBeNull();
});

it('exposes the newest release', function (): void {
    changelogFixture("## [2.0.0] - 2026-01-01\n\n## [1.0.0] - 2025-01-01");

    expect(shiplog()->latest()?->version)->toBe('2.0.0');
});

it('only reads published releases from the database', function (): void {
    config()->set('shiplog.driver', 'database');

    ReleaseModel::factory()->create(['version' => '2.0.0', 'status' => ReleaseStatus::Published]);
    ReleaseModel::factory()->draft()->create(['version' => '1.9.0']);

    expect(shiplog()->releases()->pluck('version')->all())->toBe(['2.0.0']);
});

it('hides database releases scheduled for the future', function (): void {
    config()->set('shiplog.driver', 'database');

    ReleaseModel::factory()->create(['version' => '3.0.0', 'released_at' => now()->addWeek()]);
    ReleaseModel::factory()->create(['version' => '2.0.0', 'released_at' => now()->subWeek()]);

    expect(shiplog()->releases()->pluck('version')->all())->toBe(['2.0.0']);
});

it('renders database bodies with the same markdown pipeline', function (): void {
    config()->set('shiplog.driver', 'database');

    ReleaseModel::factory()->create([
        'version' => '1.0.0',
        'body' => "> [!TIP]\n> Handy.\n\n### Fixed\n- A bug",
    ]);

    $release = shiplog()->releases()->first();

    expect($release->body)->toContain('sl-alert--success')
        ->and($release->changes[0]->items)->toBe(['A bug']);
});

it('caches releases when caching is enabled', function (): void {
    $path = changelogFixture('## [1.0.0] - 2025-01-01');

    config()->set('shiplog.cache.enabled', true);

    expect(shiplog()->releases())->toHaveCount(1);

    file_put_contents($path, "## [1.0.0] - 2025-01-01\n\n## [2.0.0] - 2026-01-01");

    expect(shiplog()->releases())->toHaveCount(1);

    shiplog()->flush();

    expect(shiplog()->releases())->toHaveCount(2);
});

it('does not touch the cache when caching is disabled', function (): void {
    changelogFixture('## [1.0.0] - 2025-01-01');

    shiplog()->releases();

    expect(Cache::has('shiplog.releases'))->toBeFalse();
});

it('flushes the cache when a release is saved', function (): void {
    config()->set('shiplog.driver', 'database');
    config()->set('shiplog.cache.enabled', true);

    ReleaseModel::factory()->create(['version' => '1.0.0']);

    expect(shiplog()->releases())->toHaveCount(1);

    ReleaseModel::factory()->create(['version' => '2.0.0']);

    expect(shiplog()->releases())->toHaveCount(2);
});

it('signs the markdown changelog with its modification time', function (): void {
    $path = changelogFixture('## [1.0.0] - 2025-01-01');

    expect(shiplog()->signature())->toBe((string) filemtime($path));
});

it('returns an empty signature when there is no changelog file', function (): void {
    config()->set('shiplog.markdown.path', '/does/not/exist.md');

    expect(shiplog()->signature())->toBe('');
});

it('accepts a custom driver', function (): void {
    $manager = shiplog();

    $manager->extend('github', fn (): ChangelogRepository => new class implements ChangelogRepository
    {
        public function all(): Collection
        {
            return collect([new Release(version: '9.9.9')]);
        }

        public function find(string $version): ?Release
        {
            return null;
        }

        public function signature(): string
        {
            return 'static';
        }
    });

    config()->set('shiplog.driver', 'github');

    expect($manager->releases()->first()->version)->toBe('9.9.9')
        ->and($manager->signature())->toBe('static');
});
