<?php

namespace Modules\Workflow\Exceptions;

use RuntimeException;

class WorkflowEvidenceRequiredException extends RuntimeException
{
    public function __construct(
        public readonly string $stepName,
        public readonly int $required,
        public readonly int $uploaded,
    ) {
        parent::__construct(
            "Step \"{$stepName}\" requires {$required} evidence file(s) before advancing, but only {$uploaded} uploaded."
        );
    }
}
