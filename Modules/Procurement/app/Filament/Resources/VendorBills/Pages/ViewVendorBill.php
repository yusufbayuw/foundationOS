<?php

namespace Modules\Procurement\Filament\Resources\VendorBills\Pages;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Core\Support\FilamentUi;
use Modules\Procurement\Filament\Resources\VendorBills\VendorBillResource;
use Modules\Procurement\Models\VendorBill;

class ViewVendorBill extends ViewRecord
{
    protected static string $resource = VendorBillResource::class;

    protected function getHeaderActions(): array
    {
        /** @var VendorBill $record */
        $record = $this->getRecord();

        return [
            Action::make('downloadPdf')
                ->label(FilamentUi::text('Download PDF'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->visible(fn (): bool => $record->isPrintable())
                ->url(fn (): string => route('procurement.vendor-bills.pdf', $record))
                ->openUrlInNewTab(),
            EditAction::make(),
        ];
    }
}
