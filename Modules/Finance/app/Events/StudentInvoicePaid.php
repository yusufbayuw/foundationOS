<?php

namespace Modules\Finance\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Core\Models\User;
use Modules\Finance\Models\StudentInvoice;

/**
 * Dispatched when a student invoice becomes fully paid.
 *
 * @see EVENTS.md
 */
class StudentInvoicePaid
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public StudentInvoice $invoice,
        public ?User $actor = null,
    ) {}
}
