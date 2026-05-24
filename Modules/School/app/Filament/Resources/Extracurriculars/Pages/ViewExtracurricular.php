<?php

namespace Modules\School\Filament\Resources\Extracurriculars\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\School\Filament\Resources\Extracurriculars\ExtracurricularResource;

class ViewExtracurricular extends ViewRecord
{
    protected static string $resource = ExtracurricularResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
