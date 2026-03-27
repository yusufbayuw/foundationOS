<?php

namespace Modules\Campus\Filament\Resources\FeederLogs\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;

class FeederLogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TenantField::make(),
                Select::make('organization_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('organization_id'))
                    ->relationship('organization', 'name'),
                TextInput::make('synced_by')
                    ->label(\Modules\Core\Support\FilamentUi::field('synced_by'))
                    ->numeric(),
                TextInput::make('entity_type')
                    ->label(\Modules\Core\Support\FilamentUi::field('entity_type'))
                    ->required(),
                TextInput::make('entity_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('entity_id'))
                    ->numeric(),
                TextInput::make('action')
                    ->label(\Modules\Core\Support\FilamentUi::field('action'))
                    ->required(),
                TextInput::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status'))
                    ->required()
                    ->default('queued'),
                Textarea::make('request_payload')
                    ->label(\Modules\Core\Support\FilamentUi::field('request_payload'))
                    ->columnSpanFull(),
                Textarea::make('response_payload')
                    ->label(\Modules\Core\Support\FilamentUi::field('response_payload'))
                    ->columnSpanFull(),
                Textarea::make('error_message')
                    ->label(\Modules\Core\Support\FilamentUi::field('error_message'))
                    ->columnSpanFull(),
                DateTimePicker::make('synced_at'),
            ]);
    }
}
