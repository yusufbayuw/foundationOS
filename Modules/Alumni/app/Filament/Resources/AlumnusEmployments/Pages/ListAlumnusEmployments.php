<?php

namespace Modules\Alumni\Filament\Resources\AlumnusEmployments\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Alumni\Filament\Resources\AlumnusEmployments\AlumnusEmploymentResource;

class ListAlumnusEmployments extends ListRecords
{
    protected static string $resource = AlumnusEmploymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
