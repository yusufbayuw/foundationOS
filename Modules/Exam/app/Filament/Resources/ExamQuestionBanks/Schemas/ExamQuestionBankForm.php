<?php

namespace Modules\Exam\Filament\Resources\ExamQuestionBanks\Schemas;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;
use Modules\Exam\Enums\ExamAcademicContext;
use Modules\Exam\Enums\QuestionBankStatus;

class ExamQuestionBankForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('General information'))
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        TextInput::make('name')
                            ->label(FilamentUi::field('name'))
                            ->required()
                            ->columnSpanFull(),
                        Textarea::make('description')
                            ->label(FilamentUi::field('description'))
                            ->columnSpanFull(),
                        Select::make('status')
                            ->label(FilamentUi::field('status'))
                            ->options(collect(QuestionBankStatus::cases())->mapWithKeys(
                                fn (QuestionBankStatus $case) => [$case->value => FilamentUi::text($case->label())]
                            ))
                            ->default(QuestionBankStatus::Active->value)
                            ->required(),
                        TextInput::make('code')
                            ->label(FilamentUi::field('code')),
                    ]),

                Section::make(FilamentUi::text('Academic context'))
                    ->columns(2)
                    ->schema([
                        Select::make('academic_context_type')
                            ->label(FilamentUi::field('academic_context_type'))
                            ->options(collect(ExamAcademicContext::cases())->mapWithKeys(
                                fn (ExamAcademicContext $case) => [$case->value => FilamentUi::text($case->label())]
                            ))
                            ->default(ExamAcademicContext::Standalone->value)
                            ->required()
                            ->live(),
                    ]),

                Section::make(FilamentUi::text('School references'))
                    ->columns(2)
                    ->visible(fn (Get $get): bool => $get('academic_context_type') === ExamAcademicContext::School->value)
                    ->schema([
                        Select::make('school_subject_reference')
                            ->label(FilamentUi::field('school_subject_reference'))
                            ->relationship('schoolSubject', 'name')
                            ->searchable()
                            ->preload(),
                        TextInput::make('school_grade_level_reference')
                            ->label(FilamentUi::field('school_grade_level_reference'))
                            ->helperText(FilamentUi::text('Optional. Free text grade level, e.g. X or 10.')),
                        Select::make('school_curriculum_reference')
                            ->label(FilamentUi::field('school_curriculum_reference'))
                            ->relationship('schoolCurriculum', 'name')
                            ->searchable()
                            ->preload(),
                    ]),

                Section::make(FilamentUi::text('Campus references'))
                    ->columns(2)
                    ->visible(fn (Get $get): bool => $get('academic_context_type') === ExamAcademicContext::Campus->value)
                    ->schema([
                        Select::make('campus_course_reference')
                            ->label(FilamentUi::field('campus_course_reference'))
                            ->relationship('campusCourse', 'name')
                            ->searchable()
                            ->preload(),
                        Select::make('campus_study_program_reference')
                            ->label(FilamentUi::field('campus_study_program_reference'))
                            ->relationship('campusStudyProgram', 'name')
                            ->searchable()
                            ->preload(),
                    ]),

                Section::make(FilamentUi::text('Standalone references'))
                    ->columns(2)
                    ->visible(fn (Get $get): bool => $get('academic_context_type') === ExamAcademicContext::Standalone->value)
                    ->schema([
                        TextInput::make('standalone_subject')
                            ->label(FilamentUi::field('standalone_subject')),
                        TextInput::make('standalone_level')
                            ->label(FilamentUi::field('standalone_level')),
                    ]),

                Section::make(FilamentUi::text('Advanced'))
                    ->collapsed()
                    ->schema([
                        KeyValue::make('metadata_json')
                            ->label(FilamentUi::field('metadata_json'))
                            ->keyLabel(FilamentUi::text('Key'))
                            ->valueLabel(FilamentUi::text('Value')),
                    ]),
            ]);
    }
}
