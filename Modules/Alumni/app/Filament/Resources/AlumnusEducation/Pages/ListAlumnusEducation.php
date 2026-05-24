<?php

namespace Modules\Alumni\Filament\Resources\AlumnusEducation\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Alumni\Filament\Resources\AlumnusEducation\AlumnusEducationResource;

class ListAlumnusEducation extends ListRecords
{
    protected static string $resource = AlumnusEducationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
