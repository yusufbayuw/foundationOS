<?php

namespace Modules\Printing\Filament\Resources\Royalties\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Printing\Filament\Resources\Royalties\RoyaltyResource;

class ListRoyalties extends ListRecords
{
    protected static string $resource = RoyaltyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
