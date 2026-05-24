<?php

namespace Modules\Cafeteria\Filament\Resources\CafeteriaTenants\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Cafeteria\Filament\Resources\CafeteriaTenants\CafeteriaTenantResource;

class ListCafeteriaTenants extends ListRecords
{
    protected static string $resource = CafeteriaTenantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
