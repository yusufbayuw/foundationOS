<?php

namespace Modules\Employee\Filament\Resources\LeaveRequests\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;

class LeaveRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TenantField::make(),
                Select::make('employee_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('employee_id'))
                    ->relationship('employee', 'id')
                    ->required(),
                Select::make('substitute_employee_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('substitute_employee_id'))
                    ->relationship('substituteEmployee', 'id'),
                Select::make('supervisor_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('supervisor_id'))
                    ->relationship('supervisor', 'name'),
                Select::make('approver_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('approver_id'))
                    ->relationship('approver', 'name'),
                TextInput::make('leave_type')
                    ->label(\Modules\Core\Support\FilamentUi::field('leave_type')),
                DatePicker::make('start_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('start_date'))
                    ->required(),
                DatePicker::make('end_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('end_date'))
                    ->required(),
                TextInput::make('total_days')
                    ->label(\Modules\Core\Support\FilamentUi::field('total_days'))
                    ->required()
                    ->numeric(),
                Textarea::make('reason')
                    ->label(\Modules\Core\Support\FilamentUi::field('reason'))
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('attachment')
                    ->label(\Modules\Core\Support\FilamentUi::field('attachment')),
                TextInput::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status'))
                    ->required()
                    ->default('draft'),
                DateTimePicker::make('supervisor_approved_at'),
                DateTimePicker::make('approved_at'),
                Textarea::make('rejection_reason')
                    ->label(\Modules\Core\Support\FilamentUi::field('rejection_reason'))
                    ->columnSpanFull(),
            ]);
    }
}
