<?php

namespace Modules\InternalAudit\Filament\Resources\AuditPlans\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\InternalAudit\Filament\Resources\AuditPlans\AuditPlanResource;

class CreateAuditPlan extends CreateRecord
{
    protected static string $resource = AuditPlanResource::class;
}
