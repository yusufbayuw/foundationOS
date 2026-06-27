<?php

namespace Modules\Core\Exceptions;

use RuntimeException;

class MissingTenantContextException extends RuntimeException
{
    public function __construct(string $modelClass)
    {
        parent::__construct(
            "Tenant context is required to query [{$modelClass}] when tenancy.scope_fail_closed is enabled.",
        );
    }
}
