<?php

namespace Modules\Employee\Filament\Resources\SalarySlipComponents\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SalarySlipComponentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Tenant')),
                TextEntry::make('salarySlip.id')
                    ->label(\Modules\Core\Support\FilamentUi::text('Salary slip')),
                TextEntry::make('payrollComponent.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Payroll component'))
                    ->placeholder('-'),
                TextEntry::make('component_type')
                    ->label(\Modules\Core\Support\FilamentUi::field('component_type'))
                    ->placeholder('-'),
                TextEntry::make('component_name')
                    ->label(\Modules\Core\Support\FilamentUi::field('component_name')),
                TextEntry::make('calculation_type')
                    ->label(\Modules\Core\Support\FilamentUi::field('calculation_type'))
                    ->placeholder('-'),
                TextEntry::make('amount')
                    ->label(\Modules\Core\Support\FilamentUi::field('amount'))
                    ->numeric(),
                TextEntry::make('percentage')
                    ->label(\Modules\Core\Support\FilamentUi::field('percentage'))
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('base_amount')
                    ->label(\Modules\Core\Support\FilamentUi::field('base_amount'))
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('formula_used')
                    ->label(\Modules\Core\Support\FilamentUi::field('formula_used'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                IconEntry::make('is_taxable')
                    ->boolean(),
                IconEntry::make('is_mandatory')
                    ->boolean(),
                TextEntry::make('display_order')
                    ->label(\Modules\Core\Support\FilamentUi::field('display_order'))
                    ->numeric(),
                TextEntry::make('notes')
                    ->label(\Modules\Core\Support\FilamentUi::field('notes'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('created_at'))
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('updated_at'))
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
