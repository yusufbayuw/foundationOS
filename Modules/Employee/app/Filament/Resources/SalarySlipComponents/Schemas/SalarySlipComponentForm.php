<?php

namespace Modules\Employee\Filament\Resources\SalarySlipComponents\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class SalarySlipComponentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('General Information')
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        Select::make('salary_slip_id')
                            ->label(FilamentUi::field('salary_slip_id'))
                            ->relationship('salarySlip', 'id')
                            ->required(),
                        Select::make('payroll_component_id')
                            ->label(FilamentUi::field('payroll_component_id'))
                            ->relationship('payrollComponent', 'name'),
                        TextInput::make('component_type')
                            ->label(FilamentUi::field('component_type')),
                        TextInput::make('component_name')
                            ->label(FilamentUi::field('component_name'))
                            ->required(),
                        TextInput::make('calculation_type')
                            ->label(FilamentUi::field('calculation_type')),
                    ]),

                Section::make('Calculation')
                    ->columns(2)
                    ->schema([
                        TextInput::make('amount')
                            ->label(FilamentUi::field('amount'))
                            ->required()
                            ->numeric(),
                        TextInput::make('percentage')
                            ->label(FilamentUi::field('percentage'))
                            ->numeric(),
                        TextInput::make('base_amount')
                            ->label(FilamentUi::field('base_amount'))
                            ->numeric(),
                        Textarea::make('formula_used')
                            ->label(FilamentUi::field('formula_used'))
                            ->columnSpanFull(),
                    ]),

                Section::make('Settings')
                    ->columns(2)
                    ->schema([
                        Toggle::make('is_taxable')
                            ->label(FilamentUi::field('is_taxable'))
                            ->required(),
                        Toggle::make('is_mandatory')
                            ->label(FilamentUi::field('is_mandatory'))
                            ->required(),
                        TextInput::make('display_order')
                            ->label(FilamentUi::field('display_order'))
                            ->required()
                            ->numeric()
                            ->default(0),
                        Textarea::make('notes')
                            ->label(FilamentUi::field('notes'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
