<?php

namespace Modules\Core\Services\TenantDomain;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use InvalidArgumentException;
use Modules\Core\Models\Tenant;
use Modules\Core\Services\TenantDomain\Contracts\TenantDomainQueryInterface;
use Modules\Core\Services\TenantDomain\Queries\TenantCampusDomainQuery;
use Modules\Core\Services\TenantDomain\Queries\TenantCoreStructureDomainQuery;
use Modules\Core\Services\TenantDomain\Queries\TenantEmployeeDomainQuery;
use Modules\Core\Services\TenantDomain\Queries\TenantEnrollmentDomainQuery;
use Modules\Core\Services\TenantDomain\Queries\TenantFinanceDomainQuery;
use Modules\Core\Services\TenantDomain\Queries\TenantLibraryDomainQuery;
use Modules\Core\Services\TenantDomain\Queries\TenantMonitoringDomainQuery;
use Modules\Core\Services\TenantDomain\Queries\TenantProcurementDomainQuery;
use Modules\Core\Services\TenantDomain\Queries\TenantSchoolDomainQuery;

class TenantDomainAggregateService
{
    /** @var array<string, TenantDomainRelationDefinition>|null */
    private ?array $definitions = null;

    /**
     * @param  list<TenantDomainQueryInterface>  $queries
     */
    public function __construct(
        private readonly array $queries = [
            new TenantCoreStructureDomainQuery,
            new TenantSchoolDomainQuery,
            new TenantCampusDomainQuery,
            new TenantEnrollmentDomainQuery,
            new TenantFinanceDomainQuery,
            new TenantLibraryDomainQuery,
            new TenantEmployeeDomainQuery,
            new TenantProcurementDomainQuery,
            new TenantMonitoringDomainQuery,
        ],
    ) {}

    /**
     * @return array<string, TenantDomainRelationDefinition>
     */
    public function definitions(): array
    {
        if ($this->definitions !== null) {
            return $this->definitions;
        }

        $definitions = [];

        foreach ($this->queries as $query) {
            foreach ($query->relations() as $name => $definition) {
                $definitions[$name] = $definition;
            }
        }

        return $this->definitions = $definitions;
    }

    /**
     * @return list<string>
     */
    public function relationNames(): array
    {
        return array_keys($this->definitions());
    }

    public function hasRelation(string $name): bool
    {
        return array_key_exists($name, $this->definitions());
    }

    public function definition(string $name): TenantDomainRelationDefinition
    {
        return $this->definitions()[$name]
            ?? throw new InvalidArgumentException("Unknown tenant domain relation [{$name}].");
    }

    public function query(Tenant $tenant, string $relationName): Builder
    {
        $definition = $this->definition($relationName);

        if ($definition->type === TenantDomainRelationType::MorphMany) {
            throw new InvalidArgumentException("Relation [{$relationName}] is morphMany and cannot be queried by tenant_id alone.");
        }

        $foreignKey = $definition->foreignKey ?? 'tenant_id';

        return $definition->model::query()->where($foreignKey, $tenant->getKey());
    }

    public function buildRelation(Tenant $tenant, string $name): Relation
    {
        $definition = $this->definition($name);

        return match ($definition->type) {
            TenantDomainRelationType::HasMany => $tenant->hasMany(
                $definition->model,
                $definition->foreignKey ?? 'tenant_id',
            ),
            TenantDomainRelationType::MorphMany => $tenant->morphMany(
                $definition->model,
                $definition->morphName ?? throw new InvalidArgumentException("Morph relation [{$name}] is missing morph name."),
            ),
        };
    }
}
