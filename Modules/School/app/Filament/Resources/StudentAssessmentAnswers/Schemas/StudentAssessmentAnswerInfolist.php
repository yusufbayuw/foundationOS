<?php

namespace Modules\School\Filament\Resources\StudentAssessmentAnswers\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class StudentAssessmentAnswerInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Tenant')),
                TextEntry::make('assessment.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Assessment')),
                TextEntry::make('assessmentItem.id')
                    ->label(\Modules\Core\Support\FilamentUi::text('Assessment item')),
                TextEntry::make('student.id')
                    ->label(\Modules\Core\Support\FilamentUi::text('Student')),
                TextEntry::make('classStudent.id')
                    ->label(\Modules\Core\Support\FilamentUi::text('Class student'))
                    ->placeholder('-'),
                TextEntry::make('graded_by')
                    ->label(\Modules\Core\Support\FilamentUi::field('graded_by'))
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('answer_text')
                    ->label(\Modules\Core\Support\FilamentUi::field('answer_text'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('answer_selected')
                    ->label(\Modules\Core\Support\FilamentUi::field('answer_selected'))
                    ->placeholder('-'),
                TextEntry::make('answer_attachment')
                    ->label(\Modules\Core\Support\FilamentUi::field('answer_attachment'))
                    ->placeholder('-'),
                TextEntry::make('score')
                    ->label(\Modules\Core\Support\FilamentUi::field('score'))
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('max_score')
                    ->label(\Modules\Core\Support\FilamentUi::field('max_score'))
                    ->numeric()
                    ->placeholder('-'),
                IconEntry::make('is_correct')
                    ->boolean()
                    ->placeholder('-'),
                TextEntry::make('grader_notes')
                    ->label(\Modules\Core\Support\FilamentUi::field('grader_notes'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('graded_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('graded_at'))
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('attempt_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('attempt_number'))
                    ->numeric(),
                TextEntry::make('time_spent_seconds')
                    ->label(\Modules\Core\Support\FilamentUi::field('time_spent_seconds'))
                    ->numeric()
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
