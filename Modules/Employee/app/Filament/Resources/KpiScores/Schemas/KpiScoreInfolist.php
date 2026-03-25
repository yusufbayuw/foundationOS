<?php

namespace Modules\Employee\Filament\Resources\KpiScores\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class KpiScoreInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Tenant')),
                TextEntry::make('employee.id')
                    ->label(\Modules\Core\Support\FilamentUi::text('Employee')),
                TextEntry::make('kpi_template_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('kpi_template_id'))
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('evaluator.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Evaluator'))
                    ->placeholder('-'),
                TextEntry::make('period_month')
                    ->label(\Modules\Core\Support\FilamentUi::field('period_month')),
                TextEntry::make('period_year')
                    ->label(\Modules\Core\Support\FilamentUi::field('period_year')),
                TextEntry::make('scores')
                    ->label(\Modules\Core\Support\FilamentUi::field('scores'))
                    ->columnSpanFull(),
                TextEntry::make('total_score')
                    ->label(\Modules\Core\Support\FilamentUi::field('total_score'))
                    ->numeric(),
                TextEntry::make('grade')
                    ->label(\Modules\Core\Support\FilamentUi::field('grade'))
                    ->placeholder('-'),
                TextEntry::make('qualitative_assessment')
                    ->label(\Modules\Core\Support\FilamentUi::field('qualitative_assessment'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('employee_self_assessment')
                    ->label(\Modules\Core\Support\FilamentUi::field('employee_self_assessment'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('development_plan')
                    ->label(\Modules\Core\Support\FilamentUi::field('development_plan'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status')),
                TextEntry::make('submitted_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('submitted_at'))
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('evaluated_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('evaluated_at'))
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('approved_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('approved_at'))
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('created_at'))
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('updated_at'))
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
