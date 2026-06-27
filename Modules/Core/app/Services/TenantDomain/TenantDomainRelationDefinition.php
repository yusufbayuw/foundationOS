<?php

namespace Modules\Core\Services\TenantDomain;

use Illuminate\Database\Eloquent\Model;

final class TenantDomainRelationDefinition
{
    /**
     * @param  class-string<Model>  $model
     */
    public function __construct(
        public readonly string $name,
        public readonly string $model,
        public readonly TenantDomainRelationType $type = TenantDomainRelationType::HasMany,
        public readonly ?string $foreignKey = null,
        public readonly ?string $morphName = null,
    ) {}
}
