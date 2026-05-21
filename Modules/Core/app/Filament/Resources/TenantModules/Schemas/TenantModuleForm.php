<?php

namespace Modules\Core\Filament\Resources\TenantModules\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class TenantModuleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Info')
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        Select::make('module_id')
                            ->label(FilamentUi::field('module_id'))
                            ->relationship('module', 'name')
                            ->required(),
                    ]),

                Section::make('Status')
                    ->columns(2)
                    ->schema([
                        Toggle::make('is_enabled')
                            ->label(FilamentUi::field('is_enabled'))
                            ->required(),
                        DateTimePicker::make('enabled_at'),
                        DateTimePicker::make('disabled_at'),
                    ]),

                Section::make('Settings')
                    ->columns(2)
                    ->schema([
                        Textarea::make('settings')
                            ->label(FilamentUi::field('settings'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
