<?php

namespace Modules\Consulting\Filament\Resources\EngagementInvoices\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Consulting\Filament\Resources\EngagementInvoices\EngagementInvoiceResource;

class ViewEngagementInvoice extends ViewRecord
{
    protected static string $resource = EngagementInvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
