<?php

namespace Modules\Campus\Filament\Resources\CoursePrerequisites\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Campus\Filament\Resources\CoursePrerequisites\CoursePrerequisiteResource;

class ViewCoursePrerequisite extends ViewRecord
{
    protected static string $resource = CoursePrerequisiteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
