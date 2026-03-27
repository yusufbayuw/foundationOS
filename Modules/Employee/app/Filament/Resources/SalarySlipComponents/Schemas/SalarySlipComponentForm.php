<?php

namespace Modules\Employee\Filament\Resources\SalarySlipComponents\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;

class SalarySlipComponentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant_id'))
                    ->relationship('tenant', 'name')
                    ->required(),
                Select::make('salary_slip_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('salary_slip_id'))
                    ->relationship('salarySlip', 'id')
                    ->required(),
                Select::make('payroll_component_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('payroll_component_id'))
                    ->relationship('payrollComponent', 'name'),
                TextInput::make('component_type')
                    ->label(\Modules\Core\Support\FilamentUi::field('component_type')),
                TextInput::make('component_name')
                    ->label(\Modules\Core\Support\FilamentUi::field('component_name'))
                    ->required(),
                TextInput::make('calculation_type')
                    ->label(\Modules\Core\Support\FilamentUi::field('calculation_type')),
                TextInput::make('amount')
                    ->label(\Modules\Core\Support\FilamentUi::field('amount'))
                    ->required()
                    ->numeric(),
                TextInput::make('percentage')
                    ->label(\Modules\Core\Support\FilamentUi::field('percentage'))
                    ->numeric(),
                TextInput::make('base_amount')
                    ->label(\Modules\Core\Support\FilamentUi::field('base_amount'))
                    ->numeric(),
                Textarea::make('formula_used')
                    ->label(\Modules\Core\Support\FilamentUi::field('formula_used'))
                    ->columnSpanFull(),
                Toggle::make('is_taxable')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_taxable'))
                    ->required(),
                Toggle::make('is_mandatory')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_mandatory'))
                    ->required(),
                TextInput::make('display_order')
                    ->label(\Modules\Core\Support\FilamentUi::field('display_order'))
                    ->required()
                    ->numeric()
                    ->default(0),
                Textarea::make('notes')
                    ->label(\Modules\Core\Support\FilamentUi::field('notes'))
                    ->columnSpanFull(),
            ]);
    }
}
