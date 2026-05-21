<?php

namespace Modules\Procurement\Exceptions;

use RuntimeException;

class ThreeWayMatchException extends RuntimeException
{
    /** @var array<int, string> */
    public array $errors;

    /**
     * @param  array<int, string>  $errors
     */
    public function __construct(array $errors)
    {
        $this->errors = $errors;
        parent::__construct('Three-way match failed: '.implode(' ', $errors));
    }
}
