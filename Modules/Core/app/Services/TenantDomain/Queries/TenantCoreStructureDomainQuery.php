<?php

namespace Modules\Core\Services\TenantDomain\Queries;

use Modules\Core\Models\AcademicPeriod;
use Modules\Core\Models\AcademicYear;
use Modules\Core\Models\Department;
use Modules\Core\Services\TenantDomain\Contracts\TenantDomainQueryInterface;
use Modules\Core\Services\TenantDomain\TenantDomainRelationDefinition;

final class TenantCoreStructureDomainQuery implements TenantDomainQueryInterface
{
    public function relations(): array
    {
        return [
            'academicYears' => new TenantDomainRelationDefinition('academicYears', AcademicYear::class),
            'departments' => new TenantDomainRelationDefinition('departments', Department::class),
            'academicPeriods' => new TenantDomainRelationDefinition('academicPeriods', AcademicPeriod::class),
        ];
    }
}
