<?php

namespace Modules\Procurement\Filament\Resources\ProcurementCategories\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class ProcurementCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Category Information')
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        Select::make('organization_id')
                            ->label(FilamentUi::field('organization_id'))
                            ->relationship('organization', 'name'),
                        Select::make('parent_id')
                            ->label(FilamentUi::field('parent_id'))
                            ->relationship('parent', 'name'),
                        TextInput::make('code')
                            ->label(FilamentUi::field('code')),
                        TextInput::make('name')
                            ->label(FilamentUi::field('name'))
                            ->required(),
                        TextInput::make('type')
                            ->label(FilamentUi::field('type'))
                            ->required()
                            ->default('general'),
                    ]),

                Section::make('Details')
                    ->columns(1)
                    ->schema([
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
