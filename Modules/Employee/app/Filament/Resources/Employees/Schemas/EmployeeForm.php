<?php

namespace Modules\Employee\Filament\Resources\Employees\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class EmployeeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make()
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make(FilamentUi::text('Basic Information'))
                            ->columns(2)
                            ->schema([
                                TenantField::make(),
                                Select::make('organization_id')
                                    ->label(FilamentUi::field('organization_id'))
                                    ->relationship('organization', 'name')
                                    ->required(),
                                Select::make('user_id')
                                    ->label(FilamentUi::field('user_id'))
                                    ->relationship('user', 'name')
                                    ->required(),
                                Select::make('department_id')
                                    ->label(FilamentUi::field('department_id'))
                                    ->relationship('department', 'name'),
                                Select::make('position_id')
                                    ->label(FilamentUi::field('position_id'))
                                    ->relationship('position', 'name'),
                                TextInput::make('employee_number')
                                    ->label(FilamentUi::field('employee_number'))
                                    ->required(),
                                TextInput::make('full_name')
                                    ->label(FilamentUi::field('full_name'))
                                    ->required(),
                            ]),

                        Tab::make(FilamentUi::text('Personal Data'))
                            ->columns(2)
                            ->schema([
                                TextInput::make('birth_place')
                                    ->label(FilamentUi::field('birth_place')),
                                DatePicker::make('birth_date')
                                    ->label(FilamentUi::field('birth_date')),
                                TextInput::make('gender')
                                    ->label(FilamentUi::field('gender')),
                                TextInput::make('religion')
                                    ->label(FilamentUi::field('religion')),
                                TextInput::make('marital_status')
                                    ->label(FilamentUi::field('marital_status')),
                                TextInput::make('id_number')
                                    ->label(FilamentUi::field('id_number')),
                                TextInput::make('npwp')
                                    ->label(FilamentUi::field('npwp')),
                                Textarea::make('address')
                                    ->label(FilamentUi::field('address'))
                                    ->columnSpanFull(),
                                TextInput::make('phone')
                                    ->label(FilamentUi::field('phone'))
                                    ->tel(),
                                TextInput::make('email')
                                    ->label(FilamentUi::text('Email address'))
                                    ->email(),
                                Textarea::make('emergency_contact')
                                    ->label(FilamentUi::field('emergency_contact'))
                                    ->columnSpanFull(),
                                TextInput::make('photo')
                                    ->label(FilamentUi::field('photo')),
                            ]),

                        Tab::make(FilamentUi::text('Employment'))
                            ->columns(2)
                            ->schema([
                                TextInput::make('employment_type')
                                    ->label(FilamentUi::field('employment_type')),
                                TextInput::make('employment_status')
                                    ->label(FilamentUi::field('employment_status')),
                                DatePicker::make('join_date')
                                    ->label(FilamentUi::field('join_date'))
                                    ->required(),
                                DatePicker::make('end_date')
                                    ->label(FilamentUi::field('end_date')),
                                DatePicker::make('probation_end_date')
                                    ->label(FilamentUi::field('probation_end_date')),
                                TextInput::make('grade_level')
                                    ->label(FilamentUi::field('grade_level')),
                                TextInput::make('basic_salary')
                                    ->label(FilamentUi::field('basic_salary'))
                                    ->required()
                                    ->numeric()
                                    ->default(0),
                            ]),

                        Tab::make(FilamentUi::text('Banking & Insurance'))
                            ->columns(2)
                            ->schema([
                                TextInput::make('bank_account')
                                    ->label(FilamentUi::field('bank_account')),
                                TextInput::make('bank_name')
                                    ->label(FilamentUi::field('bank_name')),
                                TextInput::make('account_holder')
                                    ->label(FilamentUi::field('account_holder')),
                                TextInput::make('bpjs_tk_number')
                                    ->label(FilamentUi::field('bpjs_tk_number')),
                                TextInput::make('bpjs_kes_number')
                                    ->label(FilamentUi::field('bpjs_kes_number')),
                                TextInput::make('insurance_number')
                                    ->label(FilamentUi::field('insurance_number')),
                            ]),
                    ]),
            ]);
    }
}
