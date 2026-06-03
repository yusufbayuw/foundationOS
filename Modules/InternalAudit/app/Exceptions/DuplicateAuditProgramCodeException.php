<?php

namespace Modules\InternalAudit\Exceptions;

use RuntimeException;

class DuplicateAuditProgramCodeException extends RuntimeException
{
    public function __construct(string $code)
    {
        parent::__construct("Audit program code [{$code}] already exists for this tenant scope.");
    }
}
