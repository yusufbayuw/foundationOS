<?php

namespace Modules\IsoCompliance\Filament\Resources\IsoEvidence\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\IsoCompliance\Filament\Resources\IsoEvidence\IsoEvidenceResource;

class EditIsoEvidence extends EditRecord
{
    protected static string $resource = IsoEvidenceResource::class;

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
