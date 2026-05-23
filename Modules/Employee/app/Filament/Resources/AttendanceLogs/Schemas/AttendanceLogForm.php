<?php

namespace Modules\Employee\Filament\Resources\AttendanceLogs\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
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
                            ->relationship('employee', 'full_name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('shift_id')
                            ->label(FilamentUi::field('shift_id'))
                            ->relationship('shift', 'name'),
                        Select::make('approved_by')
                            ->label(FilamentUi::field('approved_by'))
                            ->relationship('approvedBy', 'name')
                            ->searchable()
                            ->preload(),
                    ]),

                Section::make(FilamentUi::text('Attendance Details'))
                    ->columns(2)
                    ->schema([
                        DatePicker::make('date')
                            ->label(FilamentUi::field('date'))
                            ->required(),
                        Select::make('status')
                            ->label(FilamentUi::field('status'))
                            ->options([
                                'present' => FilamentUi::text('Present'),
                                'absent' => FilamentUi::text('Absent'),
                                'late' => FilamentUi::text('Late'),
                                'leave' => FilamentUi::text('Leave'),
                                'holiday' => FilamentUi::text('Holiday'),
                            ])
                            ->required()
                            ->default('present'),
                        DateTimePicker::make('check_in')
                            ->label(FilamentUi::field('check_in')),
                        DateTimePicker::make('check_out')
                            ->label(FilamentUi::field('check_out')),
                        TextInput::make('work_hours')
                            ->label(FilamentUi::field('work_hours'))
                            ->numeric()
                            ->step(0.5)
                            ->suffix(FilamentUi::text('hours')),
                        TextInput::make('overtime_hours')
                            ->label(FilamentUi::field('overtime_hours'))
                            ->numeric()
                            ->step(0.5)
                            ->default(0)
                            ->suffix(FilamentUi::text('hours')),
                    ]),

                Section::make(FilamentUi::text('Check-In Location & Photo'))
                    ->columns(2)
                    ->description(FilamentUi::text('Capture GPS coordinates via the browser geolocation API.'))
                    ->schema([
                        Grid::make(3)
                            ->columnSpanFull()
                            ->extraAttributes([
                                'x-data' => '{
                                    getLocation(latField, lngField, accField) {
                                        if (!navigator.geolocation) { alert("Geolocation not supported."); return; }
                                        navigator.geolocation.getCurrentPosition(
                                            pos => {
                                                $wire.set(latField, pos.coords.latitude.toFixed(7));
                                                $wire.set(lngField, pos.coords.longitude.toFixed(7));
                                                $wire.set(accField, Math.round(pos.coords.accuracy));
                                            },
                                            err => alert("Location error: " + err.message),
                                            { enableHighAccuracy: true, timeout: 10000 }
                                        );
                                    }
                                }',
                            ])
                            ->schema([
                                TextInput::make('location_check_in.lat')
                                    ->label(FilamentUi::text('Check-In Latitude'))
                                    ->numeric()
                                    ->placeholder('-6.2000000'),
                                TextInput::make('location_check_in.lng')
                                    ->label(FilamentUi::text('Check-In Longitude'))
                                    ->numeric()
                                    ->placeholder('106.8000000'),
                                TextInput::make('location_check_in.accuracy')
                                    ->label(FilamentUi::text('Accuracy (m)'))
                                    ->numeric()
                                    ->readOnly()
                                    ->suffix('m')
                                    ->extraInputAttributes([
                                        'x-on:click.prevent' => "getLocation(
                                            'data.location_check_in.lat',
                                            'data.location_check_in.lng',
                                            'data.location_check_in.accuracy'
                                        )",
                                        'placeholder' => FilamentUi::text('Click to auto-detect'),
                                        'style' => 'cursor:pointer;background:#f0fdf4;',
                                    ]),
                            ]),

                        FileUpload::make('photo_check_in')
                            ->label(FilamentUi::field('photo_check_in'))
                            ->image()
                            ->imageEditor()
                            ->directory('attendance/check-in')
                            ->visibility('private')
                            ->maxSize(2048)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp']),

                        TextInput::make('device_check_in')
                            ->label(FilamentUi::field('device_check_in'))
                            ->placeholder('web / mobile / fingerprint'),
                    ]),

                Section::make(FilamentUi::text('Check-Out Location & Photo'))
                    ->columns(2)
                    ->schema([
                        Grid::make(3)
                            ->columnSpanFull()
                            ->schema([
                                TextInput::make('location_check_out.lat')
                                    ->label(FilamentUi::text('Check-Out Latitude'))
                                    ->numeric()
                                    ->placeholder('-6.2000000'),
                                TextInput::make('location_check_out.lng')
                                    ->label(FilamentUi::text('Check-Out Longitude'))
                                    ->numeric()
                                    ->placeholder('106.8000000'),
                                TextInput::make('location_check_out.accuracy')
                                    ->label(FilamentUi::text('Accuracy (m)'))
                                    ->numeric()
                                    ->readOnly()
                                    ->suffix('m'),
                            ]),

                        FileUpload::make('photo_check_out')
                            ->label(FilamentUi::field('photo_check_out'))
                            ->image()
                            ->imageEditor()
                            ->directory('attendance/check-out')
                            ->visibility('private')
                            ->maxSize(2048)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp']),

                        TextInput::make('device_check_out')
                            ->label(FilamentUi::field('device_check_out'))
                            ->placeholder('web / mobile / fingerprint'),
                    ]),

                Section::make(FilamentUi::text('Notes'))
                    ->schema([
                        Textarea::make('notes')
                            ->label(FilamentUi::field('notes'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
