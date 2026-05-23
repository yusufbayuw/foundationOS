<?php

namespace Modules\InternalAudit\Filament\Resources\AuditEngagements\Pages;

use Filament\Resources\Pages\ListRecords;
use Modules\InternalAudit\Filament\Resources\AuditEngagements\AuditEngagementResource;

class ListAuditEngagements extends ListRecords
{
    protected static string $resource = AuditEngagementResource::class;
}
