<?php

namespace Modules\School\Filament\Resources\ExtracurricularEnrollments\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\School\Filament\Resources\ExtracurricularEnrollments\ExtracurricularEnrollmentResource;

class ListExtracurricularEnrollments extends ListRecords
{
    protected static string $resource = ExtracurricularEnrollmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
