<?php

namespace Modules\Campus\Filament\Resources\LecturerEvaluations\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Campus\Filament\Resources\LecturerEvaluations\LecturerEvaluationResource;

class ListLecturerEvaluations extends ListRecords
{
    protected static string $resource = LecturerEvaluationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
