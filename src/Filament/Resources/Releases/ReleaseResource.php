<?php

namespace Ysfkaya\ShipLog\Filament\Resources\Releases;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;
use Ysfkaya\ShipLog\Filament\Resources\Releases\Pages\CreateRelease;
use Ysfkaya\ShipLog\Filament\Resources\Releases\Pages\EditRelease;
use Ysfkaya\ShipLog\Filament\Resources\Releases\Pages\ListReleases;
use Ysfkaya\ShipLog\Filament\Resources\Releases\Schemas\ReleaseForm;
use Ysfkaya\ShipLog\Filament\Resources\Releases\Tables\ReleasesTable;
use Ysfkaya\ShipLog\ShipLogPlugin;
use Ysfkaya\ShipLog\Support\Authorizer;

class ReleaseResource extends Resource
{
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedRocketLaunch;

    public static function getModel(): string
    {
        return config('shiplog.model', \Ysfkaya\ShipLog\Models\Release::class);
    }

    public static function getSlug(?\Filament\Panel $panel = null): string
    {
        return 'shiplog-releases';
    }

    public static function getModelLabel(): string
    {
        return __('shiplog::shiplog.resource.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('shiplog::shiplog.resource.plural_label');
    }

    public static function getNavigationGroup(): string | UnitEnum | null
    {
        return ShipLogPlugin::get()->getNavigationGroup();
    }

    public static function form(Schema $schema): Schema
    {
        return ReleaseForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ReleasesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListReleases::route('/'),
            'create' => CreateRelease::route('/create'),
            'edit' => EditRelease::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        return app(Authorizer::class)->canManage();
    }

    public static function canViewAny(): bool
    {
        return static::canAccess();
    }

    public static function canCreate(): bool
    {
        return static::canAccess();
    }

    public static function canEdit(Model $record): bool
    {
        return static::canAccess();
    }

    public static function canDelete(Model $record): bool
    {
        return static::canAccess();
    }

    public static function canDeleteAny(): bool
    {
        return static::canAccess();
    }
}
