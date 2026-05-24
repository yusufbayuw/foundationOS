<?php

namespace Modules\School\Filament\Resources\ExtracurricularEnrollments\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\School\Filament\Resources\ExtracurricularEnrollments\ExtracurricularEnrollmentResource;

class ViewExtracurricularEnrollment extends ViewRecord
{
    protected static string $resource = ExtracurricularEnrollmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
