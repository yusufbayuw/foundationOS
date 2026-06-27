<?php

namespace Modules\Exam\Filament\Resources\ExamDefinitions\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Modules\Campus\Models\CourseOffering;
use Modules\Campus\Models\Lecturer;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;
use Modules\Exam\Enums\ExamAcademicContext;
use Modules\Exam\Enums\ExamStatus;
use Modules\Exam\Enums\ExamType;
use Modules\Exam\Enums\GradeSyncMode;
use Modules\Exam\Models\ExamDefinition;
use Modules\School\Models\Teacher;

class ExamDefinitionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('General information'))
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        TenantField::organizationSelect(),
                        Select::make('owner_user_id')
                            ->label(FilamentUi::field('owner_user_id'))
                            ->relationship('owner', 'name')
                            ->searchable()
                            ->preload(),
                        TextInput::make('name')
                            ->label(FilamentUi::text('Title'))
                            ->required()
                            ->columnSpanFull(),
                        TextInput::make('code')
                            ->label(FilamentUi::field('code')),
                        Textarea::make('description')
                            ->label(FilamentUi::field('description'))
                            ->columnSpanFull(),
                        Select::make('exam_type')
                            ->label(FilamentUi::field('exam_type'))
                            ->options(collect(ExamType::cases())->mapWithKeys(
                                fn (ExamType $case) => [$case->value => FilamentUi::text($case->label())]
                            ))
                            ->required()
                            ->default(ExamType::Practice->value),
                        Placeholder::make('status_display')
                            ->label(FilamentUi::field('status'))
                            ->content(fn (?ExamDefinition $record): string => $record?->status
                                ? FilamentUi::text($record->status->label())
                                : FilamentUi::text(ExamStatus::Draft->label()))
                            ->visible(fn (?ExamDefinition $record): bool => $record !== null),
                    ]),

                Section::make(FilamentUi::text('Academic context'))
                    ->columns(2)
                    ->schema([
                        Select::make('exam_academic_context')
                            ->label(FilamentUi::field('exam_academic_context'))
                            ->options(collect(ExamAcademicContext::cases())->mapWithKeys(
                                fn (ExamAcademicContext $case) => [$case->value => FilamentUi::text($case->label())]
                            ))
                            ->required()
                            ->live()
                            ->disabled(fn (?ExamDefinition $record): bool => $record !== null
                                && ! in_array($record->status, [ExamStatus::Draft, ExamStatus::Ready], true)),
                        Select::make('school_assessment_id')
                            ->label(FilamentUi::field('school_assessment_id'))
                            ->relationship('schoolAssessment', 'name')
                            ->searchable()
                            ->preload()
                            ->visible(fn (Get $get): bool => $get('exam_academic_context') === ExamAcademicContext::School->value),
                        Select::make('grade_sync_mode')
                            ->label(FilamentUi::field('grade_sync_mode'))
                            ->options(collect(GradeSyncMode::cases())->mapWithKeys(
                                fn (GradeSyncMode $case) => [$case->value => FilamentUi::text($case->label())]
                            ))
                            ->default(GradeSyncMode::None->value),
                        TextInput::make('grade_sync_target')
                            ->label(FilamentUi::field('grade_sync_target'))
                            ->helperText(FilamentUi::text('Optional. Campus component key, e.g. midterm or final.')),
                    ]),

                Section::make(FilamentUi::text('School references'))
                    ->columns(2)
                    ->visible(fn (Get $get): bool => $get('exam_academic_context') === ExamAcademicContext::School->value)
                    ->schema([
                        Select::make('academic_year_reference')
                            ->label(FilamentUi::field('academic_year_reference'))
                            ->relationship('academicYear', 'name')
                            ->searchable()
                            ->preload(),
                        Select::make('school_semester_reference')
                            ->label(FilamentUi::field('school_semester_reference'))
                            ->relationship('schoolSemester', 'name')
                            ->searchable()
                            ->preload(),
                        Select::make('school_class_reference')
                            ->label(FilamentUi::field('school_class_reference'))
                            ->relationship('schoolClass', 'name')
                            ->searchable()
                            ->preload(),
                        Select::make('school_subject_reference')
                            ->label(FilamentUi::field('school_subject_reference'))
                            ->relationship('schoolSubject', 'name')
                            ->searchable()
                            ->preload(),
                        TextInput::make('school_grade_level_reference')
                            ->label(FilamentUi::field('school_grade_level_reference'))
                            ->helperText(FilamentUi::text('Optional. Free text grade level, e.g. X or 10.')),
                        Select::make('school_teacher_reference')
                            ->label(FilamentUi::field('school_teacher_reference'))
                            ->relationship('schoolTeacher', 'id')
                            ->getOptionLabelFromRecordUsing(fn (Teacher $record): string => $record->user->name ?? $record->nip ?? (string) $record->id)
                            ->searchable()
                            ->preload(),
                    ]),

                Section::make(FilamentUi::text('Campus references'))
                    ->columns(2)
                    ->visible(fn (Get $get): bool => $get('exam_academic_context') === ExamAcademicContext::Campus->value)
                    ->schema([
                        Select::make('campus_academic_year_reference')
                            ->label(FilamentUi::field('campus_academic_year_reference'))
                            ->relationship('campusAcademicYear', 'name')
                            ->searchable()
                            ->preload(),
                        Select::make('campus_academic_term_reference')
                            ->label(FilamentUi::field('campus_academic_term_reference'))
                            ->relationship('campusAcademicTerm', 'name')
                            ->searchable()
                            ->preload(),
                        Select::make('campus_faculty_reference')
                            ->label(FilamentUi::field('campus_faculty_reference'))
                            ->relationship('campusFaculty', 'name')
                            ->searchable()
                            ->preload(),
                        Select::make('campus_study_program_reference')
                            ->label(FilamentUi::field('campus_study_program_reference'))
                            ->relationship('campusStudyProgram', 'name')
                            ->searchable()
                            ->preload(),
                        Select::make('campus_course_reference')
                            ->label(FilamentUi::field('campus_course_reference'))
                            ->relationship('campusCourse', 'name')
                            ->searchable()
                            ->preload(),
                        Select::make('campus_class_reference')
                            ->label(FilamentUi::field('campus_class_reference'))
                            ->relationship('campusClass', 'class_code')
                            ->getOptionLabelFromRecordUsing(fn (CourseOffering $record): string => $record->class_code ?? (string) $record->id)
                            ->searchable()
                            ->preload(),
                        Select::make('campus_lecturer_reference')
                            ->label(FilamentUi::field('campus_lecturer_reference'))
                            ->relationship('campusLecturer', 'full_name')
                            ->getOptionLabelFromRecordUsing(fn (Lecturer $record): string => $record->full_name ?? (string) $record->id)
                            ->searchable()
                            ->preload(),
                    ]),

                Section::make(FilamentUi::text('Standalone references'))
                    ->columns(2)
                    ->visible(fn (Get $get): bool => $get('exam_academic_context') === ExamAcademicContext::Standalone->value)
                    ->schema([
                        TextInput::make('standalone_subject')
                            ->label(FilamentUi::field('standalone_subject')),
                        TextInput::make('standalone_level')
                            ->label(FilamentUi::field('standalone_level')),
                        Textarea::make('target_description')
                            ->label(FilamentUi::field('target_description'))
                            ->columnSpanFull(),
                    ]),

                Section::make(FilamentUi::text('Schedule & scoring'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('duration_minutes')
                            ->label(FilamentUi::field('duration_minutes'))
                            ->numeric(),
                        TextInput::make('max_attempts')
                            ->label(FilamentUi::field('max_attempts'))
                            ->numeric(),
                        TextInput::make('max_score')
                            ->label(FilamentUi::field('max_score'))
                            ->numeric()
                            ->helperText(FilamentUi::text('Optional. Leave blank to use total from selected questions.')),
                        TextInput::make('passing_score')
                            ->label(FilamentUi::field('passing_score'))
                            ->numeric(),
                        Toggle::make('shuffle_questions')
                            ->label(FilamentUi::field('shuffle_questions')),
                        Toggle::make('shuffle_options')
                            ->label(FilamentUi::field('shuffle_options')),
                        Toggle::make('show_result')
                            ->label(FilamentUi::field('show_result')),
                        Toggle::make('show_explanation')
                            ->label(FilamentUi::field('show_explanation')),
                        DateTimePicker::make('starts_at')
                            ->label(FilamentUi::field('starts_at')),
                        DateTimePicker::make('ends_at')
                            ->label(FilamentUi::field('ends_at')),
                    ]),
            ]);
    }
}
