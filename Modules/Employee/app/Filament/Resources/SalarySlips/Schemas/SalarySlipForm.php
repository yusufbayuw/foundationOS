<?php

namespace Modules\Employee\Filament\Resources\SalarySlips\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class SalarySlipForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('Payroll Info'))
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        Select::make('employee_id')
                            ->label(FilamentUi::field('employee_id'))
                            ->relationship('employee', 'id')
                            ->required(),
                        TextInput::make('period_month')
                            ->label(FilamentUi::field('period_month'))
                            ->required(),
                        TextInput::make('period_year')
                            ->label(FilamentUi::field('period_year'))
                            ->required(),
                        TextInput::make('period_label')
                            ->label(FilamentUi::field('period_label'))
                            ->required(),
                        TextInput::make('status')
                            ->label(FilamentUi::field('status'))
                            ->required()
                            ->default('draft'),
                    ]),

                Section::make(FilamentUi::text('Salary'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('basic_salary')
                            ->label(FilamentUi::field('basic_salary'))
                            ->required()
                            ->numeric(),
                        TextInput::make('total_earnings')
                            ->label(FilamentUi::field('total_earnings'))
                            ->required()
                            ->numeric(),
                        TextInput::make('total_deductions')
                            ->label(FilamentUi::field('total_deductions'))
                            ->required()
                            ->numeric(),
                        TextInput::make('net_salary')
                            ->label(FilamentUi::field('net_salary'))
                            ->required()
                            ->numeric(),
                    ]),

                Section::make(FilamentUi::text('Details'))
                    ->columns(2)
                    ->schema([
                        Repeater::make('earnings_details')
                            ->label(FilamentUi::field('earnings_details'))
                            ->required()
                            ->schema([
                                TextInput::make('name')
                                    ->label(FilamentUi::text('Component')),
                                TextInput::make('amount')
                                    ->label(FilamentUi::text('Amount'))
                                    ->numeric(),
                            ])
                            ->columnSpanFull()
                            ->addActionLabel('Add Item')
                            ->reorderable(),
                        Repeater::make('deductions_details')
                            ->label(FilamentUi::field('deductions_details'))
                            ->required()
                            ->schema([
                                TextInput::make('name')
                                    ->label(FilamentUi::text('Component')),
                                TextInput::make('amount')
                                    ->label(FilamentUi::text('Amount'))
                                    ->numeric(),
                            ])
                            ->columnSpanFull()
                            ->addActionLabel('Add Item')
                            ->reorderable(),
                        Repeater::make('tax_details')
                            ->label(FilamentUi::field('tax_details'))
                            ->schema([
                                TextInput::make('bracket')
                                    ->label(FilamentUi::text('Bracket')),
                                TextInput::make('amount')
                                    ->label(FilamentUi::text('Amount'))
                                    ->numeric(),
                            ])
                            ->columnSpanFull()
                            ->addActionLabel('Add Item')
                            ->reorderable(),
                        Repeater::make('bpjs_details')
                            ->label(FilamentUi::field('bpjs_details'))
                            ->schema([
                                TextInput::make('type')
                                    ->label(FilamentUi::text('Type')),
                                TextInput::make('amount')
                                    ->label(FilamentUi::text('Amount'))
                                    ->numeric(),
                            ])
                            ->columnSpanFull()
                            ->addActionLabel('Add Item')
                            ->reorderable(),
                    ]),

                Section::make(FilamentUi::text('Attendance'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('working_days')
                            ->label(FilamentUi::field('working_days'))
                            ->required()
                            ->numeric()
                            ->default(0),
                        TextInput::make('working_hours')
                            ->label(FilamentUi::field('working_hours'))
                            ->required()
                            ->numeric()
                            ->default(0),
                        TextInput::make('overtime_hours')
                            ->label(FilamentUi::field('overtime_hours'))
                            ->required()
                            ->numeric()
                            ->default(0),
                        TextInput::make('leave_days')
                            ->label(FilamentUi::field('leave_days'))
                            ->required()
                            ->numeric()
                            ->default(0),
                        TextInput::make('absent_days')
                            ->label(FilamentUi::field('absent_days'))
                            ->required()
                            ->numeric()
                            ->default(0),
                    ]),

                Section::make(FilamentUi::text('Payment'))
                    ->columns(2)
                    ->schema([
                        DateTimePicker::make('paid_at'),
                        TextInput::make('paid_via')
                            ->label(FilamentUi::field('paid_via')),
                        Toggle::make('is_sent')
                            ->label(FilamentUi::field('is_sent'))
                            ->required(),
                        DateTimePicker::make('sent_at'),
                        Textarea::make('notes')
                            ->label(FilamentUi::field('notes'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
