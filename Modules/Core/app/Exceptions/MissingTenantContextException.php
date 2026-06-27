<?php

namespace Modules\Core\Exceptions;

use RuntimeException;

class MissingTenantContextException extends RuntimeException
{
    public function __construct(string $context)
    {
        parent::__construct(
            "Tenant context is required to access [{$context}].",
        );
    }
}
