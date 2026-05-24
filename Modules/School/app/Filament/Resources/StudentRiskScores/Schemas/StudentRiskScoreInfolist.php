<?php

namespace Modules\School\Filament\Resources\StudentRiskScores\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class StudentRiskScoreInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(FilamentUi::text('General information'))
                ->schema([
                    TextEntry::make('tenant_id')
                        ->label(FilamentUi::field('tenant_id'))
                        ->placeholder('-'),
                    TextEntry::make('student_id')
                        ->label(FilamentUi::field('student_id'))
                        ->placeholder('-'),
                    TextEntry::make('composite_score')
                        ->label(FilamentUi::field('composite_score'))
                        ->placeholder('-'),
                    TextEntry::make('academic_score')
                        ->label(FilamentUi::field('academic_score'))
                        ->placeholder('-'),
                    TextEntry::make('financial_score')
                        ->label(FilamentUi::field('financial_score'))
                        ->placeholder('-'),
                    TextEntry::make('behavioral_score')
                        ->label(FilamentUi::field('behavioral_score'))
                        ->placeholder('-'),
                    TextEntry::make('health_score')
                        ->label(FilamentUi::field('health_score'))
                        ->placeholder('-'),
                    TextEntry::make('attendance_score')
                        ->label(FilamentUi::field('attendance_score'))
                        ->placeholder('-'),
                    TextEntry::make('is_at_risk')
                        ->label(FilamentUi::field('is_at_risk'))
                        ->placeholder('-'),
                    TextEntry::make('meta')
                        ->label(FilamentUi::field('meta'))
                        ->placeholder('-'),
                    TextEntry::make('computed_at')
                        ->label(FilamentUi::field('computed_at'))
                        ->dateTime()
                        ->placeholder('-'),
                ])
                ->columns(2),
        ]);
    }
}
