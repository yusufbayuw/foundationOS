<?php

namespace Modules\IsoCompliance\Filament\Resources\IsoEvidence\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\IsoCompliance\Filament\Resources\IsoEvidence\IsoEvidenceResource;

class ViewIsoEvidence extends ViewRecord
{
    protected static string $resource = IsoEvidenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
