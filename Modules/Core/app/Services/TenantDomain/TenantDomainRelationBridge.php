<?php

namespace Modules\Core\Services\TenantDomain;

use Illuminate\Database\Eloquent\Relations\Relation;
use Modules\Core\Models\Tenant;

/**
 * Backward-compatible bridge that exposes delegated tenant domain relations.
 *
 * @deprecated Prefer TenantDomainAggregateService::query() for new code.
 */
class TenantDomainRelationBridge
{
    public function __construct(
        private readonly TenantDomainAggregateService $aggregate,
    ) {}

    /**
     * @return list<string>
     */
    public function relationNames(): array
    {
        return $this->aggregate->relationNames();
    }

    public function hasRelation(string $name): bool
    {
        return $this->aggregate->hasRelation($name);
    }

    public function buildRelation(Tenant $tenant, string $name): Relation
    {
        return $this->aggregate->buildRelation($tenant, $name);
    }
}
