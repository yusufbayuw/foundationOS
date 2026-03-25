<?php

namespace Modules\Finance\Filament\Resources\TuitionTypes\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Finance\Filament\Resources\TuitionTypes\TuitionTypeResource;

class ListTuitionTypes extends ListRecords
{
    protected static string $resource = TuitionTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
