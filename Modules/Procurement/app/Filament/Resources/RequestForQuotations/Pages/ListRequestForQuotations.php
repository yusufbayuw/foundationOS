<?php

namespace Modules\Procurement\Filament\Resources\RequestForQuotations\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Procurement\Filament\Resources\RequestForQuotations\RequestForQuotationResource;

class ListRequestForQuotations extends ListRecords
{
    protected static string $resource = RequestForQuotationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
