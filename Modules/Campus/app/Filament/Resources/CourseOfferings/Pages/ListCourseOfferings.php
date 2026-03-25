<?php

namespace Modules\Campus\Filament\Resources\CourseOfferings\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Campus\Filament\Resources\CourseOfferings\CourseOfferingResource;

class ListCourseOfferings extends ListRecords
{
    protected static string $resource = CourseOfferingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
