<?php

namespace Modules\School\Filament\Resources\StudentGrades\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class StudentGradeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('Basic Information'))
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        Select::make('student_id')
                            ->label(FilamentUi::field('student_id'))
                            ->relationship('student', 'id')
                            ->required(),
                        Select::make('assessment_id')
                            ->label(FilamentUi::field('assessment_id'))
                            ->relationship('assessment', 'name')
                            ->required(),
                        TextInput::make('graded_by')
                            ->label(FilamentUi::field('graded_by'))
                            ->numeric(),
                    ]),

                Section::make(FilamentUi::text('Scoring'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('score')
                            ->label(FilamentUi::field('score'))
                            ->required()
                            ->numeric(),
                        TextInput::make('score_letter')
                            ->label(FilamentUi::field('score_letter')),
                        TextInput::make('weight')
                            ->label(FilamentUi::field('weight'))
                            ->required()
                            ->numeric()
                            ->default(1),
                        TextInput::make('final_score')
                            ->label(FilamentUi::field('final_score'))
                            ->numeric(),
                        Textarea::make('notes')
                            ->label(FilamentUi::field('notes'))
                            ->columnSpanFull(),
                    ]),

                Section::make(FilamentUi::text('Status'))
                    ->columns(2)
                    ->schema([
                        Toggle::make('is_passed')
                            ->label(FilamentUi::field('is_passed')),
                        DateTimePicker::make('graded_at'),
                        Toggle::make('is_locked')
                            ->label(FilamentUi::field('is_locked'))
                            ->required(),
                    ]),
            ]);
    }
}
