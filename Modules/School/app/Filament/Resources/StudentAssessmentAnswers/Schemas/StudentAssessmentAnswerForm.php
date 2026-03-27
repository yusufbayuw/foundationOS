<?php

namespace Modules\School\Filament\Resources\StudentAssessmentAnswers\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;

class StudentAssessmentAnswerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant_id'))
                    ->relationship('tenant', 'name')
                    ->required(),
                Select::make('assessment_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('assessment_id'))
                    ->relationship('assessment', 'name')
                    ->required(),
                Select::make('assessment_item_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('assessment_item_id'))
                    ->relationship('assessmentItem', 'id')
                    ->required(),
                Select::make('student_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('student_id'))
                    ->relationship('student', 'id')
                    ->required(),
                Select::make('class_student_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('class_student_id'))
                    ->relationship('classStudent', 'id'),
                TextInput::make('graded_by')
                    ->label(\Modules\Core\Support\FilamentUi::field('graded_by'))
                    ->numeric(),
                Textarea::make('answer_text')
                    ->label(\Modules\Core\Support\FilamentUi::field('answer_text'))
                    ->columnSpanFull(),
                TextInput::make('answer_selected')
                    ->label(\Modules\Core\Support\FilamentUi::field('answer_selected')),
                TextInput::make('answer_attachment')
                    ->label(\Modules\Core\Support\FilamentUi::field('answer_attachment')),
                TextInput::make('score')
                    ->label(\Modules\Core\Support\FilamentUi::field('score'))
                    ->numeric(),
                TextInput::make('max_score')
                    ->label(\Modules\Core\Support\FilamentUi::field('max_score'))
                    ->numeric(),
                Toggle::make('is_correct')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_correct')),
                Textarea::make('grader_notes')
                    ->label(\Modules\Core\Support\FilamentUi::field('grader_notes'))
                    ->columnSpanFull(),
                DateTimePicker::make('graded_at'),
                TextInput::make('attempt_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('attempt_number'))
                    ->required()
                    ->numeric()
                    ->default(1),
                TextInput::make('time_spent_seconds')
                    ->label(\Modules\Core\Support\FilamentUi::field('time_spent_seconds'))
                    ->numeric(),
            ]);
    }
}
