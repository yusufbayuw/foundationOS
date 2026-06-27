<?php

namespace Modules\Core\Services\TenantDomain\Queries;

use Modules\Core\Services\TenantDomain\Contracts\TenantDomainQueryInterface;
use Modules\Core\Services\TenantDomain\TenantDomainRelationDefinition;
use Modules\Core\Services\TenantDomain\TenantDomainRelationType;
use Modules\Monitoring\Models\AuditLog;
use Modules\Monitoring\Models\FileUpload;

final class TenantMonitoringDomainQuery implements TenantDomainQueryInterface
{
    public function relations(): array
    {
        return [
            'auditLogs' => new TenantDomainRelationDefinition('auditLogs', AuditLog::class),
            'fileUploads' => new TenantDomainRelationDefinition('fileUploads', FileUpload::class),
            'auditableLogs' => new TenantDomainRelationDefinition(
                'auditableLogs',
                AuditLog::class,
                TenantDomainRelationType::MorphMany,
                morphName: 'auditable',
            ),
            'attachedFiles' => new TenantDomainRelationDefinition(
                'attachedFiles',
                FileUpload::class,
                TenantDomainRelationType::MorphMany,
                morphName: 'fileable',
            ),
        ];
    }
}
