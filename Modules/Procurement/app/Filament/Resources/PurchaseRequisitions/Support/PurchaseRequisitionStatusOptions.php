<?php

namespace Modules\Procurement\Filament\Resources\PurchaseRequisitions\Support;

class PurchaseRequisitionStatusOptions
{
    /**
     * @return array<string, string>
     */
    public static function filterLabels(): array
    {
        return [
            'draft' => 'Draft',
            'submitted' => 'Submitted',
            'in_review' => 'In Review',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'cancelled' => 'Cancelled',
        ];
    }
}
