<?php

namespace Modules\Employee\Filament\Resources\AttendanceLogs\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;

class AttendanceLogForm
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
                Select::make('shift_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('shift_id'))
                    ->relationship('shift', 'name'),
                TextInput::make('approved_by')
                    ->label(\Modules\Core\Support\FilamentUi::field('approved_by'))
                    ->numeric(),
                DatePicker::make('date')
                    ->label(\Modules\Core\Support\FilamentUi::field('date'))
                    ->required(),
                DateTimePicker::make('check_in'),
                DateTimePicker::make('check_out'),
                TextInput::make('work_hours')
                    ->label(\Modules\Core\Support\FilamentUi::field('work_hours'))
                    ->numeric(),
                TextInput::make('overtime_hours')
                    ->label(\Modules\Core\Support\FilamentUi::field('overtime_hours'))
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status'))
                    ->required()
                    ->default('present'),
                Textarea::make('location_check_in')
                    ->label(\Modules\Core\Support\FilamentUi::field('location_check_in'))
                    ->columnSpanFull(),
                Textarea::make('location_check_out')
                    ->label(\Modules\Core\Support\FilamentUi::field('location_check_out'))
                    ->columnSpanFull(),
                TextInput::make('device_check_in')
                    ->label(\Modules\Core\Support\FilamentUi::field('device_check_in')),
                TextInput::make('device_check_out')
                    ->label(\Modules\Core\Support\FilamentUi::field('device_check_out')),
                TextInput::make('photo_check_in')
                    ->label(\Modules\Core\Support\FilamentUi::field('photo_check_in')),
                TextInput::make('photo_check_out')
                    ->label(\Modules\Core\Support\FilamentUi::field('photo_check_out')),
                Textarea::make('notes')
                    ->label(\Modules\Core\Support\FilamentUi::field('notes'))
                    ->columnSpanFull(),
            ]);
    }
}
