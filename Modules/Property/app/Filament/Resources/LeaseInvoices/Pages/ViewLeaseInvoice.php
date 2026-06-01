<?php

namespace Modules\Property\Filament\Resources\LeaseInvoices\Pages;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Core\Support\FilamentUi;
use Modules\Property\Filament\Resources\LeaseInvoices\LeaseInvoiceResource;
use Modules\Property\Models\LeaseInvoice;

class ViewLeaseInvoice extends ViewRecord
{
    protected static string $resource = LeaseInvoiceResource::class;

    protected function getHeaderActions(): array
    {
        /** @var LeaseInvoice $record */
        $record = $this->getRecord();

        return [
            Action::make('downloadPdf')
                ->label(FilamentUi::text('Download PDF'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->visible(fn (): bool => $record->isPrintable())
                ->url(fn (): string => route('property.lease-invoices.pdf', $record))
                ->openUrlInNewTab(),
            EditAction::make(),
        ];
    }
}
