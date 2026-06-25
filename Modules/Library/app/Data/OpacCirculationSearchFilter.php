<?php

namespace Modules\Library\Data;

readonly class OpacCirculationSearchFilter
{
    public function __construct(
        public string $memberQuery,
        public string $itemQuery,
        public bool $scannerMode,
    ) {}
}
