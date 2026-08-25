<?php

namespace Ysfkaya\ShipLog\Filament\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Panel;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use UnitEnum;
use Ysfkaya\ShipLog\Facades\ShipLog;
use Ysfkaya\ShipLog\Filament\Resources\Releases\ReleaseResource;
use Ysfkaya\ShipLog\ShipLogPlugin;
use Ysfkaya\ShipLog\Support\Authorizer;
use Ysfkaya\ShipLog\Support\Settings;

class Changelog extends Page
{
    protected string $view = 'shiplog::filament.changelog';

    public static function canAccess(): bool
    {
        return app(Authorizer::class)->canView();
    }

    public static function getSlug(?Panel $panel = null): string
    {
        return ShipLogPlugin::get()->getSlug() ?? 'changelog';
    }

    public static function getNavigationLabel(): string
    {
        return ShipLogPlugin::get()->getNavigationLabel() ?? __('shiplog::shiplog.navigation.label');
    }

    public static function getNavigationIcon(): string | BackedEnum | Htmlable | null
    {
        return ShipLogPlugin::get()->getNavigationIcon() ?? Heroicon::OutlinedMegaphone;
    }

    public static function getNavigationGroup(): string | UnitEnum | null
    {
        return ShipLogPlugin::get()->getNavigationGroup();
    }

    public static function getNavigationSort(): ?int
    {
        return ShipLogPlugin::get()->getNavigationSort();
    }

    public function getTitle(): string | Htmlable
    {
        return ShipLogPlugin::get()->getPageTitle() ?? __('shiplog::shiplog.navigation.title');
    }

    /**
     * @return array<int, Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('manage')
                ->label(__('shiplog::shiplog.actions.manage'))
                ->icon(Heroicon::PencilSquare)
                ->url(fn (): string => ReleaseResource::getUrl())
                ->visible(fn (): bool => ShipLogPlugin::get()->hasResource() && ShipLogPlugin::get()->canManage()),

            Action::make('flush')
                ->label(__('shiplog::shiplog.actions.flush'))
                ->icon(Heroicon::ArrowPath)
                ->color('gray')
                ->visible(fn (): bool => app(Settings::class)->cacheEnabled && ShipLogPlugin::get()->canManage())
                ->action(function (): void {
                    ShipLog::flush();

                    Notification::make()
                        ->title(__('shiplog::shiplog.actions.flushed'))
                        ->success()
                        ->send();
                }),
        ];
    }
}
