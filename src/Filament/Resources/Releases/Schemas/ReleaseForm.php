<?php

namespace Ysfkaya\ShipLog\Filament\Resources\Releases\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Ysfkaya\ShipLog\Enums\ReleaseStatus;

class ReleaseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('shiplog::shiplog.form.details'))
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('version')
                            ->label(__('shiplog::shiplog.form.version'))
                            ->placeholder('2.1.0')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('title')
                            ->label(__('shiplog::shiplog.form.title'))
                            ->placeholder(__('shiplog::shiplog.form.title_placeholder'))
                            ->maxLength(255),

                        DatePicker::make('released_at')
                            ->label(__('shiplog::shiplog.form.released_at'))
                            ->helperText(__('shiplog::shiplog.form.released_at_hint'))
                            ->native(false),

                        Select::make('status')
                            ->label(__('shiplog::shiplog.form.status'))
                            ->options(ReleaseStatus::class)
                            ->default(ReleaseStatus::Draft)
                            ->selectablePlaceholder(false)
                            ->required(),

                        TagsInput::make('environments')
                            ->label(__('shiplog::shiplog.form.environments'))
                            ->helperText(__('shiplog::shiplog.form.environments_hint'))
                            ->suggestions(['local', 'staging', 'production'])
                            ->columnSpanFull(),

                        Toggle::make('yanked')
                            ->label(__('shiplog::shiplog.form.yanked'))
                            ->helperText(__('shiplog::shiplog.form.yanked_hint'))
                            ->columnSpanFull(),
                    ]),

                Section::make(__('shiplog::shiplog.form.body'))
                    ->description(__('shiplog::shiplog.form.body_hint'))
                    ->columnSpanFull()
                    ->schema([
                        MarkdownEditor::make('body')
                            ->label('')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
