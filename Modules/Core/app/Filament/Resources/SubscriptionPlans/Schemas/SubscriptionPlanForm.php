<?php

namespace Modules\Core\Filament\Resources\SubscriptionPlans\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SubscriptionPlanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->label(\Modules\Core\Support\FilamentUi::field('code'))
                    ->required(),
                TextInput::make('name')
                    ->label(\Modules\Core\Support\FilamentUi::field('name'))
                    ->required(),
                Textarea::make('description')
                    ->label(\Modules\Core\Support\FilamentUi::field('description'))
                    ->columnSpanFull(),
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
                TextInput::make('max_users')
                    ->label(\Modules\Core\Support\FilamentUi::field('max_users'))
                    ->required()
                    ->numeric()
                    ->default(1),
                TextInput::make('max_organizations')
                    ->label(\Modules\Core\Support\FilamentUi::field('max_organizations'))
                    ->required()
                    ->numeric()
                    ->default(1),
                TextInput::make('max_storage_gb')
                    ->label(\Modules\Core\Support\FilamentUi::field('max_storage_gb'))
                    ->required()
                    ->numeric()
                    ->default(1),
                Textarea::make('included_modules')
                    ->label(\Modules\Core\Support\FilamentUi::field('included_modules'))
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('features')
                    ->label(\Modules\Core\Support\FilamentUi::field('features'))
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_active'))
                    ->required(),
                Toggle::make('is_recommended')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_recommended'))
                    ->required(),
                TextInput::make('display_order')
                    ->label(\Modules\Core\Support\FilamentUi::field('display_order'))
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
