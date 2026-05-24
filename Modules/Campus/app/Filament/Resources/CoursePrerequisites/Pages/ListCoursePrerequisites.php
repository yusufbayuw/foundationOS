<?php

namespace Modules\Campus\Filament\Resources\CoursePrerequisites\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Campus\Filament\Resources\CoursePrerequisites\CoursePrerequisiteResource;

class ListCoursePrerequisites extends ListRecords
{
    protected static string $resource = CoursePrerequisiteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
