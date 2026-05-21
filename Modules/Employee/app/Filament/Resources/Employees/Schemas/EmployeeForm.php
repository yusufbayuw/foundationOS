<?php

namespace Modules\Employee\Filament\Resources\Employees\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;

class EmployeeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make()
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Basic Information')
                            ->columns(2)
                            ->schema([
                                TenantField::make(),
                                Select::make('organization_id')
                                    ->label(\Modules\Core\Support\FilamentUi::field('organization_id'))
                                    ->relationship('organization', 'name')
                                    ->required(),
                                Select::make('user_id')
                                    ->label(\Modules\Core\Support\FilamentUi::field('user_id'))
                                    ->relationship('user', 'name')
                                    ->required(),
                                Select::make('department_id')
                                    ->label(\Modules\Core\Support\FilamentUi::field('department_id'))
                                    ->relationship('department', 'name'),
                                Select::make('position_id')
                                    ->label(\Modules\Core\Support\FilamentUi::field('position_id'))
                                    ->relationship('position', 'name'),
                                TextInput::make('employee_number')
                                    ->label(\Modules\Core\Support\FilamentUi::field('employee_number'))
                                    ->required(),
                                TextInput::make('full_name')
                                    ->label(\Modules\Core\Support\FilamentUi::field('full_name'))
                                    ->required(),
                            ]),

                        Tab::make('Personal Data')
                            ->columns(2)
                            ->schema([
                                TextInput::make('birth_place')
                                    ->label(\Modules\Core\Support\FilamentUi::field('birth_place')),
                                DatePicker::make('birth_date')
                                    ->label(\Modules\Core\Support\FilamentUi::field('birth_date')),
                                TextInput::make('gender')
                                    ->label(\Modules\Core\Support\FilamentUi::field('gender')),
                                TextInput::make('religion')
                                    ->label(\Modules\Core\Support\FilamentUi::field('religion')),
                                TextInput::make('marital_status')
                                    ->label(\Modules\Core\Support\FilamentUi::field('marital_status')),
                                TextInput::make('id_number')
                                    ->label(\Modules\Core\Support\FilamentUi::field('id_number')),
                                TextInput::make('npwp')
                                    ->label(\Modules\Core\Support\FilamentUi::field('npwp')),
                                Textarea::make('address')
                                    ->label(\Modules\Core\Support\FilamentUi::field('address'))
                                    ->columnSpanFull(),
                                TextInput::make('phone')
                                    ->label(\Modules\Core\Support\FilamentUi::field('phone'))
                                    ->tel(),
                                TextInput::make('email')
                                    ->label(\Modules\Core\Support\FilamentUi::text('Email address'))
                                    ->email(),
                                Textarea::make('emergency_contact')
                                    ->label(\Modules\Core\Support\FilamentUi::field('emergency_contact'))
                                    ->columnSpanFull(),
                                TextInput::make('photo')
                                    ->label(\Modules\Core\Support\FilamentUi::field('photo')),
                            ]),

                        Tab::make('Employment')
                            ->columns(2)
                            ->schema([
                                TextInput::make('employment_type')
                                    ->label(\Modules\Core\Support\FilamentUi::field('employment_type')),
                                TextInput::make('employment_status')
                                    ->label(\Modules\Core\Support\FilamentUi::field('employment_status')),
                                DatePicker::make('join_date')
                                    ->label(\Modules\Core\Support\FilamentUi::field('join_date'))
                                    ->required(),
                                DatePicker::make('end_date')
                                    ->label(\Modules\Core\Support\FilamentUi::field('end_date')),
                                DatePicker::make('probation_end_date')
                                    ->label(\Modules\Core\Support\FilamentUi::field('probation_end_date')),
                                TextInput::make('grade_level')
                                    ->label(\Modules\Core\Support\FilamentUi::field('grade_level')),
                                TextInput::make('basic_salary')
                                    ->label(\Modules\Core\Support\FilamentUi::field('basic_salary'))
                                    ->required()
                                    ->numeric()
                                    ->default(0),
                            ]),

                        Tab::make('Banking & Insurance')
                            ->columns(2)
                            ->schema([
                                TextInput::make('bank_account')
                                    ->label(\Modules\Core\Support\FilamentUi::field('bank_account')),
                                TextInput::make('bank_name')
                                    ->label(\Modules\Core\Support\FilamentUi::field('bank_name')),
                                TextInput::make('account_holder')
                                    ->label(\Modules\Core\Support\FilamentUi::field('account_holder')),
                                TextInput::make('bpjs_tk_number')
                                    ->label(\Modules\Core\Support\FilamentUi::field('bpjs_tk_number')),
                                TextInput::make('bpjs_kes_number')
                                    ->label(\Modules\Core\Support\FilamentUi::field('bpjs_kes_number')),
                                TextInput::make('insurance_number')
                                    ->label(\Modules\Core\Support\FilamentUi::field('insurance_number')),
                            ]),
                    ]),
            ]);
    }
}
