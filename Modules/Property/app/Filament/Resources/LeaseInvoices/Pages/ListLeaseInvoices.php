<?php

namespace Modules\Property\Filament\Resources\LeaseInvoices\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Property\Filament\Resources\LeaseInvoices\LeaseInvoiceResource;

class ListLeaseInvoices extends ListRecords
{
    protected static string $resource = LeaseInvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
