<?php

namespace Modules\Core\Filament\Resources\Departments\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class DepartmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Info')
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        Select::make('organization_id')
                            ->label(FilamentUi::field('organization_id'))
                            ->relationship('organization', 'name'),
                        TextInput::make('code')
                            ->label(FilamentUi::field('code')),
                        TextInput::make('name')
                            ->label(FilamentUi::field('name'))
                            ->required(),
                        Textarea::make('description')
                            ->label(FilamentUi::field('description'))
                            ->columnSpanFull(),
                    ]),

                Section::make('Status')
                    ->columns(2)
                    ->schema([
                        Toggle::make('is_active')
                            ->label(FilamentUi::field('is_active'))
                            ->required(),
                    ]),
            ]);
    }
}
