<?php

namespace Modules\Library\Data;

readonly class OpacCatalogFilter
{
    public function __construct(
        public string $search,
        public ?int $categoryId,
        public string $publisher,
        public bool $availableOnly,
    ) {}
}
