<?php

namespace Modules\Exam\Filament\Resources\ExamDefinitions\RelationManagers;

use Filament\Actions\Action;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Modules\Core\Support\FilamentUi;
use Modules\Exam\Enums\ParticipantSource;
use Modules\Exam\Models\ExamDefinition;
use Modules\Exam\Services\ExamResultExportService;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExamResultsRelationManager extends RelationManager
{
    protected static string $relationship = 'examResults';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return FilamentUi::text('Results');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading(FilamentUi::text('Exam results'))
            ->description(FilamentUi::text('Final scores synced from Cloudflare runtime.'))
            ->modifyQueryUsing(fn ($query) => $query->with('examParticipant')->latest('submitted_at'))
            ->columns([
                TextColumn::make('id')
                    ->label(FilamentUi::field('result_id'))
                    ->copyable()
                    ->limit(8)
                    ->tooltip(fn (string $state): string => $state),
                TextColumn::make('examParticipant.student_name')
                    ->label(FilamentUi::field('student_name'))
                    ->searchable(),
                TextColumn::make('examParticipant.participant_source')
                    ->label(FilamentUi::field('participant_source'))
                    ->badge()
                    ->formatStateUsing(fn (?ParticipantSource $state): string => $state !== null
                        ? FilamentUi::text($state->label())
                        : '-'),
                TextColumn::make('examParticipant.student_identifier')
                    ->label(FilamentUi::field('student_identifier'))
                    ->toggleable(),
                TextColumn::make('status')
                    ->label(FilamentUi::field('status'))
                    ->badge(),
                TextColumn::make('score')
                    ->label(FilamentUi::field('score'))
                    ->numeric(decimalPlaces: 2),
                TextColumn::make('percentage')
                    ->label(FilamentUi::field('percentage'))
                    ->numeric(decimalPlaces: 2)
                    ->suffix('%'),
                TextColumn::make('is_passed')
                    ->label(FilamentUi::field('is_passed'))
                    ->formatStateUsing(fn (?bool $state): string => $state === null
                        ? '-'
                        : ($state ? FilamentUi::text('Yes') : FilamentUi::text('No'))),
                TextColumn::make('submitted_at')
                    ->label(FilamentUi::field('submitted_at'))
                    ->dateTime(),
                TextColumn::make('suspicious_activity_count')
                    ->label(FilamentUi::field('suspicious_activity_count'))
                    ->numeric(),
            ])
            ->headerActions($this->exportHeaderActions())
            ->defaultSort('score', 'desc')
            ->paginated([10, 25, 50]);
    }

    /**
     * @return list<Action>
     */
    protected function exportHeaderActions(): array
    {
        return [
            Action::make('exportResultsCsv')
                ->label(FilamentUi::text('Export CSV'))
                ->icon('heroicon-o-arrow-down-tray')
                ->visible(fn (): bool => $this->canExportResults())
                ->action(function (): StreamedResponse {
                    $exam = $this->resolveOwnerExam();
                    $this->authorize('exportResult', $exam);

                    return app(ExamResultExportService::class)->exportCsv($exam)['response'];
                }),
            Action::make('exportResultsExcel')
                ->label(FilamentUi::text('Export Excel'))
                ->icon('heroicon-o-table-cells')
                ->visible(fn (): bool => $this->canExportResults())
                ->action(function (): StreamedResponse {
                    $exam = $this->resolveOwnerExam();
                    $this->authorize('exportResult', $exam);

                    return app(ExamResultExportService::class)->exportExcel($exam)['response'];
                }),
            Action::make('exportResultsPdf')
                ->label(FilamentUi::text('Export PDF'))
                ->icon('heroicon-o-document-arrow-down')
                ->visible(fn (): bool => $this->canExportResults())
                ->url(fn (): string => route('exam.results.pdf', $this->resolveOwnerExam()))
                ->openUrlInNewTab(),
        ];
    }

    public function isReadOnly(): bool
    {
        return true;
    }

    protected function canExportResults(): bool
    {
        try {
            return Gate::check('exportResult', $this->resolveOwnerExam());
        } catch (\Throwable) {
            return false;
        }
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
