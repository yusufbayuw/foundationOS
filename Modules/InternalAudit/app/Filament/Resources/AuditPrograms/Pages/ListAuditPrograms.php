<?php

namespace Modules\InternalAudit\Filament\Resources\AuditPrograms\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\InternalAudit\Filament\Resources\AuditPrograms\AuditProgramResource;

class ListAuditPrograms extends ListRecords
{
    protected static string $resource = AuditProgramResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
