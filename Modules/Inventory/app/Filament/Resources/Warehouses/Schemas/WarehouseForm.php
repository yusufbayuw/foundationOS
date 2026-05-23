<?php

namespace Modules\Inventory\Filament\Resources\Warehouses\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class WarehouseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(FilamentUi::text('General information'))
                ->columns(2)
                ->schema([
                    TenantField::make(),
                    Select::make('organization_id')
                        ->label(FilamentUi::field('organization_id'))
                        ->relationship('organization', 'name'),
                    TextInput::make('code')
                        ->label(FilamentUi::field('code'))
                        ->required(),
                    TextInput::make('name')
                        ->label(FilamentUi::field('name'))
                        ->required(),
                    Textarea::make('address')
                        ->label(FilamentUi::field('address'))
                        ->columnSpanFull(),
                    Toggle::make('is_default')
                        ->label(FilamentUi::field('is_default')),
                    Toggle::make('is_active')
                        ->label(FilamentUi::field('is_active'))
                        ->default(true),
                ]),
        ]);
    }
}
