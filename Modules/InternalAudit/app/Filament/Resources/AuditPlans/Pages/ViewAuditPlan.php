<?php

namespace Modules\InternalAudit\Filament\Resources\AuditPlans\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\InternalAudit\Filament\Resources\AuditPlans\AuditPlanResource;

class ViewAuditPlan extends ViewRecord
{
    protected static string $resource = AuditPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
