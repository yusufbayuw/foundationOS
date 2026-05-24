<?php

namespace Modules\Property\Filament\Resources\LeaseInvoices\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Property\Filament\Resources\LeaseInvoices\LeaseInvoiceResource;

class ViewLeaseInvoice extends ViewRecord
{
    protected static string $resource = LeaseInvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
