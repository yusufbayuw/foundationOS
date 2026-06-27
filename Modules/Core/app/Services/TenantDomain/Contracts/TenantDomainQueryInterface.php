<?php

namespace Modules\Core\Services\TenantDomain\Contracts;

use Modules\Core\Services\TenantDomain\TenantDomainRelationDefinition;

interface TenantDomainQueryInterface
{
    /**
     * @return array<string, TenantDomainRelationDefinition>
     */
    public function relations(): array;
}
