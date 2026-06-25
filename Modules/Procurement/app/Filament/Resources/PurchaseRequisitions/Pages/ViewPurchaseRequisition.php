<?php

namespace Modules\Procurement\Filament\Resources\PurchaseRequisitions\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Procurement\Filament\Actions\DownloadPurchaseRequisitionPdfAction;
use Modules\Procurement\Filament\Resources\PurchaseRequisitions\PurchaseRequisitionResource;
use Modules\Procurement\Models\PurchaseRequisition;
use Modules\Workflow\Filament\Actions\OpenActiveSubjectWorkflowAction;
use Modules\Workflow\Filament\Actions\StartSubjectWorkflowAction;

class ViewPurchaseRequisition extends ViewRecord
{
    protected static string $resource = PurchaseRequisitionResource::class;

    protected function getHeaderActions(): array
    {
        /** @var PurchaseRequisition $record */
        $record = $this->getRecord();

        return [
            DownloadPurchaseRequisitionPdfAction::make($record),
            OpenActiveSubjectWorkflowAction::make($this),
            StartSubjectWorkflowAction::make(
                $this,
                'Approval workflow started.',
                'Unable to start approval workflow.',
                visibleWhen: fn (): bool => $record->status === 'draft',
            ),
            EditAction::make(),
        ];
    }
}
