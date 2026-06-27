<?php

namespace Modules\Workflow\Filament\Resources\Workflows\Pages;

use App\Support\TypedValue;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Modules\Core\Support\FilamentUi;
use Modules\Workflow\Enums\WorkflowDefinitionStatus;
use Modules\Workflow\Filament\Resources\Workflows\WorkflowResource;
use Modules\Workflow\Models\Workflow;
use Modules\Workflow\Services\WorkflowDefinitionLifecycleService;

/**
 * @property Workflow $record
 */
class ViewWorkflow extends ViewRecord
{
    protected static string $resource = WorkflowResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->visible(fn (): bool => $this->record->status->value !== WorkflowDefinitionStatus::Active->value),
            Action::make('publish')
                ->label(FilamentUi::text('Publish'))
                ->icon('heroicon-o-bolt')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn (): bool => $this->record->status->value !== WorkflowDefinitionStatus::Active->value || ! $this->record->is_active)
                ->action(function (): void {
                    $this->record = app(WorkflowDefinitionLifecycleService::class)
                        ->publish($this->record, TypedValue::nullableInt(auth()->id()));

                    Notification::make()
                        ->title('Workflow berhasil dipublish.')
                        ->success()
                        ->send();
                }),
            Action::make('archive')
                ->label(FilamentUi::text('Archive'))
                ->icon('heroicon-o-archive-box')
                ->color('gray')
                ->requiresConfirmation()
                ->visible(fn (): bool => $this->record->status->value !== WorkflowDefinitionStatus::Archived->value)
                ->action(function (): void {
                    $this->record = app(WorkflowDefinitionLifecycleService::class)
                        ->archive($this->record, TypedValue::nullableInt(auth()->id()));

                    Notification::make()
                        ->title('Workflow berhasil diarsipkan.')
                        ->success()
                        ->send();
                }),
            Action::make('duplicateVersion')
                ->label(FilamentUi::text('Duplicate as New Version'))
                ->icon('heroicon-o-document-duplicate')
                ->color('warning')
                ->requiresConfirmation()
                ->action(function (): void {
                    $clone = app(WorkflowDefinitionLifecycleService::class)
                        ->duplicateAsNewVersion($this->record, TypedValue::nullableInt(auth()->id()));

                    Notification::make()
                        ->title('Versi baru workflow berhasil dibuat.')
                        ->success()
                        ->send();

                    $this->redirect(static::getResource()::getUrl('view', ['record' => $clone]));
                }),
        ];
    }
}
