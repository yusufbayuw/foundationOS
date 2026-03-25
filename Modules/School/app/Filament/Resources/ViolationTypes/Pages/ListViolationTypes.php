<?php

namespace Modules\School\Filament\Resources\ViolationTypes\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\School\Filament\Resources\ViolationTypes\ViolationTypeResource;

class ListViolationTypes extends ListRecords
{
    protected static string $resource = ViolationTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
