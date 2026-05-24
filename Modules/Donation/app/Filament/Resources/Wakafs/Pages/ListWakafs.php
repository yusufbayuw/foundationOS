<?php

namespace Modules\Donation\Filament\Resources\Wakafs\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Donation\Filament\Resources\Wakafs\WakafResource;

class ListWakafs extends ListRecords
{
    protected static string $resource = WakafResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
