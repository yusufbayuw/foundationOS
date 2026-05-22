<?php

namespace Modules\Employee\Filament\Resources\LeaveRequests\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class LeaveRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('General Information'))
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        Select::make('employee_id')
                            ->label(FilamentUi::field('employee_id'))
                            ->relationship('employee', 'id')
                            ->required(),
                        Select::make('substitute_employee_id')
                            ->label(FilamentUi::field('substitute_employee_id'))
                            ->relationship('substituteEmployee', 'id'),
                        Select::make('supervisor_id')
                            ->label(FilamentUi::field('supervisor_id'))
                            ->relationship('supervisor', 'name'),
                        Select::make('approver_id')
                            ->label(FilamentUi::field('approver_id'))
                            ->relationship('approver', 'name'),
                        TextInput::make('leave_type')
                            ->label(FilamentUi::field('leave_type')),
                    ]),

                Section::make(FilamentUi::text('Leave Period'))
                    ->columns(2)
                    ->schema([
                        DatePicker::make('start_date')
                            ->label(FilamentUi::field('start_date'))
                            ->required(),
                        DatePicker::make('end_date')
                            ->label(FilamentUi::field('end_date'))
                            ->required(),
                        TextInput::make('total_days')
                            ->label(FilamentUi::field('total_days'))
                            ->required()
                            ->numeric(),
                        TextInput::make('attachment')
                            ->label(FilamentUi::field('attachment')),
                        Textarea::make('reason')
                            ->label(FilamentUi::field('reason'))
                            ->required()
                            ->columnSpanFull(),
                    ]),

                Section::make(FilamentUi::text('Approval'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('status')
                            ->label(FilamentUi::field('status'))
                            ->required()
                            ->default('draft'),
                        DateTimePicker::make('supervisor_approved_at'),
                        DateTimePicker::make('approved_at'),
                        Textarea::make('rejection_reason')
                            ->label(FilamentUi::field('rejection_reason'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
