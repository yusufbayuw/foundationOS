<?php

namespace Modules\Employee\Filament\Resources\SalarySlips\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SalarySlipForm
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
                TextInput::make('period_month')
                    ->label(\Modules\Core\Support\FilamentUi::field('period_month'))
                    ->required(),
                TextInput::make('period_year')
                    ->label(\Modules\Core\Support\FilamentUi::field('period_year'))
                    ->required(),
                TextInput::make('period_label')
                    ->label(\Modules\Core\Support\FilamentUi::field('period_label'))
                    ->required(),
                TextInput::make('basic_salary')
                    ->label(\Modules\Core\Support\FilamentUi::field('basic_salary'))
                    ->required()
                    ->numeric(),
                Textarea::make('earnings_details')
                    ->label(\Modules\Core\Support\FilamentUi::field('earnings_details'))
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('deductions_details')
                    ->label(\Modules\Core\Support\FilamentUi::field('deductions_details'))
                    ->required()
                    ->columnSpanFull(),
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
                Textarea::make('tax_details')
                    ->label(\Modules\Core\Support\FilamentUi::field('tax_details'))
                    ->columnSpanFull(),
                Textarea::make('bpjs_details')
                    ->label(\Modules\Core\Support\FilamentUi::field('bpjs_details'))
                    ->columnSpanFull(),
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
                TextInput::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status'))
                    ->required()
                    ->default('draft'),
                DateTimePicker::make('paid_at'),
                TextInput::make('paid_via')
                    ->label(\Modules\Core\Support\FilamentUi::field('paid_via')),
                Textarea::make('notes')
                    ->label(\Modules\Core\Support\FilamentUi::field('notes'))
                    ->columnSpanFull(),
                Toggle::make('is_sent')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_sent'))
                    ->required(),
                DateTimePicker::make('sent_at'),
            ]);
    }
}
