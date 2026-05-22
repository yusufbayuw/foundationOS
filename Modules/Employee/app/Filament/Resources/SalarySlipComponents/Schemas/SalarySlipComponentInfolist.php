<?php

namespace Modules\Employee\Filament\Resources\SalarySlipComponents\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class SalarySlipComponentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('General Information'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('tenant.name')
                            ->label(FilamentUi::text('Tenant')),
                        TextEntry::make('salarySlip.id')
                            ->label(FilamentUi::text('Salary slip')),
                        TextEntry::make('payrollComponent.name')
                            ->label(FilamentUi::text('Payroll component'))
                            ->placeholder('-'),
                        TextEntry::make('component_type')
                            ->label(FilamentUi::field('component_type'))
                            ->placeholder('-'),
                        TextEntry::make('component_name')
                            ->label(FilamentUi::field('component_name')),
                        TextEntry::make('calculation_type')
                            ->label(FilamentUi::field('calculation_type'))
                            ->placeholder('-'),
                    ]),

                Section::make(FilamentUi::text('Calculation'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('amount')
                            ->label(FilamentUi::field('amount'))
                            ->numeric(),
                        TextEntry::make('percentage')
                            ->label(FilamentUi::field('percentage'))
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('base_amount')
                            ->label(FilamentUi::field('base_amount'))
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('formula_used')
                            ->label(FilamentUi::field('formula_used'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),

                Section::make(FilamentUi::text('Settings'))
                    ->columns(2)
                    ->schema([
                        IconEntry::make('is_taxable')
                            ->boolean(),
                        IconEntry::make('is_mandatory')
                            ->boolean(),
                        TextEntry::make('display_order')
                            ->label(FilamentUi::field('display_order'))
                            ->numeric(),
                        TextEntry::make('notes')
                            ->label(FilamentUi::field('notes'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),

                Section::make(FilamentUi::text('Timestamps'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('created_at')
                            ->label(FilamentUi::field('created_at'))
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('updated_at')
                            ->label(FilamentUi::field('updated_at'))
                            ->dateTime()
                            ->placeholder('-'),
                    ]),
            ]);
    }
}
