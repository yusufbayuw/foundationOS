<?php

namespace Modules\Exam\Filament\Resources\ExamDefinitions\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;
use Modules\Exam\Enums\ExamAcademicContext;
use Modules\Exam\Enums\ExamPurpose;
use Modules\Exam\Enums\ExamStatus;
use Modules\Exam\Enums\GradeSyncMode;

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
                        Select::make('academic_period_id')
                            ->label(FilamentUi::field('academic_period_id'))
                            ->relationship('academicPeriod', 'name'),
                        Select::make('owner_user_id')
                            ->label(FilamentUi::field('owner_user_id'))
                            ->relationship('owner', 'name'),
                        TextInput::make('name')
                            ->label(FilamentUi::field('name'))
                            ->required()
                            ->columnSpanFull(),
                        TextInput::make('code')
                            ->label(FilamentUi::field('code')),
                        Textarea::make('description')
                            ->label(FilamentUi::field('description'))
                            ->columnSpanFull(),
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
                            ->live(),
                        Select::make('exam_purpose')
                            ->label(FilamentUi::field('exam_purpose'))
                            ->options(collect(ExamPurpose::cases())->mapWithKeys(
                                fn (ExamPurpose $case) => [$case->value => FilamentUi::text($case->label())]
                            )),
                        TextInput::make('context_reference_type')
                            ->label(FilamentUi::field('context_reference_type'))
                            ->helperText(FilamentUi::text('Optional. FQCN or slug, e.g. school_class or Modules\\School\\Models\\Student.')),
                        TextInput::make('context_reference_id')
                            ->label(FilamentUi::field('context_reference_id'))
                            ->numeric(),
                        TextInput::make('school_assessment_id')
                            ->label(FilamentUi::field('school_assessment_id'))
                            ->numeric()
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

                Section::make(FilamentUi::text('Schedule & scoring'))
                    ->columns(2)
                    ->schema([
                        Select::make('status')
                            ->label(FilamentUi::field('status'))
                            ->options(collect(ExamStatus::cases())->mapWithKeys(
                                fn (ExamStatus $case) => [$case->value => FilamentUi::text($case->label())]
                            ))
                            ->default(ExamStatus::Draft->value)
                            ->required(),
                        TextInput::make('duration_minutes')
                            ->label(FilamentUi::field('duration_minutes'))
                            ->numeric(),
                        TextInput::make('max_attempts')
                            ->label(FilamentUi::field('max_attempts'))
                            ->numeric(),
                        TextInput::make('max_score')
                            ->label(FilamentUi::field('max_score'))
                            ->numeric(),
                        TextInput::make('passing_score')
                            ->label(FilamentUi::field('passing_score'))
                            ->numeric(),
                        Toggle::make('shuffle_questions')
                            ->label(FilamentUi::field('shuffle_questions')),
                        DateTimePicker::make('starts_at')
                            ->label(FilamentUi::field('starts_at')),
                        DateTimePicker::make('ends_at')
                            ->label(FilamentUi::field('ends_at')),
                    ]),
            ]);
    }
}
