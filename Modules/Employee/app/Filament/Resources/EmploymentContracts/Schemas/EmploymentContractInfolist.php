<?php

namespace Modules\Employee\Filament\Resources\EmploymentContracts\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class EmploymentContractInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('Contract Details'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('tenant.name')
                            ->label(FilamentUi::text('Tenant')),
                        TextEntry::make('employee.id')
                            ->label(FilamentUi::text('Employee')),
                        TextEntry::make('previousContract.id')
                            ->label(FilamentUi::text('Previous contract'))
                            ->placeholder('-'),
                        TextEntry::make('contract_number')
                            ->label(FilamentUi::field('contract_number')),
                        TextEntry::make('contract_type')
                            ->label(FilamentUi::field('contract_type'))
                            ->placeholder('-'),
                        TextEntry::make('status')
                            ->label(FilamentUi::field('status')),
                        TextEntry::make('document_file')
                            ->label(FilamentUi::field('document_file'))
                            ->placeholder('-'),
                    ]),

                Section::make(FilamentUi::text('Period'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('start_date')
                            ->label(FilamentUi::field('start_date'))
                            ->date(),
                        TextEntry::make('end_date')
                            ->label(FilamentUi::field('end_date'))
                            ->date()
                            ->placeholder('-'),
                        TextEntry::make('probation_period_months')
                            ->label(FilamentUi::field('probation_period_months'))
                            ->numeric(),
                    ]),

                Section::make(FilamentUi::text('Compensation'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('basic_salary')
                            ->label(FilamentUi::field('basic_salary'))
                            ->numeric(),
                        TextEntry::make('allowance_details')
                            ->label(FilamentUi::field('allowance_details'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('benefits')
                            ->label(FilamentUi::field('benefits'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),

                Section::make(FilamentUi::text('Work Conditions'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('work_location')
                            ->label(FilamentUi::field('work_location'))
                            ->placeholder('-'),
                        TextEntry::make('work_hours_per_week')
                            ->label(FilamentUi::field('work_hours_per_week'))
                            ->numeric(),
                        TextEntry::make('termination_clause')
                            ->label(FilamentUi::field('termination_clause'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),

                Section::make(FilamentUi::text('Signatures'))
                    ->columns(2)
                    ->schema([
                        IconEntry::make('signed_by_employee')
                            ->boolean(),
                        IconEntry::make('signed_by_employer')
                            ->boolean(),
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
