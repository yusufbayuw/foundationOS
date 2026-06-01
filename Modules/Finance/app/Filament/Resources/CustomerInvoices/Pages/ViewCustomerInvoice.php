<?php

namespace Modules\Finance\Filament\Resources\CustomerInvoices\Pages;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Core\Support\FilamentUi;
use Modules\Finance\Filament\Resources\CustomerInvoices\CustomerInvoiceResource;
use Modules\Finance\Models\CustomerInvoice;

class ViewCustomerInvoice extends ViewRecord
{
    protected static string $resource = CustomerInvoiceResource::class;

    protected function getHeaderActions(): array
    {
        /** @var CustomerInvoice $record */
        $record = $this->getRecord();

        return [
            Action::make('downloadPdf')
                ->label(FilamentUi::text('Download PDF'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->visible(fn (): bool => $record->isPrintable())
                ->url(fn (): string => route('finance.customer-invoices.pdf', $record))
                ->openUrlInNewTab(),
            EditAction::make(),
        ];
    }
}
