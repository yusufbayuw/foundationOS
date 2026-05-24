<?php

namespace Modules\InternalAudit\Filament\Resources\AuditFindings\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\InternalAudit\Filament\Resources\AuditFindings\AuditFindingResource;

class ViewAuditFinding extends ViewRecord
{
    protected static string $resource = AuditFindingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
