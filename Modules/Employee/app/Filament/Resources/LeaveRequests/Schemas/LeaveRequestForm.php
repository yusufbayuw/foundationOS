<?php

namespace Modules\Employee\Filament\Resources\LeaveRequests\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Models\User;
use Modules\Employee\Enums\LeaveRequestStatus;
use Modules\Employee\Enums\LeaveType;
use Modules\Employee\Models\Employee;

class LeaveRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TenantField::make(),

                Section::make('Pengajuan Cuti')
                    ->columns(2)
                    ->schema([
                        Select::make('employee_id')
                            ->label('Karyawan')
                            ->options(fn () => Employee::query()
                                ->orderBy('full_name')
                                ->pluck('full_name', 'id'))
                            ->searchable()
                            ->required(),

                        Select::make('leave_type')
                            ->label('Jenis Cuti')
                            ->options(LeaveType::class)
                            ->required(),

                        DatePicker::make('start_date')
                            ->label('Tanggal Mulai')
                            ->required()
                            ->native(false)
                            ->reactive()
                            ->afterStateUpdated(fn ($state, $set, $get) => self::recalcDays($set, $get)),

                        DatePicker::make('end_date')
                            ->label('Tanggal Selesai')
                            ->required()
                            ->native(false)
                            ->reactive()
                            ->afterStateUpdated(fn ($state, $set, $get) => self::recalcDays($set, $get))
                            ->minDate(fn ($get) => $get('start_date')),

                        TextInput::make('total_days')
                            ->label('Jumlah Hari')
                            ->numeric()
                            ->suffix('hari')
                            ->required()
                            ->hint('Dihitung otomatis dari tanggal'),

                        Textarea::make('reason')
                            ->label('Alasan')
                            ->required()
                            ->columnSpanFull(),

                        TextInput::make('attachment')
                            ->label('File Pendukung (path/URL)')
                            ->columnSpanFull(),
                    ]),

                Section::make('Persetujuan')
                    ->columns(2)
                    ->schema([
                        Select::make('substitute_employee_id')
                            ->label('Karyawan Pengganti')
                            ->options(fn () => Employee::query()
                                ->orderBy('full_name')
                                ->pluck('full_name', 'id'))
                            ->searchable()
                            ->nullable(),

                        Select::make('supervisor_id')
                            ->label('Supervisor')
                            ->options(fn () => User::orderBy('name')->pluck('name', 'id'))
                            ->searchable()
                            ->nullable(),

                        Select::make('approver_id')
                            ->label('Approver Akhir')
                            ->options(fn () => User::orderBy('name')->pluck('name', 'id'))
                            ->searchable()
                            ->nullable(),

                        Select::make('status')
                            ->label('Status')
                            ->options(LeaveRequestStatus::class)
                            ->required()
                            ->default(LeaveRequestStatus::Draft->value),
                    ]),
            ]);
    }

    private static function recalcDays(\Closure $set, \Closure $get): void
    {
        $start = $get('start_date');
        $end = $get('end_date');

        if ($start && $end) {
            $days = (int) \Carbon\Carbon::parse($start)->diffInDays(\Carbon\Carbon::parse($end)) + 1;
            $set('total_days', max(1, $days));
        }
    }
}
