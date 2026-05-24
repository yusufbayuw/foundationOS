<?php

namespace Modules\Counseling\Filament\Resources\CounselingSessions\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Counseling\Filament\Resources\CounselingSessions\CounselingSessionResource;

class ListCounselingSessions extends ListRecords
{
    protected static string $resource = CounselingSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
