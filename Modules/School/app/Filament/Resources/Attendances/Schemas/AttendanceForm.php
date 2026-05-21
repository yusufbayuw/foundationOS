<?php

namespace Modules\School\Filament\Resources\Attendances\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class AttendanceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Attendance Information')
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        Select::make('schedule_id')
                            ->label(FilamentUi::field('schedule_id'))
                            ->relationship('schedule', 'id'),
                        Select::make('student_id')
                            ->label(FilamentUi::field('student_id'))
                            ->relationship('student', 'id')
                            ->required(),
                        TextInput::make('verified_by')
                            ->label(FilamentUi::field('verified_by'))
                            ->numeric(),
                        TextInput::make('entity_type')
                            ->label(FilamentUi::field('entity_type'))
                            ->required()
                            ->default('student'),
                        TextInput::make('status')
                            ->label(FilamentUi::field('status'))
                            ->required(),
                        TextInput::make('entry_method')
                            ->label(FilamentUi::field('entry_method')),
                    ]),

                Section::make('Date & Time')
                    ->columns(2)
                    ->schema([
                        DatePicker::make('attendance_date')
                            ->label(FilamentUi::field('attendance_date'))
                            ->required(),
                        DateTimePicker::make('check_in'),
                        DateTimePicker::make('check_out'),
                    ]),

                Section::make('Verification Details')
                    ->columns(2)
                    ->schema([
                        Textarea::make('location_data')
                            ->label(FilamentUi::field('location_data'))
                            ->columnSpanFull(),
                        Textarea::make('device_info')
                            ->label(FilamentUi::field('device_info'))
                            ->columnSpanFull(),
                        TextInput::make('photo_proof')
                            ->label(FilamentUi::field('photo_proof')),
                        Textarea::make('notes')
                            ->label(FilamentUi::field('notes'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
