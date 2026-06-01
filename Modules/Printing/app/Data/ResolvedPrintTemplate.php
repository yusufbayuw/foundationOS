<?php

namespace Modules\Printing\Data;

class ResolvedPrintTemplate
{
    public function __construct(
        public readonly string $code,
        public readonly string $view,
        public readonly string $paper,
        public readonly string $orientation,
    ) {}
}
