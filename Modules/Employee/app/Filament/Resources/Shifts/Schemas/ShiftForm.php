<?php

namespace Modules\Employee\Filament\Resources\Shifts\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class ShiftForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('General Information')
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        Select::make('organization_id')
                            ->label(FilamentUi::field('organization_id'))
                            ->relationship('organization', 'name')
                            ->required(),
                        TextInput::make('code')
                            ->label(FilamentUi::field('code'))
                            ->required(),
                        TextInput::make('name')
                            ->label(FilamentUi::field('name'))
                            ->required(),
                        TextInput::make('color')
                            ->label(FilamentUi::field('color')),
                    ]),

                Section::make('Shift Schedule')
                    ->columns(2)
                    ->schema([
                        TimePicker::make('start_time')
                            ->label(FilamentUi::field('start_time'))
                            ->required(),
                        TimePicker::make('end_time')
                            ->label(FilamentUi::field('end_time'))
                            ->required(),
                        TextInput::make('break_duration_minutes')
                            ->label(FilamentUi::field('break_duration_minutes'))
                            ->required()
                            ->numeric()
                            ->default(60),
                    ]),

                Section::make('Settings')
                    ->columns(2)
                    ->schema([
                        Toggle::make('is_night_shift')
                            ->label(FilamentUi::field('is_night_shift'))
                            ->required(),
                        Toggle::make('is_active')
                            ->label(FilamentUi::field('is_active'))
                            ->required(),
                    ]),
            ]);
    }
}
