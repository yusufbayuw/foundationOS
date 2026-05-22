<?php

namespace Modules\Core\Filament\Resources\SubscriptionPlans\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class SubscriptionPlanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('Basic Info'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('code')
                            ->label(FilamentUi::field('code'))
                            ->required(),
                        TextInput::make('name')
                            ->label(FilamentUi::field('name'))
                            ->required(),
                        Textarea::make('description')
                            ->label(FilamentUi::field('description'))
                            ->columnSpanFull(),
                    ]),

                Section::make(FilamentUi::text('Pricing'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('price_monthly')
                            ->label(FilamentUi::field('price_monthly'))
                            ->required()
                            ->numeric()
                            ->default(0),
                        TextInput::make('price_yearly')
                            ->label(FilamentUi::field('price_yearly'))
                            ->required()
                            ->numeric()
                            ->default(0),
                    ]),

                Section::make(FilamentUi::text('Limits'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('max_users')
                            ->label(FilamentUi::field('max_users'))
                            ->required()
                            ->numeric()
                            ->default(1),
                        TextInput::make('max_organizations')
                            ->label(FilamentUi::field('max_organizations'))
                            ->required()
                            ->numeric()
                            ->default(1),
                        TextInput::make('max_storage_gb')
                            ->label(FilamentUi::field('max_storage_gb'))
                            ->required()
                            ->numeric()
                            ->default(1),
                    ]),

                Section::make(FilamentUi::text('Features'))
                    ->columns(2)
                    ->schema([
                        Repeater::make('included_modules')
                            ->label(FilamentUi::field('included_modules'))
                            ->required()
                            ->schema([
                                TextInput::make('code')
                                    ->label(FilamentUi::text('Module Code')),
                                TextInput::make('name')
                                    ->label(FilamentUi::text('Module Name')),
                            ])
                            ->columnSpanFull()
                            ->addActionLabel('Add Item')
                            ->reorderable(),
                        Repeater::make('features')
                            ->label(FilamentUi::field('features'))
                            ->schema([
                                TextInput::make('name')
                                    ->label(FilamentUi::text('Feature')),
                                Textarea::make('description')
                                    ->label(FilamentUi::text('Description')),
                            ])
                            ->columnSpanFull()
                            ->addActionLabel('Add Item')
                            ->reorderable(),
                    ]),

                Section::make(FilamentUi::text('Status'))
                    ->columns(2)
                    ->schema([
                        Toggle::make('is_active')
                            ->label(FilamentUi::field('is_active'))
                            ->required(),
                        Toggle::make('is_recommended')
                            ->label(FilamentUi::field('is_recommended'))
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
