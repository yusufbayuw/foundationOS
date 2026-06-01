<?php

namespace Modules\Enrollment\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Core\Models\User;
use Modules\Enrollment\Models\Applicant;

/**
 * Dispatched when an applicant transitions to accepted status.
 *
 * @see EVENTS.md
 */
class ApplicantAccepted
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Applicant $applicant,
        public ?User $actor = null,
    ) {}
}
