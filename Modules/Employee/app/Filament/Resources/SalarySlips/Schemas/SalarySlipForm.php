<?php

namespace Modules\Employee\Filament\Resources\SalarySlips\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Employee\Enums\SalarySlipStatus;
use Modules\Employee\Models\Employee;

class SalarySlipForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TenantField::make(),

                Section::make('Identitas Slip Gaji')
                    ->columns(3)
                    ->schema([
                        Select::make('employee_id')
                            ->label('Karyawan')
                            ->options(fn () => Employee::query()
                                ->orderBy('full_name')
                                ->pluck('full_name', 'id'))
                            ->searchable()
                            ->required(),

                        Select::make('period_month')
                            ->label('Bulan')
                            ->options([
                                '1' => 'Januari', '2' => 'Februari', '3' => 'Maret',
                                '4' => 'April', '5' => 'Mei', '6' => 'Juni',
                                '7' => 'Juli', '8' => 'Agustus', '9' => 'September',
                                '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
                            ])
                            ->required(),

                        TextInput::make('period_year')
                            ->label('Tahun')
                            ->numeric()
                            ->minValue(2000)
                            ->maxValue(2099)
                            ->default(now()->year)
                            ->required(),

                        TextInput::make('period_label')
                            ->label('Label Periode')
                            ->placeholder('cth: Januari 2025')
                            ->columnSpanFull(),

                        Select::make('status')
                            ->label('Status')
                            ->options(SalarySlipStatus::class)
                            ->required()
                            ->default(SalarySlipStatus::Draft->value),
                    ]),

                Section::make('Komponen Gaji')
                    ->columns(3)
                    ->description('Diisi otomatis saat menggunakan fitur Generate Payroll')
                    ->schema([
                        TextInput::make('basic_salary')
                            ->label('Gaji Pokok')
                            ->numeric()
                            ->prefix('Rp')
                            ->required()
                            ->default(0),

                        TextInput::make('total_earnings')
                            ->label('Total Pendapatan')
                            ->numeric()
                            ->prefix('Rp')
                            ->required()
                            ->default(0),

                        TextInput::make('total_deductions')
                            ->label('Total Potongan')
                            ->numeric()
                            ->prefix('Rp')
                            ->required()
                            ->default(0),

                        TextInput::make('net_salary')
                            ->label('Gaji Bersih (Take-Home Pay)')
                            ->numeric()
                            ->prefix('Rp')
                            ->required()
                            ->default(0)
                            ->columnSpanFull(),
                    ]),

                Section::make('Rekap Kehadiran')
                    ->columns(5)
                    ->schema([
                        TextInput::make('working_days')
                            ->label('Hari Kerja')
                            ->numeric()
                            ->suffix('hari')
                            ->default(0),

                        TextInput::make('working_hours')
                            ->label('Jam Kerja')
                            ->numeric()
                            ->suffix('jam')
                            ->default(0),

                        TextInput::make('overtime_hours')
                            ->label('Lembur')
                            ->numeric()
                            ->suffix('jam')
                            ->default(0),

                        TextInput::make('leave_days')
                            ->label('Hari Cuti')
                            ->numeric()
                            ->suffix('hari')
                            ->default(0),

                        TextInput::make('absent_days')
                            ->label('Hari Absen')
                            ->numeric()
                            ->suffix('hari')
                            ->default(0),
                    ]),

                Section::make('Pembayaran')
                    ->columns(2)
                    ->collapsed()
                    ->schema([
                        DateTimePicker::make('paid_at')
                            ->label('Dibayar Pada')
                            ->native(false),

                        TextInput::make('paid_via')
                            ->label('Metode Pembayaran')
                            ->placeholder('cth: Transfer BCA'),

                        Toggle::make('is_sent')
                            ->label('Slip sudah dikirim ke karyawan'),

                        DateTimePicker::make('sent_at')
                            ->label('Dikirim Pada')
                            ->native(false),

                        Textarea::make('notes')
                            ->label('Catatan')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
