<?php

namespace Ysfkaya\ShipLog\Filament\Resources\Releases\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Ysfkaya\ShipLog\Filament\Resources\Releases\ReleaseResource;

class ListReleases extends ListRecords
{
    protected static string $resource = ReleaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
