<?php

namespace Modules\Campus\Filament\Resources\Theses\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class ThesisForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Relationships')
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        Select::make('collage_student_id')
                            ->label(FilamentUi::field('collage_student_id'))
                            ->relationship('collageStudent', 'id')
                            ->required(),
                        Select::make('advisor_lecturer_id')
                            ->label(FilamentUi::field('advisor_lecturer_id'))
                            ->relationship('advisorLecturer', 'id'),
                        Select::make('examiner_lecturer_id')
                            ->label(FilamentUi::field('examiner_lecturer_id'))
                            ->relationship('examinerLecturer', 'id'),
                    ]),

                Section::make('Thesis Details')
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->label(FilamentUi::field('title'))
                            ->required(),
                        TextInput::make('research_area')
                            ->label(FilamentUi::field('research_area')),
                        TextInput::make('status')
                            ->label(FilamentUi::field('status'))
                            ->required()
                            ->default('proposal'),
                        TextInput::make('document_path')
                            ->label(FilamentUi::field('document_path')),
                    ]),

                Section::make('Timeline')
                    ->columns(2)
                    ->schema([
                        DateTimePicker::make('proposal_submitted_at'),
                        DateTimePicker::make('defense_date'),
                    ]),

                Section::make('Grade & Notes')
                    ->columns(2)
                    ->schema([
                        TextInput::make('grade_letter')
                            ->label(FilamentUi::field('grade_letter')),
                        TextInput::make('grade_point')
                            ->label(FilamentUi::field('grade_point'))
                            ->numeric(),
                        Textarea::make('notes')
                            ->label(FilamentUi::field('notes'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
