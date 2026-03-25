<?php

namespace Modules\Employee\Filament\Resources\PayrollComponents\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PayrollComponentForm
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
                TextInput::make('type')
                    ->label(\Modules\Core\Support\FilamentUi::field('type')),
                TextInput::make('category')
                    ->label(\Modules\Core\Support\FilamentUi::field('category')),
                TextInput::make('calculation_type')
                    ->label(\Modules\Core\Support\FilamentUi::field('calculation_type')),
                TextInput::make('amount')
                    ->label(\Modules\Core\Support\FilamentUi::field('amount'))
                    ->numeric(),
                TextInput::make('percentage')
                    ->label(\Modules\Core\Support\FilamentUi::field('percentage'))
                    ->numeric(),
                Textarea::make('formula')
                    ->label(\Modules\Core\Support\FilamentUi::field('formula'))
                    ->columnSpanFull(),
                Toggle::make('is_taxable')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_taxable'))
                    ->required(),
                Toggle::make('is_mandatory')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_mandatory'))
                    ->required(),
                Toggle::make('is_active')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_active'))
                    ->required(),
                TextInput::make('display_order')
                    ->label(\Modules\Core\Support\FilamentUi::field('display_order'))
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
