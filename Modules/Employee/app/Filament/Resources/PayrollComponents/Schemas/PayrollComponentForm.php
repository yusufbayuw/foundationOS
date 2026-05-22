<?php

namespace Modules\Employee\Filament\Resources\PayrollComponents\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class PayrollComponentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('General Information'))
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
                        TextInput::make('type')
                            ->label(FilamentUi::field('type')),
                        TextInput::make('category')
                            ->label(FilamentUi::field('category')),
                    ]),

                Section::make(FilamentUi::text('Calculation'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('calculation_type')
                            ->label(FilamentUi::field('calculation_type')),
                        TextInput::make('amount')
                            ->label(FilamentUi::field('amount'))
                            ->numeric(),
                        TextInput::make('percentage')
                            ->label(FilamentUi::field('percentage'))
                            ->numeric(),
                        Textarea::make('formula')
                            ->label(FilamentUi::field('formula'))
                            ->columnSpanFull(),
                    ]),

                Section::make(FilamentUi::text('Settings'))
                    ->columns(2)
                    ->schema([
                        Toggle::make('is_taxable')
                            ->label(FilamentUi::field('is_taxable'))
                            ->required(),
                        Toggle::make('is_mandatory')
                            ->label(FilamentUi::field('is_mandatory'))
                            ->required(),
                        Toggle::make('is_active')
                            ->label(FilamentUi::field('is_active'))
                            ->required(),
                        TextInput::make('display_order')
                            ->label(FilamentUi::field('display_order'))
                            ->required()
                            ->numeric()
                            ->default(0),
                    ]),
            ]);
    }
}
