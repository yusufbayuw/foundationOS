<?php

namespace Modules\Campus\Filament\Resources\Yudisia\Pages;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Campus\Filament\Resources\Yudisia\YudisiumResource;
use Modules\Campus\Models\Yudisium;
use Modules\Core\Support\FilamentUi;

class ViewYudisium extends ViewRecord
{
    protected static string $resource = YudisiumResource::class;

    protected function getHeaderActions(): array
    {
        /** @var Yudisium $record */
        $record = $this->getRecord();

        return [
            Action::make('downloadYudisiumPdf')
                ->label(FilamentUi::text('Download PDF'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->visible(fn (): bool => $record->isPrintable())
                ->url(fn (): string => route('campus.yudisiums.pdf', $record))
                ->openUrlInNewTab(),
            EditAction::make(),
        ];
    }
}
