<?php

namespace Modules\Procurement\Filament\Resources\RequestForQuotations\Pages;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Core\Support\FilamentUi;
use Modules\Procurement\Filament\Resources\RequestForQuotations\RequestForQuotationResource;
use Modules\Procurement\Models\RequestForQuotation;

class ViewRequestForQuotation extends ViewRecord
{
    protected static string $resource = RequestForQuotationResource::class;

    protected function getHeaderActions(): array
    {
        /** @var RequestForQuotation $record */
        $record = $this->getRecord();

        return [
            Action::make('downloadPdf')
                ->label(FilamentUi::text('Download PDF'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->visible(fn (): bool => $record->isPrintable())
                ->url(fn (): string => route('procurement.rfqs.pdf', $record))
                ->openUrlInNewTab(),
            EditAction::make(),
        ];
    }
}
