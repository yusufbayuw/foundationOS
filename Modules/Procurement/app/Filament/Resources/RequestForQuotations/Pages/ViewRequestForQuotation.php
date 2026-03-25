<?php

namespace Modules\Procurement\Filament\Resources\RequestForQuotations\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Procurement\Filament\Resources\RequestForQuotations\RequestForQuotationResource;

class ViewRequestForQuotation extends ViewRecord
{
    protected static string $resource = RequestForQuotationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
