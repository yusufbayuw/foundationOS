<?php

namespace Modules\Procurement\Filament\Resources\PurchaseRequisitions\Pages;

use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Throwable;
use Modules\Core\Models\User;
use Modules\Procurement\Filament\Resources\PurchaseRequisitions\PurchaseRequisitionResource;
use Modules\Workflow\Contracts\WorkflowInstanceStarter;
use Modules\Workflow\Contracts\WorkflowResolver;
use Modules\Workflow\Models\WorkflowInstance;

class ViewPurchaseRequisition extends ViewRecord
{
    protected static string $resource = PurchaseRequisitionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('openWorkflow')
                ->label('Open Active Workflow')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->color('gray')
                ->visible(fn (): bool => WorkflowInstance::query()
                    ->where('subject_type', $this->record::class)
                    ->where('subject_id', $this->record->getKey())
                    ->whereIn('status', ['running'])
                    ->exists())
                ->url(function (): string {
                    $instance = WorkflowInstance::query()
                        ->where('subject_type', $this->record::class)
                        ->where('subject_id', $this->record->getKey())
                        ->whereIn('status', ['running'])
                        ->latest('started_at')
                        ->firstOrFail();

                    return \Modules\Workflow\Filament\Resources\WorkflowInstances\WorkflowInstanceResource::getUrl('view', ['record' => $instance]);
                }),
            Action::make('startWorkflow')
                ->label('Start Approval Workflow')
                ->icon('heroicon-o-play')
                ->color('primary')
                ->visible(fn (): bool => ! WorkflowInstance::query()
                    ->where('subject_type', $this->record::class)
                    ->where('subject_id', $this->record->getKey())
                    ->whereIn('status', ['running'])
                    ->exists())
                ->action(function (): void {
                    try {
                        /** @var User $user */
                        $user = auth()->user();
                        $tenant = Filament::getTenant();

                        $workflow = app(WorkflowResolver::class)->resolveForSubject(
                            $this->record->workflowSubjectType(),
                            $this->record,
                            (int) ($tenant?->getKey() ?? $this->record->tenant_id),
                            data_get($this->record, 'organization_id'),
                        );

                        $instance = app(WorkflowInstanceStarter::class)->start(
                            $workflow,
                            $user,
                            $this->record->workflowContext(),
                            $this->record,
                            $user,
                        );

                        Notification::make()
                            ->title('Approval workflow started.')
                            ->success()
                            ->send();

                        $this->redirect(\Modules\Workflow\Filament\Resources\WorkflowInstances\WorkflowInstanceResource::getUrl('view', ['record' => $instance]));
                    } catch (Throwable $exception) {
                        report($exception);

                        Notification::make()
                            ->title('Unable to start approval workflow.')
                            ->body($exception->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            EditAction::make(),
        ];
    }
}
