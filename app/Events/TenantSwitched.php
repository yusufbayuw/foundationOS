<?php

namespace App\Events;

class TenantSwitched
{
    public function __construct(
        public readonly int|string $newTenantId,
        public readonly int|string|null $previousTenantId,
        public readonly int|string|null $userId,
    ) {}
}
