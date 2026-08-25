<?php

namespace Ysfkaya\ShipLog\Enums;

use Filament\Support\Contracts\HasLabel;

enum FabPosition: string implements HasLabel
{
    case TopLeft = 'top-left';
    case TopRight = 'top-right';
    case BottomLeft = 'bottom-left';
    case BottomRight = 'bottom-right';

    public function getLabel(): string
    {
        return __("shiplog::shiplog.fab_position.{$this->value}");
    }
}
