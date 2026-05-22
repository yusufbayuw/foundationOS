<?php

namespace Modules\School\Filament\Resources\StudentAssessmentAnswers\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class StudentAssessmentAnswerInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('Basic Information'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('tenant.name')
                            ->label(FilamentUi::text('Tenant')),
                        TextEntry::make('assessment.name')
                            ->label(FilamentUi::text('Assessment')),
                        TextEntry::make('assessmentItem.id')
                            ->label(FilamentUi::text('Assessment item')),
                        TextEntry::make('student.id')
                            ->label(FilamentUi::text('Student')),
                        TextEntry::make('classStudent.id')
                            ->label(FilamentUi::text('Class student'))
                            ->placeholder('-'),
                        TextEntry::make('graded_by')
                            ->label(FilamentUi::field('graded_by'))
                            ->numeric()
                            ->placeholder('-'),
                    ]),

                Section::make(FilamentUi::text('Answer'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('answer_text')
                            ->label(FilamentUi::field('answer_text'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('answer_selected')
                            ->label(FilamentUi::field('answer_selected'))
                            ->placeholder('-'),
                        TextEntry::make('answer_attachment')
                            ->label(FilamentUi::field('answer_attachment'))
                            ->placeholder('-'),
                    ]),

                Section::make(FilamentUi::text('Scoring'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('score')
                            ->label(FilamentUi::field('score'))
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('max_score')
                            ->label(FilamentUi::field('max_score'))
                            ->numeric()
                            ->placeholder('-'),
                        IconEntry::make('is_correct')
                            ->boolean()
                            ->placeholder('-'),
                        TextEntry::make('grader_notes')
                            ->label(FilamentUi::field('grader_notes'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('graded_at')
                            ->label(FilamentUi::field('graded_at'))
                            ->dateTime()
                            ->placeholder('-'),
                    ]),

                Section::make(FilamentUi::text('Attempt Information'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('attempt_number')
                            ->label(FilamentUi::field('attempt_number'))
                            ->numeric(),
                        TextEntry::make('time_spent_seconds')
                            ->label(FilamentUi::field('time_spent_seconds'))
                            ->numeric()
                            ->placeholder('-'),
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
