<?php

namespace Modules\School\Filament\Resources\StudentRiskScores\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class StudentRiskScoreForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(FilamentUi::text('General information'))
                ->schema([
                    TenantField::make(),
                    TextInput::make('student_id')
                        ->label(FilamentUi::field('student_id'))
                        ->numeric(),
                    TextInput::make('composite_score')
                        ->label(FilamentUi::field('composite_score'))
                        ->numeric(),
                    TextInput::make('academic_score')
                        ->label(FilamentUi::field('academic_score'))
                        ->numeric(),
                    TextInput::make('financial_score')
                        ->label(FilamentUi::field('financial_score'))
                        ->numeric(),
                    TextInput::make('behavioral_score')
                        ->label(FilamentUi::field('behavioral_score'))
                        ->numeric(),
                    TextInput::make('health_score')
                        ->label(FilamentUi::field('health_score'))
                        ->numeric(),
                    TextInput::make('attendance_score')
                        ->label(FilamentUi::field('attendance_score'))
                        ->numeric(),
                    TextInput::make('is_at_risk')
                        ->label(FilamentUi::field('is_at_risk')),
                    Textarea::make('meta')
                        ->label(FilamentUi::field('meta'))
                        ->columnSpanFull(),
                    TextInput::make('computed_at')
                        ->label(FilamentUi::field('computed_at')),
                ])
                ->columns(2),
        ]);
    }
}
