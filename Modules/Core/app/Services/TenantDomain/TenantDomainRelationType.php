<?php

namespace Modules\Core\Services\TenantDomain;

enum TenantDomainRelationType: string
{
    case HasMany = 'hasMany';
    case MorphMany = 'morphMany';
}
