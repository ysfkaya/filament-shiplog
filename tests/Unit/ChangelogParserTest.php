<?php

use Ysfkaya\ShipLog\Enums\ChangeType;

it('reads a bracketed version, date and headline', function (): void {
    $releases = parser()->parse(<<<'MD'
    # Changelog

    Some preamble that is not a release.

    ## [2.1.0] - 2026-08-01 — Dark Mode
    ### Added
    - A thing
    MD);

    expect($releases)->toHaveCount(1)
        ->and($releases[0]->version)->toBe('2.1.0')
        ->and($releases[0]->title)->toBe('Dark Mode')
        ->and($releases[0]->releasedAt?->toDateString())->toBe('2026-08-01');
});

it('reads a bare version without brackets', function (): void {
    $releases = parser()->parse("## 1.0.0 - 2024-01-01\n### Fixed\n- Nothing");

    expect($releases[0]->version)->toBe('1.0.0')
        ->and($releases[0]->releasedAt?->toDateString())->toBe('2024-01-01');
});

it('treats a dateless release as unreleased', function (): void {
    $releases = parser()->parse("## [Unreleased]\n### Added\n- Soon");

    expect($releases[0]->version)->toBe('Unreleased')
        ->and($releases[0]->isUnreleased())->toBeTrue()
        ->and($releases[0]->releasedAt)->toBeNull();
});

it('keeps the version when the heading links to a compare url', function (): void {
    $releases = parser()->parse('## [2.0.0](https://example.test/compare) - 2026-01-01');

    expect($releases[0]->version)->toBe('2.0.0');
});

it('flags yanked releases and keeps the marker out of the title', function (): void {
    $releases = parser()->parse('## [1.4.0] - 2026-02-02 [YANKED]');

    expect($releases[0]->yanked)->toBeTrue()
        ->and($releases[0]->title)->toBeNull();
});

it('reads environment directives and strips them from the body', function (): void {
    $releases = parser()->parse(<<<'MD'
    ## [3.0.0] - 2026-03-03
    <!-- shiplog: environments: staging, local -->

    Body text.
    MD);

    expect($releases[0]->environments)->toBe(['staging', 'local'])
        ->and($releases[0]->body)->not->toContain('shiplog:')
        ->and($releases[0]->body)->toContain('Body text.');
});

it('groups top level bullets by change type', function (): void {
    $releases = parser()->parse(<<<'MD'
    ## [1.0.0] - 2026-01-01

    ### Added
    - One
    - Two
      - A nested detail that belongs to Two

    ### Security
    - Patched something
    MD);

    $changes = collect($releases[0]->changes)->keyBy(fn ($group): string => $group->type->value);

    expect($changes)->toHaveCount(2)
        ->and($changes['added']->count())->toBe(2)
        ->and($changes['security']->type)->toBe(ChangeType::Security)
        ->and($changes['added']->items[0])->toBe('One');
});

it('renders bullet markdown inside change groups', function (): void {
    $releases = parser()->parse("## [1.0.0] - 2026-01-01\n### Fixed\n- A **bold** fix");

    expect($releases[0]->changes[0]->items[0])->toBe('A <strong>bold</strong> fix');
});

it('ignores unknown change headings', function (): void {
    $releases = parser()->parse("## [1.0.0] - 2026-01-01\n### Notes\n- Not a change type");

    expect($releases[0]->changes)->toBeEmpty();
});

it('returns nothing for a document without releases', function (): void {
    expect(parser()->parse("# Changelog\n\nNothing here yet."))->toBeEmpty();
});
