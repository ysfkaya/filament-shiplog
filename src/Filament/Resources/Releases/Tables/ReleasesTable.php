<?php

namespace Ysfkaya\ShipLog\Filament\Resources\Releases\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Ysfkaya\ShipLog\Enums\ReleaseStatus;

class ReleasesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('released_at', 'desc')
            ->columns([
                TextColumn::make('version')
                    ->label(__('shiplog::shiplog.form.version'))
                    ->weight('bold')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('title')
                    ->label(__('shiplog::shiplog.form.title'))
                    ->searchable()
                    ->limit(48)
                    ->placeholder('—'),

                TextColumn::make('released_at')
                    ->label(__('shiplog::shiplog.form.released_at'))
                    ->date()
                    ->sortable()
                    ->placeholder(__('shiplog::shiplog.table.unreleased')),

                TextColumn::make('status')
                    ->label(__('shiplog::shiplog.form.status'))
                    ->badge()
                    ->sortable(),

                TextColumn::make('environments')
                    ->label(__('shiplog::shiplog.form.environments'))
                    ->badge()
                    ->separator(',')
                    ->placeholder(__('shiplog::shiplog.table.all_environments')),

                IconColumn::make('yanked')
                    ->label(__('shiplog::shiplog.form.yanked'))
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('shiplog::shiplog.form.status'))
                    ->options(ReleaseStatus::class),

                TernaryFilter::make('yanked')
                    ->label(__('shiplog::shiplog.form.yanked')),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
