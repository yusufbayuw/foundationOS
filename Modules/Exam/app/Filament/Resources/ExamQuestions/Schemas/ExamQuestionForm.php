<?php

namespace Modules\Exam\Filament\Resources\ExamQuestions\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;
use Modules\Exam\Enums\QuestionDifficulty;
use Modules\Exam\Enums\QuestionStatus;
use Modules\Exam\Enums\QuestionType;
use Modules\Exam\Filament\Support\ExamQuestionFormSupport;
use Modules\Exam\Models\ExamQuestionBank;

class ExamQuestionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('Question bank & classification'))
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        Select::make('exam_question_bank_id')
                            ->label(FilamentUi::field('exam_question_bank_id'))
                            ->relationship('examQuestionBank', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->live()
                            ->helperText(fn (Get $get): ?string => self::bankContextSummary($get('exam_question_bank_id'))),
                        Select::make('type')
                            ->label(FilamentUi::field('type'))
                            ->options(collect(QuestionType::cases())->mapWithKeys(
                                fn (QuestionType $case) => [$case->value => FilamentUi::text($case->label())]
                            ))
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Set $set, ?string $state): void {
                                if ($state === QuestionType::TrueFalse->value) {
                                    $set('examQuestionOptions', [
                                        ['option_text' => 'True', 'is_correct' => true, 'sort_order' => 0],
                                        ['option_text' => 'False', 'is_correct' => false, 'sort_order' => 1],
                                    ]);
                                }
                            }),
                        Select::make('status')
                            ->label(FilamentUi::field('status'))
                            ->options(collect(QuestionStatus::cases())->mapWithKeys(
                                fn (QuestionStatus $case) => [$case->value => FilamentUi::text($case->label())]
                            ))
                            ->default(QuestionStatus::Draft->value)
                            ->required(),
                        TextInput::make('topic')
                            ->label(FilamentUi::field('topic')),
                        TextInput::make('subtopic')
                            ->label(FilamentUi::field('subtopic')),
                        Select::make('difficulty')
                            ->label(FilamentUi::field('difficulty'))
                            ->options(collect(QuestionDifficulty::cases())->mapWithKeys(
                                fn (QuestionDifficulty $case) => [$case->value => FilamentUi::text($case->label())]
                            )),
                        TextInput::make('question_number')
                            ->label(FilamentUi::field('question_number'))
                            ->numeric()
                            ->default(1),
                    ]),

                Section::make(FilamentUi::text('Question body'))
                    ->schema([
                        RichEditor::make('question_text')
                            ->label(FilamentUi::field('question_text'))
                            ->columnSpanFull(),
                        FileUpload::make('media_path')
                            ->label(FilamentUi::field('media_path'))
                            ->visibility('public')
                            ->directory('exam-questions')
                            ->columnSpanFull(),
                    ]),

                Section::make(FilamentUi::text('Answer options'))
                    ->visible(fn (Get $get): bool => in_array($get('type'), [
                        QuestionType::SingleChoice->value,
                        QuestionType::MultipleChoice->value,
                        QuestionType::TrueFalse->value,
                    ], true))
                    ->schema([
                        Repeater::make('examQuestionOptions')
                            ->relationship()
                            ->schema([
                                TextInput::make('option_text')
                                    ->label(FilamentUi::field('option_text'))
                                    ->required()
                                    ->columnSpan(2),
                                Toggle::make('is_correct')
                                    ->label(FilamentUi::field('is_correct')),
                                TextInput::make('sort_order')
                                    ->label(FilamentUi::field('sort_order'))
                                    ->numeric()
                                    ->default(0),
                            ])
                            ->columns(3)
                            ->defaultItems(0)
                            ->reorderable()
                            ->orderColumn('sort_order'),
                    ]),

                Section::make(FilamentUi::text('Scoring'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('score')
                            ->label(FilamentUi::field('score'))
                            ->numeric()
                            ->default(1),
                        Textarea::make('answer_key')
                            ->label(FilamentUi::field('answer_key'))
                            ->columnSpanFull(),
                        Textarea::make('explanation')
                            ->label(FilamentUi::field('explanation'))
                            ->columnSpanFull(),
                        Textarea::make('correct_answer')
                            ->label(FilamentUi::field('correct_answer'))
                            ->columnSpanFull()
                            ->visible(fn (Get $get): bool => in_array($get('type'), [
                                QuestionType::ShortAnswer->value,
                                QuestionType::Numeric->value,
                                QuestionType::Essay->value,
                            ], true)),
                    ]),

                Section::make(FilamentUi::text('MI mapping'))
                    ->collapsed()
                    ->schema([
                        Grid::make(2)->schema(ExamQuestionFormSupport::miFieldsSchema()),
                    ]),

                Section::make(FilamentUi::text('OSN metadata'))
                    ->collapsed()
                    ->schema([
                        TextInput::make('olympiad_subject')
                            ->label(FilamentUi::field('olympiad_subject')),
                        TextInput::make('olympiad_level')
                            ->label(FilamentUi::field('olympiad_level')),
                        TagsInput::make('skill_codes')
                            ->label(FilamentUi::field('skill_codes')),
                        TextInput::make('estimated_time_seconds')
                            ->label(FilamentUi::field('estimated_time_seconds'))
                            ->numeric(),
                    ]),
            ]);
    }

    public static function bankContextSummary(?string $bankId): ?string
    {
        if (blank($bankId)) {
            return null;
        }

        $bank = ExamQuestionBank::query()->with(['schoolSubject', 'schoolCurriculum', 'campusCourse', 'campusStudyProgram'])->find($bankId);

        if ($bank === null) {
            return null;
        }

        $parts = [$bank->academic_context_type?->label() ?? ''];

        if ($bank->isSchool()) {
            if ($bank->schoolSubject) {
                $parts[] = $bank->schoolSubject->name;
            }
            if ($bank->school_grade_level_reference) {
                $parts[] = $bank->school_grade_level_reference;
            }
        } elseif ($bank->isCampus()) {
            if ($bank->campusCourse) {
                $parts[] = $bank->campusCourse->name;
            }
            if ($bank->campusStudyProgram) {
                $parts[] = $bank->campusStudyProgram->name;
            }
        } else {
            if ($bank->standalone_subject) {
                $parts[] = $bank->standalone_subject;
            }
            if ($bank->standalone_level) {
                $parts[] = $bank->standalone_level;
            }
        }

        return implode(' · ', array_filter($parts));
    }
}
