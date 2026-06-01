<?php

namespace Modules\Library\Filament\Resources\Fines\Pages;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Core\Support\FilamentUi;
use Modules\Library\Filament\Resources\Fines\FineResource;
use Modules\Library\Models\Fine;

class ViewFine extends ViewRecord
{
    protected static string $resource = FineResource::class;

    protected function getHeaderActions(): array
    {
        /** @var Fine $record */
        $record = $this->getRecord();

        return [
            Action::make('downloadPdf')
                ->label(FilamentUi::text('Download PDF'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->visible(fn (): bool => $record->isPrintable())
                ->url(fn (): string => route('library.fines.pdf', $record))
                ->openUrlInNewTab(),
            EditAction::make(),
        ];
    }
}
