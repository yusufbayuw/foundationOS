<?php

namespace Modules\Finance\Filament\Resources\TuitionTypes\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Finance\Filament\Resources\TuitionTypes\TuitionTypeResource;

class ViewTuitionType extends ViewRecord
{
    protected static string $resource = TuitionTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
