<?php

namespace Modules\Procurement\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Core\Models\User;
use Modules\Procurement\Models\PurchaseRequisition;

/**
 * Dispatched after a purchase requisition transitions to the `approved`
 * status via its workflow. The auto-RFQ pipeline listens to this.
 */
class PurchaseRequisitionApproved
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public PurchaseRequisition $requisition,
        public ?User $approver = null,
    ) {}
}
