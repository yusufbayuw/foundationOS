<?php

namespace Modules\IsoCompliance\Filament\Resources\StatementsOfApplicabilities\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\IsoCompliance\Filament\Resources\StatementsOfApplicabilities\StatementsOfApplicabilityResource;

class ListStatementsOfApplicabilities extends ListRecords
{
    protected static string $resource = StatementsOfApplicabilityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
