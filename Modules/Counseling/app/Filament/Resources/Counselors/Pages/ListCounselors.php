<?php

namespace Modules\Counseling\Filament\Resources\Counselors\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Counseling\Filament\Resources\Counselors\CounselorResource;

class ListCounselors extends ListRecords
{
    protected static string $resource = CounselorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
