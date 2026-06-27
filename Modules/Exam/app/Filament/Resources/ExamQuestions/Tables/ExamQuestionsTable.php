<?php

namespace Modules\Exam\Filament\Resources\ExamQuestions\Tables;

use App\Support\TypedValue;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\Core\Support\FilamentUi;
use Modules\Exam\Enums\ExamAcademicContext;
use Modules\Exam\Enums\QuestionDifficulty;
use Modules\Exam\Enums\QuestionStatus;
use Modules\Exam\Enums\QuestionType;
use Modules\Exam\Models\ExamQuestion;
use Modules\Exam\Models\ExamQuestionBank;

class ExamQuestionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('examQuestionBank.name')
                    ->label(FilamentUi::field('exam_question_bank_id'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('examQuestionBank.academic_context_type')
                    ->label(FilamentUi::field('academic_context_type'))
                    ->badge(),
                TextColumn::make('type')
                    ->label(FilamentUi::field('type'))
                    ->badge(),
                TextColumn::make('topic')
                    ->label(FilamentUi::field('topic'))
                    ->searchable(),
                TextColumn::make('difficulty')
                    ->label(FilamentUi::field('difficulty'))
                    ->badge(),
                TextColumn::make('score')
                    ->label(FilamentUi::field('score'))
                    ->numeric(),
                TextColumn::make('status')
                    ->label(FilamentUi::field('status'))
                    ->badge(),
            ])
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('exam_question_bank_id')
                    ->label(FilamentUi::field('exam_question_bank_id'))
                    ->relationship('examQuestionBank', 'name'),
                SelectFilter::make('academic_context')
                    ->label(FilamentUi::field('academic_context_type'))
                    ->options(collect(ExamAcademicContext::cases())->mapWithKeys(
                        fn (ExamAcademicContext $case) => [$case->value => FilamentUi::text($case->label())]
                    ))
                    ->query(function (Builder $query, array $data): Builder {
                        if (blank($data['value'] ?? null)) {
                            return $query;
                        }

                        return $query->whereHas(
                            'examQuestionBank',
                            function (Builder $bankQuery) use ($data): void {
                                /** @var Builder<ExamQuestionBank> $bankQuery */
                                $bankQuery->where('academic_context_type', $data['value']);
                            },
                        );
                    }),
                SelectFilter::make('type')
                    ->label(FilamentUi::field('type'))
                    ->options(collect(QuestionType::cases())->mapWithKeys(
                        fn (QuestionType $case) => [$case->value => FilamentUi::text($case->label())]
                    )),
                SelectFilter::make('difficulty')
                    ->label(FilamentUi::field('difficulty'))
                    ->options(collect(QuestionDifficulty::cases())->mapWithKeys(
                        fn (QuestionDifficulty $case) => [$case->value => FilamentUi::text($case->label())]
                    )),
                SelectFilter::make('status')
                    ->label(FilamentUi::field('status'))
                    ->options(collect(QuestionStatus::cases())->mapWithKeys(
                        fn (QuestionStatus $case) => [$case->value => FilamentUi::text($case->label())]
                    )),
                SelectFilter::make('topic')
                    ->label(FilamentUi::field('topic'))
                    ->options(fn (): array => ExamQuestion::query()
                        ->whereNotNull('topic')
                        ->distinct()
                        ->pluck('topic', 'topic')
                        ->all()),
                SelectFilter::make('olympiad_level')
                    ->label(FilamentUi::field('olympiad_level'))
                    ->options(fn (): array => ExamQuestion::query()
                        ->whereNotNull('metadata_json')
                        ->whereRaw("JSON_EXTRACT(metadata_json, '$.olympiad_level') IS NOT NULL")
                        ->selectRaw("JSON_UNQUOTE(JSON_EXTRACT(metadata_json, '$.olympiad_level')) as olympiad_level")
                        ->distinct()
                        ->pluck('olympiad_level', 'olympiad_level')
                        ->all())
                    ->query(function (Builder $query, array $data): Builder {
                        if (blank($data['value'] ?? null)) {
                            return $query;
                        }

                        /** @var Builder<ExamQuestion> $query */
                        return $query->whereRaw(
                            'JSON_UNQUOTE(JSON_EXTRACT(metadata_json, ?)) = ?',
                            ['$.olympiad_level', TypedValue::string($data['value'])],
                        );
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
