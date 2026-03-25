<?php

namespace Modules\Campus\Filament\Resources\CourseOfferings\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Campus\Filament\Resources\CourseOfferings\CourseOfferingResource;

class ViewCourseOffering extends ViewRecord
{
    protected static string $resource = CourseOfferingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
