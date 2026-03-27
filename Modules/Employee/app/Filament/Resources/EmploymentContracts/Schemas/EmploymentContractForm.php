<?php

namespace Modules\Employee\Filament\Resources\EmploymentContracts\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;

class EmploymentContractForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant_id'))
                    ->relationship('tenant', 'name')
                    ->required(),
                Select::make('employee_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('employee_id'))
                    ->relationship('employee', 'id')
                    ->required(),
                Select::make('previous_contract_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('previous_contract_id'))
                    ->relationship('previousContract', 'id'),
                TextInput::make('contract_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('contract_number'))
                    ->required(),
                TextInput::make('contract_type')
                    ->label(\Modules\Core\Support\FilamentUi::field('contract_type')),
                DatePicker::make('start_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('start_date'))
                    ->required(),
                DatePicker::make('end_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('end_date')),
                TextInput::make('probation_period_months')
                    ->label(\Modules\Core\Support\FilamentUi::field('probation_period_months'))
                    ->required()
                    ->numeric()
                    ->default(3),
                TextInput::make('basic_salary')
                    ->label(\Modules\Core\Support\FilamentUi::field('basic_salary'))
                    ->required()
                    ->numeric(),
                Textarea::make('allowance_details')
                    ->label(\Modules\Core\Support\FilamentUi::field('allowance_details'))
                    ->columnSpanFull(),
                Textarea::make('benefits')
                    ->label(\Modules\Core\Support\FilamentUi::field('benefits'))
                    ->columnSpanFull(),
                TextInput::make('work_location')
                    ->label(\Modules\Core\Support\FilamentUi::field('work_location')),
                TextInput::make('work_hours_per_week')
                    ->label(\Modules\Core\Support\FilamentUi::field('work_hours_per_week'))
                    ->required()
                    ->numeric()
                    ->default(40),
                Textarea::make('termination_clause')
                    ->label(\Modules\Core\Support\FilamentUi::field('termination_clause'))
                    ->columnSpanFull(),
                TextInput::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status'))
                    ->required()
                    ->default('active'),
                Toggle::make('signed_by_employee')
                    ->label(\Modules\Core\Support\FilamentUi::field('signed_by_employee'))
                    ->required(),
                Toggle::make('signed_by_employer')
                    ->label(\Modules\Core\Support\FilamentUi::field('signed_by_employer'))
                    ->required(),
                TextInput::make('document_file')
                    ->label(\Modules\Core\Support\FilamentUi::field('document_file'))
            ]);
    }
}
