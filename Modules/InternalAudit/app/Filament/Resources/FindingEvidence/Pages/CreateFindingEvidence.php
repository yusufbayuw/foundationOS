<?php

namespace Modules\InternalAudit\Filament\Resources\FindingEvidence\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\InternalAudit\Filament\Resources\FindingEvidence\FindingEvidenceResource;

class CreateFindingEvidence extends CreateRecord
{
    protected static string $resource = FindingEvidenceResource::class;
}
