<?php

namespace Ysfkaya\ShipLog\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;

enum ChangeType: string implements HasColor, HasIcon, HasLabel
{
    case Added = 'added';
    case Changed = 'changed';
    case Deprecated = 'deprecated';
    case Removed = 'removed';
    case Fixed = 'fixed';
    case Security = 'security';

    public static function fromHeading(string $heading): ?self
    {
        return self::tryFrom(mb_strtolower(trim($heading)));
    }

    public function getLabel(): string
    {
        return __("shiplog::shiplog.change_type.{$this->value}");
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Added => 'success',
            self::Changed => 'warning',
            self::Deprecated => 'gray',
            self::Removed => 'danger',
            self::Fixed => 'info',
            self::Security => 'primary',
        };
    }

    public function getIcon(): Heroicon
    {
        return match ($this) {
            self::Added => Heroicon::Sparkles,
            self::Changed => Heroicon::ArrowPath,
            self::Deprecated => Heroicon::ArchiveBox,
            self::Removed => Heroicon::Trash,
            self::Fixed => Heroicon::Wrench,
            self::Security => Heroicon::ShieldCheck,
        };
    }

    /**
     * The accent colour used by the public timeline, which renders inside a
     * shadow root and therefore cannot rely on Filament's CSS variables.
     */
    public function getAccent(): string
    {
        return match ($this) {
            self::Added => '#10b981',
            self::Changed => '#f59e0b',
            self::Deprecated => '#71717a',
            self::Removed => '#f43f5e',
            self::Fixed => '#0ea5e9',
            self::Security => '#8b5cf6',
        };
    }
}
