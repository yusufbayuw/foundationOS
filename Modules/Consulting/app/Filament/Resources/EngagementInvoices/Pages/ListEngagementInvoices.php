<?php

namespace Modules\Consulting\Filament\Resources\EngagementInvoices\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Consulting\Filament\Resources\EngagementInvoices\EngagementInvoiceResource;

class ListEngagementInvoices extends ListRecords
{
    protected static string $resource = EngagementInvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
