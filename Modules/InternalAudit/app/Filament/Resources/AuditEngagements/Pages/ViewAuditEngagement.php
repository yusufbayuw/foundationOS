<?php

namespace Modules\InternalAudit\Filament\Resources\AuditEngagements\Pages;

use Filament\Resources\Pages\ViewRecord;
use Modules\InternalAudit\Filament\Resources\AuditEngagements\AuditEngagementResource;

class ViewAuditEngagement extends ViewRecord
{
    protected static string $resource = AuditEngagementResource::class;
}
