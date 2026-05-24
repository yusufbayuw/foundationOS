<?php

namespace Modules\Core\Filament\Resources\Stakeholders\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Core\Filament\Resources\Stakeholders\StakeholderResource;

class ListStakeholders extends ListRecords
{
    protected static string $resource = StakeholderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
