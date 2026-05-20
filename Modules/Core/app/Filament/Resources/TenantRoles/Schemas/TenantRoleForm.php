<?php

namespace Modules\Core\Filament\Resources\TenantRoles\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class TenantRoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Info')
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        TextInput::make('name')
                            ->label(FilamentUi::field('name'))
                            ->required(),
                        TextInput::make('slug')
                            ->label(FilamentUi::field('slug'))
                            ->required(),
                        TextInput::make('level')
                            ->label(FilamentUi::field('level')),
                        Textarea::make('description')
                            ->label(FilamentUi::field('description'))
                            ->columnSpanFull(),
                    ]),

                Section::make('Permissions')
                    ->columns(2)
                    ->schema([
                        Textarea::make('permissions')
                            ->label(FilamentUi::field('permissions'))
                            ->columnSpanFull(),
                    ]),

                Section::make('Settings')
                    ->columns(2)
                    ->schema([
                        Toggle::make('is_default')
                            ->label(FilamentUi::field('is_default'))
                            ->required(),
                        Toggle::make('is_super_admin')
                            ->label(FilamentUi::field('is_super_admin'))
                            ->required(),
                        TextInput::make('dashboard_route')
                            ->label(FilamentUi::field('dashboard_route')),
                    ]),
            ]);
    }
}
