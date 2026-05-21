<?php

namespace Modules\Employee\Filament\Resources\SalarySlips\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;

class SalarySlipForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Payroll Info')
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        Select::make('employee_id')
                            ->label(\Modules\Core\Support\FilamentUi::field('employee_id'))
                            ->relationship('employee', 'id')
                            ->required(),
                        TextInput::make('period_month')
                            ->label(\Modules\Core\Support\FilamentUi::field('period_month'))
                            ->required(),
                        TextInput::make('period_year')
                            ->label(\Modules\Core\Support\FilamentUi::field('period_year'))
                            ->required(),
                        TextInput::make('period_label')
                            ->label(\Modules\Core\Support\FilamentUi::field('period_label'))
                            ->required(),
                        TextInput::make('status')
                            ->label(\Modules\Core\Support\FilamentUi::field('status'))
                            ->required()
                            ->default('draft'),
                    ]),

                Section::make('Salary')
                    ->columns(2)
                    ->schema([
                        TextInput::make('basic_salary')
                            ->label(\Modules\Core\Support\FilamentUi::field('basic_salary'))
                            ->required()
                            ->numeric(),
                        TextInput::make('total_earnings')
                            ->label(\Modules\Core\Support\FilamentUi::field('total_earnings'))
                            ->required()
                            ->numeric(),
                        TextInput::make('total_deductions')
                            ->label(\Modules\Core\Support\FilamentUi::field('total_deductions'))
                            ->required()
                            ->numeric(),
                        TextInput::make('net_salary')
                            ->label(\Modules\Core\Support\FilamentUi::field('net_salary'))
                            ->required()
                            ->numeric(),
                    ]),

                Section::make('Details')
                    ->columns(2)
                    ->schema([
                        Textarea::make('earnings_details')
                            ->label(\Modules\Core\Support\FilamentUi::field('earnings_details'))
                            ->required()
                            ->columnSpanFull(),
                        Textarea::make('deductions_details')
                            ->label(\Modules\Core\Support\FilamentUi::field('deductions_details'))
                            ->required()
                            ->columnSpanFull(),
                        Textarea::make('tax_details')
                            ->label(\Modules\Core\Support\FilamentUi::field('tax_details'))
                            ->columnSpanFull(),
                        Textarea::make('bpjs_details')
                            ->label(\Modules\Core\Support\FilamentUi::field('bpjs_details'))
                            ->columnSpanFull(),
                    ]),

                Section::make('Attendance')
                    ->columns(2)
                    ->schema([
                        TextInput::make('working_days')
                            ->label(\Modules\Core\Support\FilamentUi::field('working_days'))
                            ->required()
                            ->numeric()
                            ->default(0),
                        TextInput::make('working_hours')
                            ->label(\Modules\Core\Support\FilamentUi::field('working_hours'))
                            ->required()
                            ->numeric()
                            ->default(0),
                        TextInput::make('overtime_hours')
                            ->label(\Modules\Core\Support\FilamentUi::field('overtime_hours'))
                            ->required()
                            ->numeric()
                            ->default(0),
                        TextInput::make('leave_days')
                            ->label(\Modules\Core\Support\FilamentUi::field('leave_days'))
                            ->required()
                            ->numeric()
                            ->default(0),
                        TextInput::make('absent_days')
                            ->label(\Modules\Core\Support\FilamentUi::field('absent_days'))
                            ->required()
                            ->numeric()
                            ->default(0),
                    ]),

                Section::make('Payment')
                    ->columns(2)
                    ->schema([
                        DateTimePicker::make('paid_at'),
                        TextInput::make('paid_via')
                            ->label(\Modules\Core\Support\FilamentUi::field('paid_via')),
                        Toggle::make('is_sent')
                            ->label(\Modules\Core\Support\FilamentUi::field('is_sent'))
                            ->required(),
                        DateTimePicker::make('sent_at'),
                        Textarea::make('notes')
                            ->label(\Modules\Core\Support\FilamentUi::field('notes'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
