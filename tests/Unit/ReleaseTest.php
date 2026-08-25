<?php

use Carbon\CarbonImmutable;
use Ysfkaya\ShipLog\Data\ChangeGroup;
use Ysfkaya\ShipLog\Data\Release;
use Ysfkaya\ShipLog\Enums\ChangeType;

it('is visible everywhere when no environment is set', function (): void {
    $release = new Release(version: '1.0.0');

    expect($release->isVisibleIn('production'))->toBeTrue()
        ->and($release->isVisibleIn('local'))->toBeTrue();
});

it('is only visible in the environments it names', function (): void {
    $release = new Release(version: '1.0.0', environments: ['staging']);

    expect($release->isVisibleIn('staging'))->toBeTrue()
        ->and($release->isVisibleIn('production'))->toBeFalse();
});

it('serialises everything the timeline needs', function (): void {
    $release = new Release(
        version: '2.0.0',
        title: 'Big one',
        releasedAt: CarbonImmutable::parse('2026-04-05'),
        body: '<p>Notes</p>',
        changes: [new ChangeGroup(ChangeType::Added, ['One', 'Two'])],
        yanked: true,
        id: 7,
    );

    expect($release->toArray())
        ->toMatchArray([
            'id' => 7,
            'version' => '2.0.0',
            'title' => 'Big one',
            'body' => '<p>Notes</p>',
            'yanked' => true,
        ])
        ->and($release->toArray()['released_at'])->toStartWith('2026-04-05')
        ->and($release->toArray()['changes'][0])->toMatchArray([
            'type' => 'added',
            'count' => 2,
            'accent' => '#10b981',
        ]);
});

it('falls back to the version as an identifier', function (): void {
    expect((new Release(version: '9.9.9'))->toArray()['id'])->toBe('9.9.9');
});
