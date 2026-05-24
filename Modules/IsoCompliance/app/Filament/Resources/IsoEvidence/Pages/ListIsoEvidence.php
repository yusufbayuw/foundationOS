<?php

namespace Modules\IsoCompliance\Filament\Resources\IsoEvidence\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\IsoCompliance\Filament\Resources\IsoEvidence\IsoEvidenceResource;

class ListIsoEvidence extends ListRecords
{
    protected static string $resource = IsoEvidenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
