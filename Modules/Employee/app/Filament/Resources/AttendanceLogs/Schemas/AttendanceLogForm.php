<?php

namespace Modules\Employee\Filament\Resources\AttendanceLogs\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Models\User;
use Modules\Employee\Enums\AttendanceStatus;
use Modules\Employee\Models\Employee;
use Modules\Employee\Models\Shift;

class AttendanceLogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TenantField::make(),

                Section::make('Data Kehadiran')
                    ->columns(2)
                    ->schema([
                        Select::make('employee_id')
                            ->label('Karyawan')
                            ->options(fn () => Employee::query()
                                ->orderBy('full_name')
                                ->pluck('full_name', 'id'))
                            ->searchable()
                            ->required(),

                        Select::make('shift_id')
                            ->label('Shift')
                            ->options(fn () => Shift::where('is_active', true)
                                ->orderBy('name')
                                ->pluck('name', 'id'))
                            ->searchable()
                            ->nullable(),

                        DatePicker::make('date')
                            ->label('Tanggal')
                            ->required()
                            ->native(false),

                        Select::make('status')
                            ->label('Status Kehadiran')
                            ->options(AttendanceStatus::class)
                            ->required()
                            ->default(AttendanceStatus::Present->value),
                    ]),

                Section::make('Waktu Kerja')
                    ->columns(2)
                    ->schema([
                        DateTimePicker::make('check_in')
                            ->label('Check In')
                            ->native(false),

                        DateTimePicker::make('check_out')
                            ->label('Check Out')
                            ->native(false),

                        TextInput::make('work_hours')
                            ->label('Jam Kerja')
                            ->numeric()
                            ->suffix('jam')
                            ->hint('Dihitung ulang otomatis saat disimpan jika check_in & check_out terisi'),

                        TextInput::make('overtime_hours')
                            ->label('Jam Lembur')
                            ->numeric()
                            ->suffix('jam')
                            ->default(0),
                    ]),

                Section::make('Persetujuan & Catatan')
                    ->columns(2)
                    ->schema([
                        Select::make('approved_by')
                            ->label('Disetujui Oleh')
                            ->options(fn () => User::orderBy('name')->pluck('name', 'id'))
                            ->searchable()
                            ->nullable(),

                        Textarea::make('notes')
                            ->label('Catatan')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
