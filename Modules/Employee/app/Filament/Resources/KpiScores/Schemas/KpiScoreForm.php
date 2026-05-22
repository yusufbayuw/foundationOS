<?php

namespace Modules\Employee\Filament\Resources\KpiScores\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class KpiScoreForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('Employee & Evaluation'))
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        Select::make('employee_id')
                            ->label(FilamentUi::field('employee_id'))
                            ->relationship('employee', 'id')
                            ->required(),
                        TextInput::make('kpi_template_id')
                            ->label(FilamentUi::field('kpi_template_id'))
                            ->numeric(),
                        Select::make('evaluator_id')
                            ->label(FilamentUi::field('evaluator_id'))
                            ->relationship('evaluator', 'name'),
                        TextInput::make('period_month')
                            ->label(FilamentUi::field('period_month'))
                            ->required(),
                        TextInput::make('period_year')
                            ->label(FilamentUi::field('period_year'))
                            ->required(),
                    ]),

                Section::make(FilamentUi::text('Scores & Assessment'))
                    ->columns(2)
                    ->schema([
                        Textarea::make('scores')
                            ->label(FilamentUi::field('scores'))
                            ->required()
                            ->columnSpanFull(),
                        TextInput::make('total_score')
                            ->label(FilamentUi::field('total_score'))
                            ->required()
                            ->numeric(),
                        TextInput::make('grade')
                            ->label(FilamentUi::field('grade')),
                        Textarea::make('qualitative_assessment')
                            ->label(FilamentUi::field('qualitative_assessment'))
                            ->columnSpanFull(),
                        Textarea::make('employee_self_assessment')
                            ->label(FilamentUi::field('employee_self_assessment'))
                            ->columnSpanFull(),
                        Textarea::make('development_plan')
                            ->label(FilamentUi::field('development_plan'))
                            ->columnSpanFull(),
                    ]),

                Section::make(FilamentUi::text('Status & Approval'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('status')
                            ->label(FilamentUi::field('status'))
                            ->required()
                            ->default('draft'),
                        DateTimePicker::make('submitted_at'),
                        DateTimePicker::make('evaluated_at'),
                        DateTimePicker::make('approved_at'),
                    ]),
            ]);
    }
}
