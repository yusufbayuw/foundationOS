<?php

namespace Modules\Procurement\Listeners;

use App\Concerns\InteractsWithTenant;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Modules\Core\Models\User;
use Modules\Procurement\Events\PurchaseRequisitionApproved;
use Modules\Procurement\Models\RequestForQuotation;
use Modules\Procurement\Services\RfqAutoCreationService;

class CreateRfqFromApprovedPurchaseRequisition implements ShouldQueue
{
    use InteractsWithTenant, Queueable;

    public function handle(PurchaseRequisitionApproved $event): void
    {
        $requisition = $event->requisition->fresh(['items']);

        if (! $requisition) {
            return;
        }

        $rfq = app(RfqAutoCreationService::class)->createDraftFor($requisition);

        if (! $rfq) {
            return;
        }

        $this->notifyRecipient($event->approver ?? $requisition->approver ?? $requisition->requester, $rfq);
    }

    protected function notifyRecipient(?User $user, RequestForQuotation $rfq): void
    {
        if (! $user) {
            return;
        }

        try {
            Notification::make()
                ->title('RFQ otomatis dibuat')
                ->body(sprintf('RFQ %s telah dibuat dari PR %s.', $rfq->rfq_number, $rfq->purchaseRequisition?->request_number))
                ->success()
                ->sendToDatabase($user);
        } catch (\Throwable) {
            // Notifications are best-effort; do not fail the listener.
        }
    }
}
