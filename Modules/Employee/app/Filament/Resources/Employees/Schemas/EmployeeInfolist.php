<?php

namespace Modules\Employee\Filament\Resources\Employees\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class EmployeeInfolist
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
                                TextEntry::make('tenant.name')
                                    ->label(FilamentUi::text('Tenant')),
                                TextEntry::make('organization.name')
                                    ->label(FilamentUi::text('Organization')),
                                TextEntry::make('user.name')
                                    ->label(FilamentUi::text('User')),
                                TextEntry::make('department.name')
                                    ->label(FilamentUi::text('Department'))
                                    ->placeholder('-'),
                                TextEntry::make('position.name')
                                    ->label(FilamentUi::text('Position'))
                                    ->placeholder('-'),
                                TextEntry::make('employee_number')
                                    ->label(FilamentUi::field('employee_number')),
                                TextEntry::make('full_name')
                                    ->label(FilamentUi::field('full_name')),
                            ]),

                        Tab::make(FilamentUi::text('Personal Data'))
                            ->columns(2)
                            ->schema([
                                TextEntry::make('birth_place')
                                    ->label(FilamentUi::field('birth_place'))
                                    ->placeholder('-'),
                                TextEntry::make('birth_date')
                                    ->label(FilamentUi::field('birth_date'))
                                    ->date()
                                    ->placeholder('-'),
                                TextEntry::make('gender')
                                    ->label(FilamentUi::field('gender'))
                                    ->placeholder('-'),
                                TextEntry::make('religion')
                                    ->label(FilamentUi::field('religion'))
                                    ->placeholder('-'),
                                TextEntry::make('marital_status')
                                    ->label(FilamentUi::field('marital_status'))
                                    ->placeholder('-'),
                                TextEntry::make('id_number')
                                    ->label(FilamentUi::field('id_number'))
                                    ->placeholder('-'),
                                TextEntry::make('npwp')
                                    ->label(FilamentUi::field('npwp'))
                                    ->placeholder('-'),
                                TextEntry::make('address')
                                    ->label(FilamentUi::field('address'))
                                    ->placeholder('-')
                                    ->columnSpanFull(),
                                TextEntry::make('phone')
                                    ->label(FilamentUi::field('phone'))
                                    ->placeholder('-'),
                                TextEntry::make('email')
                                    ->label(FilamentUi::text('Email address'))
                                    ->placeholder('-'),
                                TextEntry::make('emergency_contact')
                                    ->label(FilamentUi::field('emergency_contact'))
                                    ->placeholder('-')
                                    ->columnSpanFull(),
                                TextEntry::make('photo')
                                    ->label(FilamentUi::field('photo'))
                                    ->placeholder('-'),
                            ]),

                        Tab::make(FilamentUi::text('Employment'))
                            ->columns(2)
                            ->schema([
                                TextEntry::make('employment_type')
                                    ->label(FilamentUi::field('employment_type'))
                                    ->placeholder('-'),
                                TextEntry::make('employment_status')
                                    ->label(FilamentUi::field('employment_status'))
                                    ->placeholder('-'),
                                TextEntry::make('join_date')
                                    ->label(FilamentUi::field('join_date'))
                                    ->date(),
                                TextEntry::make('end_date')
                                    ->label(FilamentUi::field('end_date'))
                                    ->date()
                                    ->placeholder('-'),
                                TextEntry::make('probation_end_date')
                                    ->label(FilamentUi::field('probation_end_date'))
                                    ->date()
                                    ->placeholder('-'),
                                TextEntry::make('grade_level')
                                    ->label(FilamentUi::field('grade_level'))
                                    ->placeholder('-'),
                                TextEntry::make('basic_salary')
                                    ->label(FilamentUi::field('basic_salary'))
                                    ->numeric(),
                            ]),

                        Tab::make(FilamentUi::text('Banking & Insurance'))
                            ->columns(2)
                            ->schema([
                                TextEntry::make('bank_account')
                                    ->label(FilamentUi::field('bank_account'))
                                    ->placeholder('-'),
                                TextEntry::make('bank_name')
                                    ->label(FilamentUi::field('bank_name'))
                                    ->placeholder('-'),
                                TextEntry::make('account_holder')
                                    ->label(FilamentUi::field('account_holder'))
                                    ->placeholder('-'),
                                TextEntry::make('bpjs_tk_number')
                                    ->label(FilamentUi::field('bpjs_tk_number'))
                                    ->placeholder('-'),
                                TextEntry::make('bpjs_kes_number')
                                    ->label(FilamentUi::field('bpjs_kes_number'))
                                    ->placeholder('-'),
                                TextEntry::make('insurance_number')
                                    ->label(FilamentUi::field('insurance_number'))
                                    ->placeholder('-'),
                                TextEntry::make('created_at')
                                    ->label(FilamentUi::field('created_at'))
                                    ->dateTime()
                                    ->placeholder('-'),
                                TextEntry::make('updated_at')
                                    ->label(FilamentUi::field('updated_at'))
                                    ->dateTime()
                                    ->placeholder('-'),
                            ]),
                    ]),
            ]);
    }
}
