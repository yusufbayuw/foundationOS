<?php

namespace Modules\Exam\Filament\Resources\ExamDefinitions\RelationManagers;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\Core\Support\FilamentUi;
use Modules\Exam\Enums\ExamStatus;
use Modules\Exam\Enums\QuestionDifficulty;
use Modules\Exam\Enums\QuestionType;
use Modules\Exam\Models\ExamDefinition;
use Modules\Exam\Models\ExamDefinitionQuestion;
use Modules\Exam\Models\ExamQuestion;
use Modules\Exam\Services\ExamDefinitionQuestionQuery;
use Modules\Exam\Services\ExamDefinitionScoreCalculator;

class ExamDefinitionQuestionsRelationManager extends RelationManager
{
    protected static string $relationship = 'examDefinitionQuestions';

    public function isReadOnly(): bool
    {
        $owner = $this->getOwnerRecord();

        if (! $owner instanceof ExamDefinition) {
            return false;
        }

        return ! in_array($owner->status, [ExamStatus::Draft, ExamStatus::Ready], true);
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('sort_order')
                ->label(FilamentUi::field('sort_order'))
                ->numeric()
                ->default(0)
                ->required(),
            TextInput::make('score_override')
                ->label(FilamentUi::field('score_override'))
                ->numeric()
                ->helperText(FilamentUi::text('Optional. Leave blank to use the question default score.')),
        ]);
    }

    public function table(Table $table): Table
    {
        $calculator = app(ExamDefinitionScoreCalculator::class);

        return $table
            ->heading(FilamentUi::text('Exam question builder'))
            ->description(function () use ($calculator): string {
                $definition = $this->getOwnerRecord();

                if (! $definition instanceof ExamDefinition) {
                    return '';
                }

                $count = $calculator->questionCount($definition);
                $total = $calculator->totalScore($definition);

                return FilamentUi::text('Total questions').': '.$count.' · '.FilamentUi::text('Total score').': '.$total;
            })
            ->columns([
                TextColumn::make('sort_order')
                    ->label(FilamentUi::field('sort_order'))
                    ->sortable(),
                TextColumn::make('examQuestion.question_number')
                    ->label(FilamentUi::field('question_number'))
                    ->sortable(),
                TextColumn::make('examQuestion.type')
                    ->label(FilamentUi::field('type'))
                    ->badge(),
                TextColumn::make('examQuestion.topic')
                    ->label(FilamentUi::field('topic'))
                    ->searchable(),
                TextColumn::make('examQuestion.subtopic')
                    ->label(FilamentUi::field('subtopic'))
                    ->toggleable(),
                TextColumn::make('examQuestion.difficulty')
                    ->label(FilamentUi::field('difficulty'))
                    ->badge()
                    ->toggleable(),
                TextColumn::make('examQuestion.examQuestionBank.name')
                    ->label(FilamentUi::field('exam_question_bank_id'))
                    ->toggleable(),
                TextColumn::make('score_override')
                    ->label(FilamentUi::field('score_override'))
                    ->placeholder('-'),
                TextColumn::make('effective_score')
                    ->label(FilamentUi::field('score'))
                    ->state(fn (ExamDefinitionQuestion $record): float => $record->effectiveScore()),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label(FilamentUi::field('type'))
                    ->options(collect(QuestionType::cases())->mapWithKeys(
                        fn (QuestionType $case) => [$case->value => FilamentUi::text($case->label())]
                    ))
                    ->query(fn (Builder $query, array $data): Builder => filled($data['value'] ?? null)
                        ? $query->whereHas('examQuestion', fn (Builder $q) => $q->where('type', $data['value']))
                        : $query),
                SelectFilter::make('difficulty')
                    ->label(FilamentUi::field('difficulty'))
                    ->options(collect(QuestionDifficulty::cases())->mapWithKeys(
                        fn (QuestionDifficulty $case) => [$case->value => FilamentUi::text($case->label())]
                    ))
                    ->query(fn (Builder $query, array $data): Builder => filled($data['value'] ?? null)
                        ? $query->whereHas('examQuestion', fn (Builder $q) => $q->where('difficulty', $data['value']))
                        : $query),
                Filter::make('topic')
                    ->schema([
                        TextInput::make('topic')
                            ->label(FilamentUi::field('topic')),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => filled($data['topic'] ?? null)
                        ? $query->whereHas('examQuestion', fn (Builder $q) => $q->where('topic', 'like', '%'.$data['topic'].'%'))
                        : $query),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->headerActions([
                $this->addQuestionAction(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    protected function addQuestionAction(): Action
    {
        return Action::make('addQuestion')
            ->label(FilamentUi::text('Add question'))
            ->icon('heroicon-o-plus')
            ->schema([
                Toggle::make('include_cross_context')
                    ->label(FilamentUi::text('Include cross-context questions'))
                    ->visible(fn (): bool => auth()->user()?->isGlobalSuperAdmin() ?? false)
                    ->default(false)
                    ->live(),
                Select::make('exam_question_id')
                    ->label(FilamentUi::field('exam_question_id'))
                    ->searchable()
                    ->required()
                    ->options(function (Get $get): array {
                        $definition = $this->getOwnerRecord();

                        if (! $definition instanceof ExamDefinition) {
                            return [];
                        }

                        $includeCross = (bool) $get('include_cross_context');
                        $user = auth()->user();

                        if ($user === null) {
                            return [];
                        }

                        $attachedIds = $definition->examDefinitionQuestions()
                            ->pluck('exam_question_id')
                            ->all();

                        return app(ExamDefinitionQuestionQuery::class)
                            ->forPicker($definition, $user, [], $includeCross)
                            ->when($attachedIds !== [], fn (Builder $query) => $query->whereNotIn('id', $attachedIds))
                            ->with('examQuestionBank')
                            ->orderBy('question_number')
                            ->limit(200)
                            ->get()
                            ->mapWithKeys(fn (ExamQuestion $question): array => [
                                $question->id => trim(strip_tags((string) $question->question_text)) ?: $question->id,
                            ])
                            ->all();
                    }),
                TextInput::make('sort_order')
                    ->label(FilamentUi::field('sort_order'))
                    ->numeric()
                    ->default(fn (): int => (int) $this->getOwnerRecord()?->examDefinitionQuestions()->max('sort_order') + 1),
                TextInput::make('score_override')
                    ->label(FilamentUi::field('score_override'))
                    ->numeric(),
            ])
            ->action(function (array $data): void {
                $definition = $this->getOwnerRecord();

                if (! $definition instanceof ExamDefinition) {
                    return;
                }

                $question = ExamQuestion::withoutTenantScope()->findOrFail($data['exam_question_id']);
                $user = auth()->user();

                if ($user === null) {
                    return;
                }

                app(ExamDefinitionQuestionQuery::class)->assertQuestionAttachable(
                    $definition,
                    $question,
                    $user,
                    (bool) ($data['include_cross_context'] ?? false),
                );

                ExamDefinitionQuestion::query()->create([
                    'tenant_id' => $definition->tenant_id,
                    'exam_definition_id' => $definition->id,
                    'exam_question_id' => $question->id,
                    'sort_order' => (int) ($data['sort_order'] ?? 0),
                    'score_override' => $data['score_override'] ?? null,
                ]);
            });
    }
}
