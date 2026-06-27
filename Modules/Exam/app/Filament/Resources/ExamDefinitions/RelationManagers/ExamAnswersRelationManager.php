<?php

namespace Modules\Exam\Filament\Resources\ExamDefinitions\RelationManagers;

use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\HtmlString;
use Modules\Core\Models\User;
use Modules\Core\Support\FilamentUi;
use Modules\Exam\Enums\QuestionType;
use Modules\Exam\Models\ExamAnswer;
use Modules\Exam\Models\ExamDefinition;
use Modules\Exam\Services\ExamManualGradingService;

class ExamAnswersRelationManager extends RelationManager
{
    protected static string $relationship = 'examAnswers';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return FilamentUi::text('Answers');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading(FilamentUi::text('Exam answers'))
            ->description(FilamentUi::text('Review synced answers and grade essay responses manually.'))
            ->modifyQueryUsing(fn ($query) => $query->with(['examQuestion', 'examAttempt.examParticipant', 'grader']))
            ->columns([
                TextColumn::make('id')
                    ->label(FilamentUi::field('answer_id'))
                    ->copyable()
                    ->limit(8)
                    ->tooltip(fn (string $state): string => $state),
                TextColumn::make('examAttempt.examParticipant.student_name')
                    ->label(FilamentUi::field('student_name'))
                    ->searchable(),
                TextColumn::make('examQuestion.type')
                    ->label(FilamentUi::field('question_type'))
                    ->badge()
                    ->formatStateUsing(fn (?QuestionType $state): string => $state !== null
                        ? FilamentUi::text($state->label())
                        : '-'),
                TextColumn::make('answer_value')
                    ->label(FilamentUi::field('answer_text'))
                    ->limit(40)
                    ->wrap(),
                TextColumn::make('score')
                    ->label(FilamentUi::field('score'))
                    ->numeric(decimalPlaces: 2),
                TextColumn::make('manual_score')
                    ->label(FilamentUi::field('manual_score'))
                    ->numeric(decimalPlaces: 2)
                    ->placeholder('-'),
                TextColumn::make('graded_at')
                    ->label(FilamentUi::field('graded_at'))
                    ->dateTime()
                    ->placeholder('-'),
                TextColumn::make('grader.name')
                    ->label(FilamentUi::field('graded_by'))
                    ->placeholder('-'),
            ])
            ->filters([
                SelectFilter::make('needs_grading')
                    ->label(FilamentUi::field('needs_grading'))
                    ->options([
                        'yes' => FilamentUi::text('Yes'),
                        'no' => FilamentUi::text('No'),
                    ])
                    ->query(function ($query, array $data) {
                        if (($data['value'] ?? null) === 'yes') {
                            $query->whereHas('examQuestion', fn ($q) => $q->where($q->qualifyColumn('type'), QuestionType::Essay))
                                ->whereNull('manual_score');
                        }

                        if (($data['value'] ?? null) === 'no') {
                            $query->where(function ($q): void {
                                $q->whereDoesntHave('examQuestion', fn ($inner) => $inner->where($inner->qualifyColumn('type'), QuestionType::Essay))
                                    ->orWhereNotNull('manual_score');
                            });
                        }
                    }),
            ])
            ->recordActions([
                Action::make('gradeEssay')
                    ->label(FilamentUi::text('Grade essay'))
                    ->icon('heroicon-o-pencil-square')
                    ->visible(fn (ExamAnswer $record): bool => $record->requiresManualGrading()
                        && Gate::check('gradeAnswer', $this->resolveOwnerExam()))
                    ->fillForm(fn (ExamAnswer $record): array => [
                        'answer_preview' => $record->answer_value,
                        'attachments_preview' => $this->formatAttachments($record),
                        'manual_score' => $record->manual_score ?? $record->score,
                        'feedback' => $record->feedback,
                        'rubric_json' => $record->rubric_json ?? [],
                    ])
                    ->schema([
                        Placeholder::make('answer_preview')
                            ->label(FilamentUi::field('answer_text'))
                            ->content(fn (?string $state): string => $state ?? '-'),
                        Placeholder::make('attachments_preview')
                            ->label(FilamentUi::field('attachments'))
                            ->content(fn (?string $state): HtmlString => new HtmlString($state ?? '-'))
                            ->visible(fn (?string $state): bool => filled($state) && $state !== '-'),
                        KeyValue::make('rubric_json')
                            ->label(FilamentUi::field('rubric_json'))
                            ->nullable(),
                        TextInput::make('manual_score')
                            ->label(FilamentUi::field('manual_score'))
                            ->numeric()
                            ->required(),
                        Textarea::make('feedback')
                            ->label(FilamentUi::field('feedback'))
                            ->rows(4),
                    ])
                    ->action(function (array $data, ExamAnswer $record): void {
                        $exam = $this->resolveOwnerExam();
                        $this->authorize('gradeAnswer', $exam);

                        $user = Filament::auth()->user();

                        if (! $user instanceof User) {
                            return;
                        }

                        app(ExamManualGradingService::class)->gradeEssay(
                            $record,
                            $user,
                            (float) $data['manual_score'],
                            $data['feedback'] ?? null,
                            $data['rubric_json'] ?? null,
                        );

                        Notification::make()
                            ->title(FilamentUi::text('Essay graded successfully.'))
                            ->success()
                            ->send();
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50]);
    }

    protected function formatAttachments(ExamAnswer $answer): string
    {
        $attachments = $answer->metadata_json['attachments'] ?? [];

        if (! is_array($attachments) || $attachments === []) {
            return '-';
        }

        $links = [];

        foreach ($attachments as $attachment) {
            if (is_string($attachment)) {
                $links[] = '<a href="'.e($attachment).'" target="_blank" rel="noopener">'.e($attachment).'</a>';
            } elseif (is_array($attachment)) {
                $url = $attachment['url'] ?? $attachment['path'] ?? null;
                $label = $attachment['name'] ?? $url ?? FilamentUi::text('Attachment');

                if (is_string($url) && $url !== '') {
                    $links[] = '<a href="'.e($url).'" target="_blank" rel="noopener">'.e((string) $label).'</a>';
                }
            }
        }

        return $links !== [] ? implode('<br>', $links) : '-';
    }

    public function isReadOnly(): bool
    {
        return true;
    }

    protected function resolveOwnerExam(): ExamDefinition
    {
        $exam = $this->getOwnerRecord();

        if (! $exam instanceof ExamDefinition) {
            abort(404);
        }

        return $exam;
    }
}
