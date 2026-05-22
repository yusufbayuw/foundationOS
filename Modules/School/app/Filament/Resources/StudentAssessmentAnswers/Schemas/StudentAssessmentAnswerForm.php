<?php

namespace Modules\School\Filament\Resources\StudentAssessmentAnswers\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class StudentAssessmentAnswerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('Basic Information'))
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        Select::make('assessment_id')
                            ->label(FilamentUi::field('assessment_id'))
                            ->relationship('assessment', 'name')
                            ->required(),
                        Select::make('assessment_item_id')
                            ->label(FilamentUi::field('assessment_item_id'))
                            ->relationship('assessmentItem', 'id')
                            ->required(),
                        Select::make('student_id')
                            ->label(FilamentUi::field('student_id'))
                            ->relationship('student', 'id')
                            ->required(),
                        Select::make('class_student_id')
                            ->label(FilamentUi::field('class_student_id'))
                            ->relationship('classStudent', 'id'),
                        TextInput::make('graded_by')
                            ->label(FilamentUi::field('graded_by'))
                            ->numeric(),
                    ]),

                Section::make(FilamentUi::text('Answer'))
                    ->columns(2)
                    ->schema([
                        Textarea::make('answer_text')
                            ->label(FilamentUi::field('answer_text'))
                            ->columnSpanFull(),
                        TextInput::make('answer_selected')
                            ->label(FilamentUi::field('answer_selected')),
                        TextInput::make('answer_attachment')
                            ->label(FilamentUi::field('answer_attachment')),
                    ]),

                Section::make(FilamentUi::text('Scoring'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('score')
                            ->label(FilamentUi::field('score'))
                            ->numeric(),
                        TextInput::make('max_score')
                            ->label(FilamentUi::field('max_score'))
                            ->numeric(),
                        Toggle::make('is_correct')
                            ->label(FilamentUi::field('is_correct')),
                        Textarea::make('grader_notes')
                            ->label(FilamentUi::field('grader_notes'))
                            ->columnSpanFull(),
                        DateTimePicker::make('graded_at'),
                    ]),

                Section::make(FilamentUi::text('Attempt Information'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('attempt_number')
                            ->label(FilamentUi::field('attempt_number'))
                            ->required()
                            ->numeric()
                            ->default(1),
                        TextInput::make('time_spent_seconds')
                            ->label(FilamentUi::field('time_spent_seconds'))
                            ->numeric(),
                    ]),
            ]);
    }
}
