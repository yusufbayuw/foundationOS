<?php

namespace Modules\Employee\Filament\Resources\KpiScores\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class KpiScoreInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('Employee & Evaluation'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('tenant.name')
                            ->label(FilamentUi::text('Tenant')),
                        TextEntry::make('employee.id')
                            ->label(FilamentUi::text('Employee')),
                        TextEntry::make('kpi_template_id')
                            ->label(FilamentUi::field('kpi_template_id'))
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('evaluator.name')
                            ->label(FilamentUi::text('Evaluator'))
                            ->placeholder('-'),
                        TextEntry::make('period_month')
                            ->label(FilamentUi::field('period_month')),
                        TextEntry::make('period_year')
                            ->label(FilamentUi::field('period_year')),
                    ]),

                Section::make(FilamentUi::text('Scores & Assessment'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('scores')
                            ->label(FilamentUi::field('scores'))
                            ->columnSpanFull(),
                        TextEntry::make('total_score')
                            ->label(FilamentUi::field('total_score'))
                            ->numeric(),
                        TextEntry::make('grade')
                            ->label(FilamentUi::field('grade'))
                            ->placeholder('-'),
                        TextEntry::make('qualitative_assessment')
                            ->label(FilamentUi::field('qualitative_assessment'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('employee_self_assessment')
                            ->label(FilamentUi::field('employee_self_assessment'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('development_plan')
                            ->label(FilamentUi::field('development_plan'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),

                Section::make(FilamentUi::text('Status & Approval'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('status')
                            ->label(FilamentUi::field('status')),
                        TextEntry::make('submitted_at')
                            ->label(FilamentUi::field('submitted_at'))
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('evaluated_at')
                            ->label(FilamentUi::field('evaluated_at'))
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('approved_at')
                            ->label(FilamentUi::field('approved_at'))
                            ->dateTime()
                            ->placeholder('-'),
                    ]),

                Section::make(FilamentUi::text('Timestamps'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('created_at')
                            ->label(FilamentUi::field('created_at'))
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('updated_at')
                            ->label(FilamentUi::field('updated_at'))
                            ->dateTime()
                            ->placeholder('-'),
                    ]),
            ]);
    }
}
