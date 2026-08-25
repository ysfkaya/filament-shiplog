<?php

use Ysfkaya\ShipLog\Enums\ReleaseStatus;
use Ysfkaya\ShipLog\Filament\Resources\Releases\Pages\CreateRelease;
use Ysfkaya\ShipLog\Filament\Resources\Releases\Pages\EditRelease;
use Ysfkaya\ShipLog\Filament\Resources\Releases\Pages\ListReleases;
use Ysfkaya\ShipLog\Filament\Resources\Releases\ReleaseResource;
use Ysfkaya\ShipLog\Models\Release;
use Ysfkaya\ShipLog\ShipLogPlugin;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Livewire\livewire;

beforeEach(function (): void {
    config()->set('shiplog.driver', 'database');
});

it('is closed to guests', function (): void {
    expect(ReleaseResource::canAccess())->toBeFalse();
});

it('is closed to users who may view but not manage', function (): void {
    actingAsUser();

    ShipLogPlugin::make()->authorizeManage(false);

    expect(ReleaseResource::canAccess())->toBeFalse()
        ->and(ReleaseResource::canCreate())->toBeFalse();
});

it('lists releases', function (): void {
    actingAsUser();

    $releases = Release::factory()->count(3)->create();

    livewire(ListReleases::class)
        ->assertCanSeeTableRecords($releases)
        ->assertSuccessful();
});

it('searches releases by version', function (): void {
    actingAsUser();

    $wanted = Release::factory()->create(['version' => '4.2.0']);
    $other = Release::factory()->create(['version' => '1.0.0']);

    livewire(ListReleases::class)
        ->searchTable('4.2.0')
        ->assertCanSeeTableRecords([$wanted])
        ->assertCanNotSeeTableRecords([$other]);
});

it('filters releases by status', function (): void {
    actingAsUser();

    $published = Release::factory()->create();
    $draft = Release::factory()->draft()->create();

    livewire(ListReleases::class)
        ->filterTable('status', ReleaseStatus::Draft->value)
        ->assertCanSeeTableRecords([$draft])
        ->assertCanNotSeeTableRecords([$published]);
});

it('creates a release', function (): void {
    actingAsUser();

    livewire(CreateRelease::class)
        ->fillForm([
            'version' => '5.0.0',
            'title' => 'The big one',
            'body' => "### Added\n- Everything",
            'status' => ReleaseStatus::Published->value,
            'environments' => ['production'],
        ])
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertNotified();

    assertDatabaseHas(Release::class, [
        'version' => '5.0.0',
        'title' => 'The big one',
        'status' => ReleaseStatus::Published->value,
    ]);

    expect(Release::query()->firstWhere('version', '5.0.0')->environments)->toBe(['production']);
});

it('requires a version', function (): void {
    actingAsUser();

    livewire(CreateRelease::class)
        ->fillForm(['version' => null])
        ->call('create')
        ->assertHasFormErrors(['version' => 'required'])
        ->assertNotNotified();
});

it('edits a release', function (): void {
    actingAsUser();

    $release = Release::factory()->create(['version' => '1.0.0']);

    livewire(EditRelease::class, ['record' => $release->getKey()])
        ->fillForm(['title' => 'Renamed'])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertNotified();

    assertDatabaseHas(Release::class, [
        'id' => $release->getKey(),
        'title' => 'Renamed',
    ]);
});

it('publishes an edited release straight to the timeline', function (): void {
    actingAsUser();

    $release = Release::factory()->draft()->create(['version' => '1.0.0']);

    expect(shiplog()->releases())->toBeEmpty();

    livewire(EditRelease::class, ['record' => $release->getKey()])
        ->fillForm(['status' => ReleaseStatus::Published->value])
        ->call('save')
        ->assertHasNoFormErrors();

    expect(shiplog()->releases()->pluck('version')->all())->toBe(['1.0.0']);
});

it('reads its model from config', function (): void {
    expect(ReleaseResource::getModel())->toBe(Release::class);
});
