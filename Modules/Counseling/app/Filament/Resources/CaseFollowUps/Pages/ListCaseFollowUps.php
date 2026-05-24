<?php

namespace Modules\Counseling\Filament\Resources\CaseFollowUps\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Counseling\Filament\Resources\CaseFollowUps\CaseFollowUpResource;

class ListCaseFollowUps extends ListRecords
{
    protected static string $resource = CaseFollowUpResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
