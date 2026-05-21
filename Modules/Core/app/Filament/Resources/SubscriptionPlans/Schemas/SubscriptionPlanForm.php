<?php

namespace Modules\Core\Filament\Resources\SubscriptionPlans\Schemas;

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
                Section::make('Basic Info')
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

                Section::make('Pricing')
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

                Section::make('Limits')
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

                Section::make('Features')
                    ->columns(2)
                    ->schema([
                        Textarea::make('included_modules')
                            ->label(FilamentUi::field('included_modules'))
                            ->required()
                            ->columnSpanFull(),
                        Textarea::make('features')
                            ->label(FilamentUi::field('features'))
                            ->columnSpanFull(),
                    ]),

                Section::make('Status')
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
