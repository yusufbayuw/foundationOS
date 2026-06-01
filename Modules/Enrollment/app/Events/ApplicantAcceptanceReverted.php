<?php

namespace Modules\Enrollment\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Core\Models\User;
use Modules\Enrollment\Models\Applicant;

/**
 * Dispatched when an applicant leaves the accepted status.
 *
 * @see EVENTS.md
 */
class ApplicantAcceptanceReverted
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Applicant $applicant,
        public string $previousStatus,
        public ?User $actor = null,
    ) {}
}
