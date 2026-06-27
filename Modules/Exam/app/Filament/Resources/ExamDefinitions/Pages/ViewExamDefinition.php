<?php

namespace Modules\Exam\Filament\Resources\ExamDefinitions\Pages;

use App\Support\TypedValue;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Modules\Core\Support\FilamentUi;
use Modules\Exam\Enums\ExamAuditAction;
use Modules\Exam\Enums\ExamStatus;
use Modules\Exam\Exceptions\ExamRuntimeException;
use Modules\Exam\Filament\Resources\ExamDefinitions\ExamDefinitionResource;
use Modules\Exam\Models\ExamDefinition;
use Modules\Exam\Services\ExamAuditLogger;
use Modules\Exam\Services\ExamGradebookExportService;
use Modules\Exam\Services\ExamLifecycleService;
use Modules\Exam\Services\ExamParticipantSyncService;
use Modules\Exam\Services\ExamPublishService;
use Modules\Exam\Services\ExamResultSyncService;

/**
 * @property ExamDefinition $record
 */
class ViewExamDefinition extends ViewRecord
{
    protected static string $resource = ExamDefinitionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->visible(fn (): bool => in_array($this->record->status, [ExamStatus::Draft, ExamStatus::Ready], true)
                    && Gate::check('update', $this->record)),
            Action::make('markReady')
                ->label(FilamentUi::text('Mark as ready'))
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn (): bool => $this->record->status === ExamStatus::Draft
                    && Gate::check('update', $this->record))
                ->action(function (): void {
                    try {
                        app(ExamLifecycleService::class)->markReady($this->record);

                        Notification::make()
                            ->title(FilamentUi::text('Exam marked as ready.'))
                            ->success()
                            ->send();

                        $this->refreshFormData(['status']);
                    } catch (\InvalidArgumentException $exception) {
                        $this->notifyRuntimeError($exception);
                    }
                }),
            Action::make('publishToRuntime')
                ->label(FilamentUi::text('Publish to runtime'))
                ->icon('heroicon-o-bolt')
                ->color('warning')
                ->requiresConfirmation()
                ->visible(fn (): bool => in_array($this->record->status, [ExamStatus::Ready, ExamStatus::Scheduled], true)
                    && ! Str::isUuid((string) $this->record->runtime_exam_id)
                    && Gate::check('publish', $this->record))
                ->action(function (): void {
                    try {
                        $snapshot = app(ExamPublishService::class)->publish($this->record);

                        $this->notifyRuntimeSuccess(
                            FilamentUi::text('Exam published to runtime.'),
                            FilamentUi::text('Runtime ID').': '.($snapshot->runtime_exam_id ?? '-'),
                        );

                        $this->refreshFormData(['status', 'runtime_exam_id', 'published_at', 'last_published_at']);
                    } catch (\Throwable $exception) {
                        $this->notifyRuntimeError($exception);
                    }
                }),
            Action::make('republishToRuntime')
                ->label(FilamentUi::text('Republish to runtime'))
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->requiresConfirmation()
                ->visible(fn (): bool => Str::isUuid((string) $this->record->runtime_exam_id)
                    && in_array($this->record->status, [ExamStatus::Published, ExamStatus::Scheduled, ExamStatus::Ready], true)
                    && Gate::check('republish', $this->record))
                ->action(function (): void {
                    try {
                        $snapshot = app(ExamPublishService::class)->republish($this->record);

                        $this->notifyRuntimeSuccess(
                            FilamentUi::text('Exam republished to runtime.'),
                            FilamentUi::text('Runtime ID').': '.($snapshot->runtime_exam_id ?? '-'),
                        );

                        $this->refreshFormData(['runtime_exam_id', 'last_published_at']);
                    } catch (\Throwable $exception) {
                        $this->notifyRuntimeError($exception);
                    }
                }),
            Action::make('openControlRoom')
                ->label(FilamentUi::text('Open control room'))
                ->icon('heroicon-o-signal')
                ->color('info')
                ->visible(fn (): bool => Gate::check('openControlRoom', $this->record))
                ->action(function (): void {
                    app(ExamAuditLogger::class)->log(
                        ExamAuditAction::OpenControlRoom,
                        $this->record,
                        'Control room opened for exam.',
                    );

                    $runtimeExamId = (string) ($this->record->runtime_exam_id ?? '');
                    if (! Str::isUuid($runtimeExamId)) {
                        Notification::make()
                            ->title(FilamentUi::text('Control room unavailable'))
                            ->body(FilamentUi::text('Publish this exam to runtime before opening the control room.'))
                            ->warning()
                            ->send();

                        return;
                    }

                    $url = rtrim(TypedValue::string(config('exam.control_room.base_url')), '/').'/admin/exams/'.$runtimeExamId.'/control';
                    $this->js('window.open('.json_encode($url).', "_blank")');
                }),
            Action::make('duplicateExam')
                ->label(FilamentUi::text('Duplicate exam'))
                ->icon('heroicon-o-document-duplicate')
                ->color('gray')
                ->requiresConfirmation()
                ->visible(fn (): bool => Gate::check('replicate', $this->record))
                ->action(function (): void {
                    $copy = app(ExamLifecycleService::class)->duplicate($this->record);

                    Notification::make()
                        ->title(FilamentUi::text('Exam duplicated.'))
                        ->success()
                        ->send();

                    $this->redirect(static::getResource()::getUrl('view', ['record' => $copy]));
                }),
            Action::make('closeExam')
                ->label(FilamentUi::text('Close exam'))
                ->icon('heroicon-o-lock-closed')
                ->color('danger')
                ->requiresConfirmation()
                ->visible(fn (): bool => in_array($this->record->status, [ExamStatus::Ready, ExamStatus::Published, ExamStatus::Scheduled], true)
                    && Gate::check('update', $this->record))
                ->action(function (): void {
                    try {
                        app(ExamLifecycleService::class)->close($this->record);

                        Notification::make()
                            ->title(FilamentUi::text('Exam closed.'))
                            ->success()
                            ->send();

                        $this->refreshFormData(['status']);
                    } catch (\InvalidArgumentException $exception) {
                        $this->notifyRuntimeError($exception);
                    }
                }),
            Action::make('syncParticipants')
                ->label(FilamentUi::text('Generate participants'))
                ->icon('heroicon-o-user-plus')
                ->requiresConfirmation()
                ->visible(fn (): bool => in_array($this->record->status, [ExamStatus::Draft, ExamStatus::Ready], true)
                    && Gate::check('syncParticipants', $this->record))
                ->action(function (): void {
                    $result = app(ExamParticipantSyncService::class)->sync($this->record);

                    $body = FilamentUi::text('Created').': '.$result['created']
                        .' · '.FilamentUi::text('Skipped').': '.$result['skipped']
                        .' · '.FilamentUi::text('Total').': '.$result['total'];

                    if ($result['errors'] !== []) {
                        $body .= "\n".implode("\n", array_slice($result['errors'], 0, 3));
                    }

                    Notification::make()
                        ->title(FilamentUi::text('Participants synced.'))
                        ->body($body)
                        ->success()
                        ->send();
                }),
            Action::make('syncParticipantsToRuntime')
                ->label(FilamentUi::text('Sync participants only'))
                ->icon('heroicon-o-users')
                ->requiresConfirmation()
                ->visible(fn (): bool => Str::isUuid((string) $this->record->runtime_exam_id)
                    && Gate::check('syncParticipants', $this->record))
                ->action(function (): void {
                    try {
                        $log = app(ExamPublishService::class)->syncParticipantsOnly($this->record);

                        $this->notifyRuntimeSuccess(
                            FilamentUi::text('Participants synced to runtime.'),
                            FilamentUi::text('Participant count').': '.TypedValue::string($log->request_summary['participant_count'] ?? 0),
                        );
                    } catch (\Throwable $exception) {
                        $this->notifyRuntimeError($exception);
                    }
                }),
            Action::make('syncAdminAccessToRuntime')
                ->label(FilamentUi::text('Sync admin access'))
                ->icon('heroicon-o-shield-check')
                ->requiresConfirmation()
                ->visible(fn (): bool => Str::isUuid((string) $this->record->runtime_exam_id)
                    && Gate::check('publish', $this->record))
                ->action(function (): void {
                    try {
                        $log = app(ExamPublishService::class)->syncAdminAccessOnly($this->record);

                        $this->notifyRuntimeSuccess(
                            FilamentUi::text('Admin access synced to runtime.'),
                            FilamentUi::text('Admin access count').': '.TypedValue::string($log->request_summary['admin_access_count'] ?? 0),
                        );
                    } catch (\Throwable $exception) {
                        $this->notifyRuntimeError($exception);
                    }
                }),
            Action::make('syncResults')
                ->label(FilamentUi::text('Sync results'))
                ->icon('heroicon-o-arrow-down-tray')
                ->requiresConfirmation()
                ->visible(fn (): bool => Str::isUuid((string) $this->record->runtime_exam_id)
                    && Gate::check('syncResults', $this->record))
                ->action(function (): void {
                    try {
                        $summary = app(ExamResultSyncService::class)->sync($this->record);

                        $body = FilamentUi::text('Attempts').': '.($summary['attempts_synced'])
                            .' · '.FilamentUi::text('Answers').': '.($summary['answers_synced'])
                            .' · '.FilamentUi::text('Results').': '.($summary['results_synced']);

                        if (($summary['errors']) !== []) {
                            $body .= "\n".implode("\n", array_slice($summary['errors'], 0, 3));
                        }

                        $this->notifyRuntimeSuccess(FilamentUi::text('Results synced from runtime.'), $body);
                    } catch (\Throwable $exception) {
                        $this->notifyRuntimeError($exception);
                    }
                }),
            Action::make('pushToSchoolGradebook')
                ->label(FilamentUi::text('Push to School Gradebook'))
                ->icon('heroicon-o-academic-cap')
                ->color('primary')
                ->requiresConfirmation()
                ->visible(fn (): bool => $this->record->isSchool()
                    && Gate::check('pushToSchoolGradebook', $this->record))
                ->action(function (): void {
                    $summary = app(ExamGradebookExportService::class)->pushToSchoolGradebook($this->record);

                    $body = FilamentUi::text('Success').': '.($summary['success'])
                        .' · '.FilamentUi::text('Skipped').': '.($summary['skipped'])
                        .' · '.FilamentUi::text('Failed').': '.($summary['failed']);

                    if (($summary['integration']) === 'event_only') {
                        $body .= "\n".FilamentUi::text('Gradebook integration deferred; events dispatched.')
                            .' ('.($summary['events_dispatched']).')';
                    }

                    if (($summary['errors']) !== []) {
                        $body .= "\n".implode("\n", array_slice($summary['errors'], 0, 3));
                    }

                    Notification::make()
                        ->title(FilamentUi::text('School gradebook export finished.'))
                        ->body($body)
                        ->success()
                        ->send();
                }),
            Action::make('pushToCampusGradebook')
                ->label(FilamentUi::text('Push to Campus Gradebook'))
                ->icon('heroicon-o-building-library')
                ->color('primary')
                ->requiresConfirmation()
                ->visible(fn (): bool => $this->record->isCampus()
                    && Gate::check('pushToCampusGradebook', $this->record))
                ->action(function (): void {
                    $summary = app(ExamGradebookExportService::class)->pushToCampusGradebook($this->record);

                    $body = FilamentUi::text('Success').': '.($summary['success'])
                        .' · '.FilamentUi::text('Skipped').': '.($summary['skipped'])
                        .' · '.FilamentUi::text('Failed').': '.($summary['failed']);

                    if (($summary['integration']) === 'event_only') {
                        $body .= "\n".FilamentUi::text('Gradebook integration deferred; events dispatched.')
                            .' ('.($summary['events_dispatched']).')';
                    }

                    if (($summary['errors']) !== []) {
                        $body .= "\n".implode("\n", array_slice($summary['errors'], 0, 3));
                    }

                    Notification::make()
                        ->title(FilamentUi::text('Campus gradebook export finished.'))
                        ->body($body)
                        ->success()
                        ->send();
                }),
        ];
    }

    protected function notifyRuntimeSuccess(string $title, ?string $body = null): void
    {
        $notification = Notification::make()
            ->title($title)
            ->success();

        if ($body !== null) {
            $notification->body($body);
        }

        $notification->send();
    }

    protected function notifyRuntimeError(\Throwable $exception): void
    {
        $message = $exception instanceof ExamRuntimeException
            ? $exception->getMessage()
            : ($exception->getMessage() ?: FilamentUi::text('Runtime sync failed.'));

        Notification::make()
            ->title(FilamentUi::text('Runtime sync failed.'))
            ->body($message)
            ->danger()
            ->send();
    }
}
