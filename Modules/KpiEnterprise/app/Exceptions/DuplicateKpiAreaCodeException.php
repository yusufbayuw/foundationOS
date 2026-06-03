<?php

namespace Modules\KpiEnterprise\Exceptions;

use RuntimeException;

class DuplicateKpiAreaCodeException extends RuntimeException
{
    public function __construct(string $code)
    {
        parent::__construct("KPI area code [{$code}] already exists for this tenant scope.");
    }
}
