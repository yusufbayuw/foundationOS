<?php

namespace Modules\Employee\Filament\Resources\KpiIndicators\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class KpiIndicatorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant_id'))
                    ->relationship('tenant', 'name')
                    ->required(),
                Select::make('organization_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('organization_id'))
                    ->relationship('organization', 'name')
                    ->required(),
                TextInput::make('code')
                    ->label(\Modules\Core\Support\FilamentUi::field('code'))
                    ->required(),
                TextInput::make('name')
                    ->label(\Modules\Core\Support\FilamentUi::field('name'))
                    ->required(),
                Textarea::make('description')
                    ->label(\Modules\Core\Support\FilamentUi::field('description'))
                    ->columnSpanFull(),
                TextInput::make('category')
                    ->label(\Modules\Core\Support\FilamentUi::field('category')),
                TextInput::make('measurement_unit')
                    ->label(\Modules\Core\Support\FilamentUi::field('measurement_unit')),
                TextInput::make('target_type')
                    ->label(\Modules\Core\Support\FilamentUi::field('target_type')),
                TextInput::make('target_value')
                    ->label(\Modules\Core\Support\FilamentUi::field('target_value'))
                    ->numeric(),
                TextInput::make('target_minimum')
                    ->label(\Modules\Core\Support\FilamentUi::field('target_minimum'))
                    ->numeric(),
                TextInput::make('target_maximum')
                    ->label(\Modules\Core\Support\FilamentUi::field('target_maximum'))
                    ->numeric(),
                TextInput::make('weight_percentage')
                    ->label(\Modules\Core\Support\FilamentUi::field('weight_percentage'))
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('scoring_method')
                    ->label(\Modules\Core\Support\FilamentUi::field('scoring_method')),
                Textarea::make('formula')
                    ->label(\Modules\Core\Support\FilamentUi::field('formula'))
                    ->columnSpanFull(),
                TextInput::make('data_source')
                    ->label(\Modules\Core\Support\FilamentUi::field('data_source')),
                Toggle::make('is_active')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_active'))
                    ->required(),
            ]);
    }
}
