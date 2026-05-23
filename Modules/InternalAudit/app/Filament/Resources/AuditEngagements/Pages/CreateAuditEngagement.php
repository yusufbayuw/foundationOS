<?php

namespace Modules\InternalAudit\Filament\Resources\AuditEngagements\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\InternalAudit\Filament\Resources\AuditEngagements\AuditEngagementResource;

class CreateAuditEngagement extends CreateRecord
{
    protected static string $resource = AuditEngagementResource::class;
}
