<?php

namespace Modules\InternalAudit\Filament\Resources\AuditPlans\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\InternalAudit\Filament\Resources\AuditPlans\AuditPlanResource;

class ListAuditPlans extends ListRecords
{
    protected static string $resource = AuditPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
