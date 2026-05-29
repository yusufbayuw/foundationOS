<?php

namespace Modules\Exam\Exceptions;

use Exception;

class ExamRuntimeException extends Exception
{
    public function __construct(
        string $message,
        public readonly ?int $httpStatus = null,
        public readonly ?array $responseBody = null,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, 0, $previous);
    }
}
