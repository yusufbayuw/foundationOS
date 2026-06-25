<?php

namespace Modules\Procurement\Filament\Actions;

use Filament\Actions\Action;
use Modules\Core\Support\FilamentUi;
use Modules\Procurement\Models\PurchaseRequisition;

class DownloadPurchaseRequisitionPdfAction
{
    public static function make(PurchaseRequisition $record): Action
    {
        return Action::make('downloadPdf')
            ->label(FilamentUi::text('Download PDF'))
            ->icon('heroicon-o-arrow-down-tray')
            ->color('gray')
            ->visible(fn (): bool => $record->isPrintable())
            ->url(fn (): string => route('procurement.purchase-requisitions.pdf', $record))
            ->openUrlInNewTab();
    }
}
