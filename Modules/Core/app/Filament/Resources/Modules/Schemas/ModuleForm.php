<?php

namespace Modules\Core\Filament\Resources\Modules\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class ModuleForm
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
                        TextInput::make('slug')
                            ->label(FilamentUi::field('slug'))
                            ->required(),
                        TextInput::make('name')
                            ->label(FilamentUi::field('name'))
                            ->required(),
                        Textarea::make('description')
                            ->label(FilamentUi::field('description'))
                            ->columnSpanFull(),
                    ]),

                Section::make('Appearance')
                    ->columns(2)
                    ->schema([
                        TextInput::make('icon')
                            ->label(FilamentUi::field('icon')),
                        TextInput::make('color')
                            ->label(FilamentUi::field('color')),
                        TextInput::make('version')
                            ->label(FilamentUi::field('version')),
                    ]),

                Section::make('Status')
                    ->columns(2)
                    ->schema([
                        Toggle::make('is_core')
                            ->label(FilamentUi::field('is_core'))
                            ->required(),
                        Toggle::make('is_active')
                            ->label(FilamentUi::field('is_active'))
                            ->required(),
                        Toggle::make('is_premium')
                            ->label(FilamentUi::field('is_premium'))
                            ->required(),
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

                Section::make('Settings')
                    ->columns(2)
                    ->schema([
                        Textarea::make('settings_schema')
                            ->label(FilamentUi::field('settings_schema'))
                            ->columnSpanFull(),
                        Textarea::make('required_modules')
                            ->label(FilamentUi::field('required_modules'))
                            ->columnSpanFull(),
                        TextInput::make('sort_order')
                            ->label(FilamentUi::field('sort_order'))
                            ->required()
                            ->numeric()
                            ->default(0),
                    ]),
            ]);
    }
}
