<?php

namespace Modules\Campus\Filament\Resources\FeederLogs\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class FeederLogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('Log Identity'))
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        Select::make('organization_id')
                            ->label(FilamentUi::field('organization_id'))
                            ->relationship('organization', 'name'),
                        TextInput::make('entity_type')
                            ->label(FilamentUi::field('entity_type'))
                            ->required(),
                        TextInput::make('entity_id')
                            ->label(FilamentUi::field('entity_id'))
                            ->numeric(),
                        TextInput::make('action')
                            ->label(FilamentUi::field('action'))
                            ->required(),
                        TextInput::make('status')
                            ->label(FilamentUi::field('status'))
                            ->required()
                            ->default('queued'),
                        TextInput::make('synced_by')
                            ->label(FilamentUi::field('synced_by'))
                            ->numeric(),
                        DateTimePicker::make('synced_at'),
                    ]),

                Section::make(FilamentUi::text('Payload & Messages'))
                    ->columns(2)
                    ->schema([
                        Textarea::make('request_payload')
                            ->label(FilamentUi::field('request_payload'))
                            ->columnSpanFull(),
                        Textarea::make('response_payload')
                            ->label(FilamentUi::field('response_payload'))
                            ->columnSpanFull(),
                        Textarea::make('error_message')
                            ->label(FilamentUi::field('error_message'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
