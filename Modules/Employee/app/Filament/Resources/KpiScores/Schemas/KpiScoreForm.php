<?php

namespace Modules\Employee\Filament\Resources\KpiScores\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Models\User;
use Modules\Employee\Enums\KpiScoreStatus;
use Modules\Employee\Models\Employee;
use Modules\Employee\Models\KpiTemplate;
use Modules\Employee\Services\KpiScoringService;

class KpiScoreForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TenantField::make(),

                Section::make('Identitas Penilaian KPI')
                    ->columns(3)
                    ->schema([
                        Select::make('employee_id')
                            ->label('Karyawan')
                            ->options(fn () => Employee::query()
                                ->orderBy('full_name')
                                ->pluck('full_name', 'id'))
                            ->searchable()
                            ->required(),

                        Select::make('kpi_template_id')
                            ->label('Template KPI')
                            ->options(fn () => KpiTemplate::where('is_active', true)
                                ->orderBy('name')
                                ->pluck('name', 'id'))
                            ->searchable()
                            ->nullable(),

                        Select::make('evaluator_id')
                            ->label('Evaluator')
                            ->options(fn () => User::orderBy('name')->pluck('name', 'id'))
                            ->searchable()
                            ->nullable(),

                        Select::make('period_month')
                            ->label('Bulan')
                            ->options([
                                '1' => 'Januari', '2' => 'Februari', '3' => 'Maret',
                                '4' => 'April', '5' => 'Mei', '6' => 'Juni',
                                '7' => 'Juli', '8' => 'Agustus', '9' => 'September',
                                '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
                            ])
                            ->default(now()->month)
                            ->required(),

                        TextInput::make('period_year')
                            ->label('Tahun')
                            ->numeric()
                            ->default(now()->year)
                            ->required(),

                        Select::make('status')
                            ->label('Status')
                            ->options(KpiScoreStatus::class)
                            ->required()
                            ->default(KpiScoreStatus::Draft->value),
                    ]),

                Section::make('Penilaian per Indikator')
                    ->columnSpanFull()
                    ->schema([
                        Repeater::make('scores')
                            ->label('Skor Indikator')
                            ->schema([
                                TextInput::make('indicator_name')
                                    ->label('Indikator')
                                    ->required()
                                    ->columnSpan(2),

                                TextInput::make('weight')
                                    ->label('Bobot (%)')
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->suffix('%')
                                    ->required()
                                    ->default(0),

                                TextInput::make('target_value')
                                    ->label('Target')
                                    ->numeric()
                                    ->nullable(),

                                TextInput::make('actual_value')
                                    ->label('Aktual')
                                    ->numeric()
                                    ->nullable(),

                                TextInput::make('score')
                                    ->label('Skor (0–100)')
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, $set, $get) {
                                        // Auto-recalc total whenever any score changes
                                    }),

                                Textarea::make('notes')
                                    ->label('Catatan')
                                    ->rows(1)
                                    ->columnSpan(2),
                            ])
                            ->columns(4)
                            ->addActionLabel('Tambah Indikator')
                            ->reorderable()
                            ->collapsible()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if (is_array($state)) {
                                    $service = app(KpiScoringService::class);
                                    $total = $service->computeWeightedScore($state);
                                    $set('total_score', $total);
                                    $set('grade', $service->resolveGrade($total));
                                }
                            }),
                    ]),

                Section::make('Hasil Penilaian')
                    ->columns(3)
                    ->schema([
                        TextInput::make('total_score')
                            ->label('Total Skor')
                            ->numeric()
                            ->suffix('/ 100')
                            ->hint('Dihitung otomatis dari bobot indikator'),

                        TextInput::make('grade')
                            ->label('Grade')
                            ->hint('A ≥ 90 | B ≥ 80 | C ≥ 70 | D ≥ 60 | E < 60'),

                        Textarea::make('qualitative_assessment')
                            ->label('Penilaian Kualitatif')
                            ->rows(2)
                            ->columnSpanFull(),

                        Textarea::make('employee_self_assessment')
                            ->label('Self-Assessment Karyawan')
                            ->rows(2)
                            ->columnSpanFull(),

                        Textarea::make('development_plan')
                            ->label('Rencana Pengembangan')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
