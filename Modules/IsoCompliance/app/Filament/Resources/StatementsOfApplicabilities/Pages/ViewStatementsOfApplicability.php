<?php

namespace Modules\IsoCompliance\Filament\Resources\StatementsOfApplicabilities\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\IsoCompliance\Filament\Resources\StatementsOfApplicabilities\StatementsOfApplicabilityResource;

class ViewStatementsOfApplicability extends ViewRecord
{
    protected static string $resource = StatementsOfApplicabilityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
