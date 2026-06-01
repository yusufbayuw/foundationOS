<?php

namespace Modules\Campus\Filament\Resources\Theses\Pages;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Campus\Filament\Resources\Theses\ThesisResource;
use Modules\Campus\Models\Thesis;
use Modules\Core\Support\FilamentUi;

class ViewThesis extends ViewRecord
{
    protected static string $resource = ThesisResource::class;

    protected function getHeaderActions(): array
    {
        /** @var Thesis $record */
        $record = $this->getRecord();

        return [
            Action::make('downloadThesisPdf')
                ->label(FilamentUi::text('Download PDF'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->visible(fn (): bool => $record->isPrintable())
                ->url(fn (): string => route('campus.theses.pdf', $record))
                ->openUrlInNewTab(),
            EditAction::make(),
        ];
    }
}
