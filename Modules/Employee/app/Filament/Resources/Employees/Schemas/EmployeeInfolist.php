<?php

namespace Modules\Employee\Filament\Resources\Employees\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class EmployeeInfolist
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
                                TextEntry::make('tenant.name')
                                    ->label(\Modules\Core\Support\FilamentUi::text('Tenant')),
                                TextEntry::make('organization.name')
                                    ->label(\Modules\Core\Support\FilamentUi::text('Organization')),
                                TextEntry::make('user.name')
                                    ->label(\Modules\Core\Support\FilamentUi::text('User')),
                                TextEntry::make('department.name')
                                    ->label(\Modules\Core\Support\FilamentUi::text('Department'))
                                    ->placeholder('-'),
                                TextEntry::make('position.name')
                                    ->label(\Modules\Core\Support\FilamentUi::text('Position'))
                                    ->placeholder('-'),
                                TextEntry::make('employee_number')
                                    ->label(\Modules\Core\Support\FilamentUi::field('employee_number')),
                                TextEntry::make('full_name')
                                    ->label(\Modules\Core\Support\FilamentUi::field('full_name')),
                            ]),

                        Tab::make('Personal Data')
                            ->columns(2)
                            ->schema([
                                TextEntry::make('birth_place')
                                    ->label(\Modules\Core\Support\FilamentUi::field('birth_place'))
                                    ->placeholder('-'),
                                TextEntry::make('birth_date')
                                    ->label(\Modules\Core\Support\FilamentUi::field('birth_date'))
                                    ->date()
                                    ->placeholder('-'),
                                TextEntry::make('gender')
                                    ->label(\Modules\Core\Support\FilamentUi::field('gender'))
                                    ->placeholder('-'),
                                TextEntry::make('religion')
                                    ->label(\Modules\Core\Support\FilamentUi::field('religion'))
                                    ->placeholder('-'),
                                TextEntry::make('marital_status')
                                    ->label(\Modules\Core\Support\FilamentUi::field('marital_status'))
                                    ->placeholder('-'),
                                TextEntry::make('id_number')
                                    ->label(\Modules\Core\Support\FilamentUi::field('id_number'))
                                    ->placeholder('-'),
                                TextEntry::make('npwp')
                                    ->label(\Modules\Core\Support\FilamentUi::field('npwp'))
                                    ->placeholder('-'),
                                TextEntry::make('address')
                                    ->label(\Modules\Core\Support\FilamentUi::field('address'))
                                    ->placeholder('-')
                                    ->columnSpanFull(),
                                TextEntry::make('phone')
                                    ->label(\Modules\Core\Support\FilamentUi::field('phone'))
                                    ->placeholder('-'),
                                TextEntry::make('email')
                                    ->label(\Modules\Core\Support\FilamentUi::text('Email address'))
                                    ->placeholder('-'),
                                TextEntry::make('emergency_contact')
                                    ->label(\Modules\Core\Support\FilamentUi::field('emergency_contact'))
                                    ->placeholder('-')
                                    ->columnSpanFull(),
                                TextEntry::make('photo')
                                    ->label(\Modules\Core\Support\FilamentUi::field('photo'))
                                    ->placeholder('-'),
                            ]),

                        Tab::make('Employment')
                            ->columns(2)
                            ->schema([
                                TextEntry::make('employment_type')
                                    ->label(\Modules\Core\Support\FilamentUi::field('employment_type'))
                                    ->placeholder('-'),
                                TextEntry::make('employment_status')
                                    ->label(\Modules\Core\Support\FilamentUi::field('employment_status'))
                                    ->placeholder('-'),
                                TextEntry::make('join_date')
                                    ->label(\Modules\Core\Support\FilamentUi::field('join_date'))
                                    ->date(),
                                TextEntry::make('end_date')
                                    ->label(\Modules\Core\Support\FilamentUi::field('end_date'))
                                    ->date()
                                    ->placeholder('-'),
                                TextEntry::make('probation_end_date')
                                    ->label(\Modules\Core\Support\FilamentUi::field('probation_end_date'))
                                    ->date()
                                    ->placeholder('-'),
                                TextEntry::make('grade_level')
                                    ->label(\Modules\Core\Support\FilamentUi::field('grade_level'))
                                    ->placeholder('-'),
                                TextEntry::make('basic_salary')
                                    ->label(\Modules\Core\Support\FilamentUi::field('basic_salary'))
                                    ->numeric(),
                            ]),

                        Tab::make('Banking & Insurance')
                            ->columns(2)
                            ->schema([
                                TextEntry::make('bank_account')
                                    ->label(\Modules\Core\Support\FilamentUi::field('bank_account'))
                                    ->placeholder('-'),
                                TextEntry::make('bank_name')
                                    ->label(\Modules\Core\Support\FilamentUi::field('bank_name'))
                                    ->placeholder('-'),
                                TextEntry::make('account_holder')
                                    ->label(\Modules\Core\Support\FilamentUi::field('account_holder'))
                                    ->placeholder('-'),
                                TextEntry::make('bpjs_tk_number')
                                    ->label(\Modules\Core\Support\FilamentUi::field('bpjs_tk_number'))
                                    ->placeholder('-'),
                                TextEntry::make('bpjs_kes_number')
                                    ->label(\Modules\Core\Support\FilamentUi::field('bpjs_kes_number'))
                                    ->placeholder('-'),
                                TextEntry::make('insurance_number')
                                    ->label(\Modules\Core\Support\FilamentUi::field('insurance_number'))
                                    ->placeholder('-'),
                                TextEntry::make('created_at')
                                    ->label(\Modules\Core\Support\FilamentUi::field('created_at'))
                                    ->dateTime()
                                    ->placeholder('-'),
                                TextEntry::make('updated_at')
                                    ->label(\Modules\Core\Support\FilamentUi::field('updated_at'))
                                    ->dateTime()
                                    ->placeholder('-'),
                            ]),
                    ]),
            ]);
    }
}
