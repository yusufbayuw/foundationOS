<?php

namespace Modules\Helpdesk\Exceptions;

use RuntimeException;

class DuplicateTicketCategoryCodeException extends RuntimeException
{
    public function __construct(string $code)
    {
        parent::__construct("Ticket category code [{$code}] already exists for this tenant scope.");
    }
}
