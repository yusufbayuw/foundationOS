<?php

namespace Modules\Campus\Filament\Resources\CoursePrerequisites\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Campus\Filament\Resources\CoursePrerequisites\CoursePrerequisiteResource;

class EditCoursePrerequisite extends EditRecord
{
    protected static string $resource = CoursePrerequisiteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
