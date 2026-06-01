<?php

namespace Modules\Procurement\Filament\Resources\PurchaseRequisitions\Pages;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Modules\Core\Models\User;
use Modules\Core\Support\FilamentUi;
use Modules\Procurement\Filament\Resources\PurchaseRequisitions\PurchaseRequisitionResource;
use Modules\Procurement\Models\PurchaseRequisition;
use Modules\Workflow\Contracts\WorkflowInstanceStarter;
use Modules\Workflow\Contracts\WorkflowResolver;
use Modules\Workflow\Filament\Resources\WorkflowInstances\WorkflowInstanceResource;
use Modules\Workflow\Models\WorkflowInstance;
use Throwable;

class ViewPurchaseRequisition extends ViewRecord
{
    protected static string $resource = PurchaseRequisitionResource::class;

    protected function getHeaderActions(): array
    {
        /** @var PurchaseRequisition $record */
        $record = $this->getRecord();

        return [
            Action::make('downloadPdf')
                ->label(FilamentUi::text('Download PDF'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->visible(fn (): bool => $record->isPrintable())
                ->url(fn (): string => route('procurement.purchase-requisitions.pdf', $record))
                ->openUrlInNewTab(),
            Action::make('openWorkflow')
                ->label(FilamentUi::text('Open Active Workflow'))
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

                    return WorkflowInstanceResource::getUrl('view', ['record' => $instance]);
                }),
            Action::make('startWorkflow')
                ->label(FilamentUi::text('Start Approval Workflow'))
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

                        $this->redirect(WorkflowInstanceResource::getUrl('view', ['record' => $instance]));
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
