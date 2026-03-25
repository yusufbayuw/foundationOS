<?php

namespace Modules\Core\Filament\Resources\Modules\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ModuleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->label(\Modules\Core\Support\FilamentUi::field('code'))
                    ->required(),
                TextInput::make('slug')
                    ->label(\Modules\Core\Support\FilamentUi::field('slug'))
                    ->required(),
                TextInput::make('name')
                    ->label(\Modules\Core\Support\FilamentUi::field('name'))
                    ->required(),
                Textarea::make('description')
                    ->label(\Modules\Core\Support\FilamentUi::field('description'))
                    ->columnSpanFull(),
                TextInput::make('icon')
                    ->label(\Modules\Core\Support\FilamentUi::field('icon')),
                TextInput::make('color')
                    ->label(\Modules\Core\Support\FilamentUi::field('color')),
                TextInput::make('version')
                    ->label(\Modules\Core\Support\FilamentUi::field('version')),
                Toggle::make('is_core')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_core'))
                    ->required(),
                Toggle::make('is_active')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_active'))
                    ->required(),
                Toggle::make('is_premium')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_premium'))
                    ->required(),
                TextInput::make('price_monthly')
                    ->label(\Modules\Core\Support\FilamentUi::field('price_monthly'))
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('price_yearly')
                    ->label(\Modules\Core\Support\FilamentUi::field('price_yearly'))
                    ->required()
                    ->numeric()
                    ->default(0),
                Textarea::make('settings_schema')
                    ->label(\Modules\Core\Support\FilamentUi::field('settings_schema'))
                    ->columnSpanFull(),
                Textarea::make('required_modules')
                    ->label(\Modules\Core\Support\FilamentUi::field('required_modules'))
                    ->columnSpanFull(),
                TextInput::make('sort_order')
                    ->label(\Modules\Core\Support\FilamentUi::field('sort_order'))
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
