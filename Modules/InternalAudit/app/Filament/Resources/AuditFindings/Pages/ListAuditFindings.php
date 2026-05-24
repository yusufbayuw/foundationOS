<?php

namespace Modules\InternalAudit\Filament\Resources\AuditFindings\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\InternalAudit\Filament\Resources\AuditFindings\AuditFindingResource;

class ListAuditFindings extends ListRecords
{
    protected static string $resource = AuditFindingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
