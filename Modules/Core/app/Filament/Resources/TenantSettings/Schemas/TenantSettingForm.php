<?php

namespace Modules\Core\Filament\Resources\TenantSettings\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class TenantSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant_id'))
                    ->relationship('tenant', 'name')
                    ->required(),
                TextInput::make('group')
                    ->label(\Modules\Core\Support\FilamentUi::field('group')),
                TextInput::make('key')
                    ->label(\Modules\Core\Support\FilamentUi::field('key'))
                    ->required(),
                Textarea::make('value')
                    ->label(\Modules\Core\Support\FilamentUi::field('value'))
                    ->columnSpanFull(),
                TextInput::make('type')
                    ->label(\Modules\Core\Support\FilamentUi::field('type'))
                    ->required()
                    ->default('string'),
            ]);
    }
}
