<?php

namespace Modules\InternalAudit\Filament\Resources\FindingEvidence\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\InternalAudit\Filament\Resources\FindingEvidence\FindingEvidenceResource;

class ListFindingEvidence extends ListRecords
{
    protected static string $resource = FindingEvidenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
