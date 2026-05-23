<?php

namespace Modules\InternalAudit\Filament\Resources\AuditEngagements\Pages;

use Filament\Resources\Pages\EditRecord;
use Modules\InternalAudit\Filament\Resources\AuditEngagements\AuditEngagementResource;

class EditAuditEngagement extends EditRecord
{
    protected static string $resource = AuditEngagementResource::class;
}
