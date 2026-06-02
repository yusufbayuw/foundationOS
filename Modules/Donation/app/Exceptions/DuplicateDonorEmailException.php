<?php

namespace Modules\Donation\Exceptions;

use RuntimeException;

class DuplicateDonorEmailException extends RuntimeException
{
    public function __construct(string $email)
    {
        parent::__construct("Donor email [{$email}] is already registered for this tenant.");
    }
}
