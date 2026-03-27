<?php

namespace Modules\Employee\Filament\Resources\KpiScores\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;

class KpiScoreForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TenantField::make(),
                Select::make('employee_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('employee_id'))
                    ->relationship('employee', 'id')
                    ->required(),
                TextInput::make('kpi_template_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('kpi_template_id'))
                    ->numeric(),
                Select::make('evaluator_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('evaluator_id'))
                    ->relationship('evaluator', 'name'),
                TextInput::make('period_month')
                    ->label(\Modules\Core\Support\FilamentUi::field('period_month'))
                    ->required(),
                TextInput::make('period_year')
                    ->label(\Modules\Core\Support\FilamentUi::field('period_year'))
                    ->required(),
                Textarea::make('scores')
                    ->label(\Modules\Core\Support\FilamentUi::field('scores'))
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('total_score')
                    ->label(\Modules\Core\Support\FilamentUi::field('total_score'))
                    ->required()
                    ->numeric(),
                TextInput::make('grade')
                    ->label(\Modules\Core\Support\FilamentUi::field('grade')),
                Textarea::make('qualitative_assessment')
                    ->label(\Modules\Core\Support\FilamentUi::field('qualitative_assessment'))
                    ->columnSpanFull(),
                Textarea::make('employee_self_assessment')
                    ->label(\Modules\Core\Support\FilamentUi::field('employee_self_assessment'))
                    ->columnSpanFull(),
                Textarea::make('development_plan')
                    ->label(\Modules\Core\Support\FilamentUi::field('development_plan'))
                    ->columnSpanFull(),
                TextInput::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status'))
                    ->required()
                    ->default('draft'),
                DateTimePicker::make('submitted_at'),
                DateTimePicker::make('evaluated_at'),
                DateTimePicker::make('approved_at'),
            ]);
    }
}
