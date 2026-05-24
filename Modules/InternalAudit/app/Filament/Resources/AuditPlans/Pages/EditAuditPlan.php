<?php

namespace Modules\InternalAudit\Filament\Resources\AuditPlans\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\InternalAudit\Filament\Resources\AuditPlans\AuditPlanResource;

class EditAuditPlan extends EditRecord
{
    protected static string $resource = AuditPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
