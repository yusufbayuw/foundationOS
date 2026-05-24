<?php

namespace Modules\Donation\Filament\Resources\Endowments\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Donation\Filament\Resources\Endowments\EndowmentResource;

class ListEndowments extends ListRecords
{
    protected static string $resource = EndowmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
