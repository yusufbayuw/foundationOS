<?php

namespace Modules\Employee\Filament\Resources\AttendanceLogs\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class AttendanceLogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('Employee & Shift'))
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        Select::make('employee_id')
                            ->label(FilamentUi::field('employee_id'))
                            ->relationship('employee', 'id')
                            ->required(),
                        Select::make('shift_id')
                            ->label(FilamentUi::field('shift_id'))
                            ->relationship('shift', 'name'),
                        TextInput::make('approved_by')
                            ->label(FilamentUi::field('approved_by'))
                            ->numeric(),
                    ]),

                Section::make(FilamentUi::text('Attendance Details'))
                    ->columns(2)
                    ->schema([
                        DatePicker::make('date')
                            ->label(FilamentUi::field('date'))
                            ->required(),
                        TextInput::make('status')
                            ->label(FilamentUi::field('status'))
                            ->required()
                            ->default('present'),
                        DateTimePicker::make('check_in'),
                        DateTimePicker::make('check_out'),
                        TextInput::make('work_hours')
                            ->label(FilamentUi::field('work_hours'))
                            ->numeric(),
                        TextInput::make('overtime_hours')
                            ->label(FilamentUi::field('overtime_hours'))
                            ->required()
                            ->numeric()
                            ->default(0),
                    ]),

                Section::make(FilamentUi::text('Location & Device'))
                    ->columns(2)
                    ->schema([
                        Textarea::make('location_check_in')
                            ->label(FilamentUi::field('location_check_in'))
                            ->columnSpanFull(),
                        Textarea::make('location_check_out')
                            ->label(FilamentUi::field('location_check_out'))
                            ->columnSpanFull(),
                        TextInput::make('device_check_in')
                            ->label(FilamentUi::field('device_check_in')),
                        TextInput::make('device_check_out')
                            ->label(FilamentUi::field('device_check_out')),
                        TextInput::make('photo_check_in')
                            ->label(FilamentUi::field('photo_check_in')),
                        TextInput::make('photo_check_out')
                            ->label(FilamentUi::field('photo_check_out')),
                    ]),

                Section::make(FilamentUi::text('Notes'))
                    ->columns(2)
                    ->schema([
                        Textarea::make('notes')
                            ->label(FilamentUi::field('notes'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
