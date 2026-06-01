<?php

namespace Modules\Sales\Filament\Resources\SalesOrders\Pages;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Core\Support\FilamentUi;
use Modules\Sales\Filament\Resources\SalesOrders\SalesOrderResource;
use Modules\Sales\Models\SalesOrder;

class ViewSalesOrder extends ViewRecord
{
    protected static string $resource = SalesOrderResource::class;

    protected function getHeaderActions(): array
    {
        /** @var SalesOrder $record */
        $record = $this->getRecord();

        return [
            Action::make('downloadPdf')
                ->label(FilamentUi::text('Download PDF'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->visible(fn (): bool => $record->isPrintable())
                ->url(fn (): string => route('sales.orders.pdf', $record))
                ->openUrlInNewTab(),
            EditAction::make(),
        ];
    }
}
