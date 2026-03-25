<?php

namespace Modules\Employee\Filament\Resources\EmploymentContracts\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class EmploymentContractInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Tenant')),
                TextEntry::make('employee.id')
                    ->label(\Modules\Core\Support\FilamentUi::text('Employee')),
                TextEntry::make('previousContract.id')
                    ->label(\Modules\Core\Support\FilamentUi::text('Previous contract'))
                    ->placeholder('-'),
                TextEntry::make('contract_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('contract_number')),
                TextEntry::make('contract_type')
                    ->label(\Modules\Core\Support\FilamentUi::field('contract_type'))
                    ->placeholder('-'),
                TextEntry::make('start_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('start_date'))
                    ->date(),
                TextEntry::make('end_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('end_date'))
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('probation_period_months')
                    ->label(\Modules\Core\Support\FilamentUi::field('probation_period_months'))
                    ->numeric(),
                TextEntry::make('basic_salary')
                    ->label(\Modules\Core\Support\FilamentUi::field('basic_salary'))
                    ->numeric(),
                TextEntry::make('allowance_details')
                    ->label(\Modules\Core\Support\FilamentUi::field('allowance_details'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('benefits')
                    ->label(\Modules\Core\Support\FilamentUi::field('benefits'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('work_location')
                    ->label(\Modules\Core\Support\FilamentUi::field('work_location'))
                    ->placeholder('-'),
                TextEntry::make('work_hours_per_week')
                    ->label(\Modules\Core\Support\FilamentUi::field('work_hours_per_week'))
                    ->numeric(),
                TextEntry::make('termination_clause')
                    ->label(\Modules\Core\Support\FilamentUi::field('termination_clause'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status')),
                IconEntry::make('signed_by_employee')
                    ->boolean(),
                IconEntry::make('signed_by_employer')
                    ->boolean(),
                TextEntry::make('document_file')
                    ->label(\Modules\Core\Support\FilamentUi::field('document_file'))
                    ->placeholder('-'),
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
