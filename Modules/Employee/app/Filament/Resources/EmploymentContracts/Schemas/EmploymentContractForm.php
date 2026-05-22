<?php

namespace Modules\Employee\Filament\Resources\EmploymentContracts\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class EmploymentContractForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('Contract Details'))
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        Select::make('employee_id')
                            ->label(FilamentUi::field('employee_id'))
                            ->relationship('employee', 'id')
                            ->required(),
                        Select::make('previous_contract_id')
                            ->label(FilamentUi::field('previous_contract_id'))
                            ->relationship('previousContract', 'id'),
                        TextInput::make('contract_number')
                            ->label(FilamentUi::field('contract_number'))
                            ->required(),
                        TextInput::make('contract_type')
                            ->label(FilamentUi::field('contract_type')),
                        TextInput::make('status')
                            ->label(FilamentUi::field('status'))
                            ->required()
                            ->default('active'),
                        TextInput::make('document_file')
                            ->label(FilamentUi::field('document_file')),
                    ]),

                Section::make(FilamentUi::text('Period'))
                    ->columns(2)
                    ->schema([
                        DatePicker::make('start_date')
                            ->label(FilamentUi::field('start_date'))
                            ->required(),
                        DatePicker::make('end_date')
                            ->label(FilamentUi::field('end_date')),
                        TextInput::make('probation_period_months')
                            ->label(FilamentUi::field('probation_period_months'))
                            ->required()
                            ->numeric()
                            ->default(3),
                    ]),

                Section::make(FilamentUi::text('Compensation'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('basic_salary')
                            ->label(FilamentUi::field('basic_salary'))
                            ->required()
                            ->numeric(),
                        Textarea::make('allowance_details')
                            ->label(FilamentUi::field('allowance_details'))
                            ->columnSpanFull(),
                        Textarea::make('benefits')
                            ->label(FilamentUi::field('benefits'))
                            ->columnSpanFull(),
                    ]),

                Section::make(FilamentUi::text('Work Conditions'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('work_location')
                            ->label(FilamentUi::field('work_location')),
                        TextInput::make('work_hours_per_week')
                            ->label(FilamentUi::field('work_hours_per_week'))
                            ->required()
                            ->numeric()
                            ->default(40),
                        Textarea::make('termination_clause')
                            ->label(FilamentUi::field('termination_clause'))
                            ->columnSpanFull(),
                    ]),

                Section::make(FilamentUi::text('Signatures'))
                    ->columns(2)
                    ->schema([
                        Toggle::make('signed_by_employee')
                            ->label(FilamentUi::field('signed_by_employee'))
                            ->required(),
                        Toggle::make('signed_by_employer')
                            ->label(FilamentUi::field('signed_by_employer'))
                            ->required(),
                    ]),
            ]);
    }
}
