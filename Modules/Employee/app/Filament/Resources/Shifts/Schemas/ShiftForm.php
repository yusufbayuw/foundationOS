<?php

namespace Modules\Employee\Filament\Resources\Shifts\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;

class ShiftForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant_id'))
                    ->relationship('tenant', 'name')
                    ->required(),
                Select::make('organization_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('organization_id'))
                    ->relationship('organization', 'name')
                    ->required(),
                TextInput::make('code')
                    ->label(\Modules\Core\Support\FilamentUi::field('code'))
                    ->required(),
                TextInput::make('name')
                    ->label(\Modules\Core\Support\FilamentUi::field('name'))
                    ->required(),
                TimePicker::make('start_time')
                    ->label(\Modules\Core\Support\FilamentUi::field('start_time'))
                    ->required(),
                TimePicker::make('end_time')
                    ->label(\Modules\Core\Support\FilamentUi::field('end_time'))
                    ->required(),
                TextInput::make('break_duration_minutes')
                    ->label(\Modules\Core\Support\FilamentUi::field('break_duration_minutes'))
                    ->required()
                    ->numeric()
                    ->default(60),
                TextInput::make('color')
                    ->label(\Modules\Core\Support\FilamentUi::field('color')),
                Toggle::make('is_night_shift')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_night_shift'))
                    ->required(),
                Toggle::make('is_active')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_active'))
                    ->required(),
            ]);
    }
}
