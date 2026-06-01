<?php

namespace Modules\Campus\Filament\Resources\Wisudas\Pages;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Campus\Filament\Resources\Wisudas\WisudaResource;
use Modules\Campus\Models\Wisuda;
use Modules\Core\Support\FilamentUi;

class ViewWisuda extends ViewRecord
{
    protected static string $resource = WisudaResource::class;

    protected function getHeaderActions(): array
    {
        /** @var Wisuda $record */
        $record = $this->getRecord();

        return [
            Action::make('downloadWisudaPdf')
                ->label(FilamentUi::text('Download PDF'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->visible(fn (): bool => $record->isPrintable())
                ->url(fn (): string => route('campus.wisudas.pdf', $record))
                ->openUrlInNewTab(),
            EditAction::make(),
        ];
    }
}
