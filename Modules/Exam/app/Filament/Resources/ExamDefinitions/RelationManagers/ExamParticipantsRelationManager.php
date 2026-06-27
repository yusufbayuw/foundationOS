<?php

namespace Modules\Exam\Filament\Resources\ExamDefinitions\RelationManagers;

use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Modules\Core\Support\FilamentUi;
use Modules\Exam\Enums\ExamAcademicContext;
use Modules\Exam\Enums\ExamAuditAction;
use Modules\Exam\Enums\ExamStatus;
use Modules\Exam\Enums\ParticipantSource;
use Modules\Exam\Enums\ParticipantStatus;
use Modules\Exam\Models\ExamDefinition;
use Modules\Exam\Models\ExamParticipant;
use Modules\Exam\Services\ExamAuditLogger;
use Modules\Exam\Services\ExamParticipantResolver;
use Modules\Exam\Services\ExamTokenService;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExamParticipantsRelationManager extends RelationManager
{
    protected static string $relationship = 'examParticipants';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return FilamentUi::text('Participants');
    }

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
            TextInput::make('student_name')
                ->label(FilamentUi::field('student_name'))
                ->required(),
            TextInput::make('student_identifier')
                ->label(FilamentUi::field('student_identifier')),
            TextInput::make('email')
                ->label(FilamentUi::field('email'))
                ->email(),
            Select::make('status')
                ->label(FilamentUi::field('status'))
                ->options(collect(ParticipantStatus::cases())->mapWithKeys(
                    fn (ParticipantStatus $case) => [$case->value => FilamentUi::text($case->label())]
                ))
                ->required()
                ->default(ParticipantStatus::Assigned),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading(FilamentUi::text('Exam participants'))
            ->modifyQueryUsing(fn ($query) => $query->with('activeToken'))
            ->columns([
                TextColumn::make('student_name')
                    ->label(FilamentUi::field('student_name'))
                    ->searchable(),
                TextColumn::make('student_identifier')
                    ->label(FilamentUi::field('student_identifier'))
                    ->searchable(),
                TextColumn::make('participant_source')
                    ->label(FilamentUi::field('participant_source'))
                    ->badge()
                    ->formatStateUsing(fn (?ParticipantSource $state): string => $state !== null
                        ? FilamentUi::text($state->label())
                        : '-'),
                TextColumn::make('status')
                    ->label(FilamentUi::field('status'))
                    ->badge()
                    ->formatStateUsing(fn (?ParticipantStatus $state): string => $state !== null
                        ? FilamentUi::text($state->label())
                        : '-'),
                TextColumn::make('activeToken.token')
                    ->label(FilamentUi::field('token'))
                    ->placeholder('-')
                    ->copyable(),
                TextColumn::make('assigned_at')
                    ->label(FilamentUi::field('assigned_at'))
                    ->dateTime()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('participant_source')
                    ->label(FilamentUi::field('participant_source'))
                    ->options(collect(ParticipantSource::cases())->mapWithKeys(
                        fn (ParticipantSource $case) => [$case->value => FilamentUi::text($case->label())]
                    )),
                SelectFilter::make('status')
                    ->label(FilamentUi::field('status'))
                    ->options(collect(ParticipantStatus::cases())->mapWithKeys(
                        fn (ParticipantStatus $case) => [$case->value => FilamentUi::text($case->label())]
                    )),
            ])
            ->headerActions($this->headerActions())
            ->recordActions([
                Action::make('regenerateToken')
                    ->label(FilamentUi::text('Regenerate token'))
                    ->icon('heroicon-o-arrow-path')
                    ->requiresConfirmation()
                    ->visible(fn (): bool => ! $this->isReadOnly()
                        && $this->getOwnerRecord() instanceof ExamDefinition
                        && Gate::check('regenerateToken', $this->getOwnerRecord()))
                    ->action(function (ExamParticipant $record): void {
                        $exam = $this->getOwnerRecord();

                        if ($exam instanceof ExamDefinition) {
                            $this->authorize('regenerateToken', $exam);
                        }

                        app(ExamTokenService::class)->regenerate($record);

                        if ($exam instanceof ExamDefinition) {
                            app(ExamAuditLogger::class)->log(
                                ExamAuditAction::RegenerateToken,
                                $exam,
                                'Participant token regenerated.',
                                subject: $record,
                                newValues: ['participant_id' => $record->id],
                            );
                        }

                        Notification::make()
                            ->title(FilamentUi::text('Token regenerated.'))
                            ->success()
                            ->send();
                    }),
                EditAction::make()
                    ->visible(fn (): bool => ! $this->isReadOnly()),
                DeleteAction::make()
                    ->visible(fn (): bool => ! $this->isReadOnly()),
            ])
            ->toolbarActions([
                BulkAction::make('exportTokensCsv')
                    ->label(FilamentUi::text('Export tokens CSV'))
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(fn (Collection $records) => $this->exportTokensCsv($records)),
            ]);
    }

    /**
     * @return array<int, Action|CreateAction>
     */
    protected function headerActions(): array
    {
        $definition = $this->getOwnerRecord();
        $readOnly = $this->isReadOnly();

        $actions = [];

        if ($definition instanceof ExamDefinition && $definition->exam_academic_context === ExamAcademicContext::School) {
            $actions[] = Action::make('generateFromClass')
                ->label(FilamentUi::text('Generate from class'))
                ->icon('heroicon-o-user-group')
                ->requiresConfirmation()
                ->visible(fn (): bool => ! $readOnly && $definition->school_class_reference !== null)
                ->action(fn () => $this->notifyResolverResult(
                    app(ExamParticipantResolver::class)->resolveFromSchoolClass($definition),
                    FilamentUi::text('Participants generated from class.'),
                ));
        }

        if ($definition instanceof ExamDefinition && $definition->exam_academic_context === ExamAcademicContext::Campus) {
            $actions[] = Action::make('generateFromCourseClass')
                ->label(FilamentUi::text('Generate from course class'))
                ->icon('heroicon-o-academic-cap')
                ->requiresConfirmation()
                ->visible(fn (): bool => ! $readOnly && $definition->campus_class_reference !== null)
                ->action(fn () => $this->notifyResolverResult(
                    app(ExamParticipantResolver::class)->resolveFromCampusClass($definition),
                    FilamentUi::text('Participants generated from course class.'),
                ));
        }

        if (! $readOnly) {
            $actions[] = CreateAction::make()
                ->label(FilamentUi::text('Add participant'))
                ->visible(fn (): bool => $definition instanceof ExamDefinition
                    && $definition->exam_academic_context === ExamAcademicContext::Standalone)
                ->mutateFormDataUsing(function (array $data) use ($definition): array {
                    if (! $definition instanceof ExamDefinition) {
                        return $data;
                    }

                    $data['participant_source'] = ParticipantSource::Manual;
                    $data['tenant_id'] = $definition->tenant_id;
                    $data['assigned_at'] = now();

                    return $data;
                })
                ->after(function (ExamParticipant $record): void {
                    $record->issueToken();
                });

            $actions[] = Action::make('importCsv')
                ->label(FilamentUi::text('Import CSV'))
                ->icon('heroicon-o-arrow-up-tray')
                ->schema([
                    FileUpload::make('csv_file')
                        ->label(FilamentUi::field('csv_file'))
                        ->acceptedFileTypes(['text/csv', 'text/plain', 'application/vnd.ms-excel'])
                        ->required(),
                ])
                ->action(function (array $data) use ($definition): void {
                    if (! $definition instanceof ExamDefinition) {
                        return;
                    }

                    $path = $data['csv_file'] ?? null;

                    if ($path === null) {
                        return;
                    }

                    $contents = Storage::disk('local')->get($path);
                    Storage::disk('local')->delete($path);

                    if ($contents === null) {
                        Notification::make()
                            ->title(FilamentUi::text('Could not read CSV file.'))
                            ->danger()
                            ->send();

                        return;
                    }

                    $rows = $this->parseCsvRows($contents);

                    $this->notifyResolverResult(
                        app(ExamParticipantResolver::class)->importFromCsvRows($definition, $rows),
                        FilamentUi::text('CSV import completed.'),
                    );
                });

            $actions[] = Action::make('downloadImportTemplate')
                ->label(FilamentUi::text('Download import template'))
                ->icon('heroicon-o-document-arrow-down')
                ->action(fn (): StreamedResponse => response()->streamDownload(
                    fn () => print file_get_contents(module_path('Exam', 'resources/import/exam_participant_import_template.csv')),
                    'exam_participant_import_template.csv',
                    ['Content-Type' => 'text/csv; charset=UTF-8'],
                ));
        }

        $actions[] = Action::make('exportAllTokensCsv')
            ->label(FilamentUi::text('Export tokens CSV'))
            ->icon('heroicon-o-arrow-down-tray')
            ->action(fn () => $this->exportTokensCsv(
                $definition instanceof ExamDefinition
                    ? $definition->examParticipants()->with('activeToken')->get()
                    : collect(),
            ));

        if ($definition instanceof ExamDefinition) {
            $actions[] = Action::make('printTokenCards')
                ->label(FilamentUi::text('Print token cards'))
                ->icon('heroicon-o-printer')
                ->url(fn (): string => route('exam.participant-tokens.pdf', $definition))
                ->openUrlInNewTab();
        }

        return $actions;
    }

    /**
     * @param  array{created: int, skipped: int, errors: list<string>}  $result
     */
    protected function notifyResolverResult(array $result, string $title): void
    {
        $body = FilamentUi::text('Created').': '.$result['created']
            .' · '.FilamentUi::text('Skipped').': '.$result['skipped'];

        if ($result['errors'] !== []) {
            $body .= "\n".implode("\n", array_slice($result['errors'], 0, 5));
        }

        Notification::make()
            ->title($title)
            ->body($body)
            ->success()
            ->send();

        $this->dispatch('refresh');
    }

    /**
     * @return list<array<string, mixed>>
     */
    protected function parseCsvRows(string $contents): array
    {
        $lines = preg_split('/\r\n|\r|\n/', trim($contents)) ?: [];
        $rows = [];
        $header = null;

        foreach ($lines as $line) {
            $line = trim($line);

            if ($line === '') {
                continue;
            }

            $cells = str_getcsv($line);

            if ($header === null) {
                $header = array_map(fn (string $col): string => strtolower(trim($col)), $cells);

                continue;
            }

            $row = [];

            foreach ($header as $index => $column) {
                $row[$column] = $cells[$index] ?? null;
            }

            $rows[] = $row;
        }

        return $rows;
    }

    protected function exportTokensCsv(Collection $records): StreamedResponse
    {
        $filename = 'exam_participant_tokens_'.now()->format('Ymd_His').'.csv';

        return response()->streamDownload(function () use ($records): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['student_name', 'student_identifier', 'token', 'status']);

            foreach ($records as $record) {
                if (! $record instanceof ExamParticipant) {
                    continue;
                }

                fputcsv($handle, [
                    $record->student_name,
                    $record->student_identifier,
                    $record->activeToken?->token,
                    $record->status?->value,
                ]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
