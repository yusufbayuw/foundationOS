<?php

namespace Modules\InternalAudit\Filament\Resources\FindingEvidence\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\InternalAudit\Filament\Resources\FindingEvidence\FindingEvidenceResource;

class ViewFindingEvidence extends ViewRecord
{
    protected static string $resource = FindingEvidenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
