<?php

namespace Modules\IsoCompliance\Filament\Resources\StatementsOfApplicabilities\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\IsoCompliance\Filament\Resources\StatementsOfApplicabilities\StatementsOfApplicabilityResource;

class EditStatementsOfApplicability extends EditRecord
{
    protected static string $resource = StatementsOfApplicabilityResource::class;

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
