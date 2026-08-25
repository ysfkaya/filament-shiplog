<?php

namespace Ysfkaya\ShipLog\Filament\Resources\Releases\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Ysfkaya\ShipLog\Filament\Resources\Releases\ReleaseResource;

class EditRelease extends EditRecord
{
    protected static string $resource = ReleaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
