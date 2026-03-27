<?php

namespace Modules\Finance\Filament\Resources\TuitionTypes\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;

class TuitionTypeForm
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
                    ->label(\Modules\Core\Support\FilamentUi::field('code')),
                TextInput::make('name')
                    ->label(\Modules\Core\Support\FilamentUi::field('name'))
                    ->required(),
                TextInput::make('education_level')
                    ->label(\Modules\Core\Support\FilamentUi::field('education_level')),
                TextInput::make('amount')
                    ->label(\Modules\Core\Support\FilamentUi::field('amount'))
                    ->numeric(),
                TextInput::make('frequency')
                    ->label(\Modules\Core\Support\FilamentUi::field('frequency')),
                TextInput::make('due_day')
                    ->label(\Modules\Core\Support\FilamentUi::field('due_day'))
                    ->required()
                    ->numeric()
                    ->default(10),
                TextInput::make('grace_period_days')
                    ->label(\Modules\Core\Support\FilamentUi::field('grace_period_days'))
                    ->required()
                    ->numeric()
                    ->default(7),
                TextInput::make('late_fee_percentage')
                    ->label(\Modules\Core\Support\FilamentUi::field('late_fee_percentage'))
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('late_fee_fixed')
                    ->label(\Modules\Core\Support\FilamentUi::field('late_fee_fixed'))
                    ->required()
                    ->numeric()
                    ->default(0),
                Toggle::make('discount_eligible')
                    ->label(\Modules\Core\Support\FilamentUi::field('discount_eligible'))
                    ->required(),
                Toggle::make('is_active')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_active'))
                    ->required(),
                Textarea::make('description')
                    ->label(\Modules\Core\Support\FilamentUi::field('description'))
                    ->columnSpanFull(),
            ]);
    }
}
