<?php

namespace Modules\EOffice\Filament\Resources\Letters\Pages;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Core\Support\FilamentUi;
use Modules\EOffice\Filament\Resources\Letters\LetterResource;
use Modules\EOffice\Models\Letter;

class ViewLetter extends ViewRecord
{
    protected static string $resource = LetterResource::class;

    protected function getHeaderActions(): array
    {
        /** @var Letter $record */
        $record = $this->getRecord();

        return [
            Action::make('downloadPdf')
                ->label(FilamentUi::text('Download PDF'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->visible(fn (): bool => $record->isPrintable())
                ->url(fn (): string => route('eoffice.letters.pdf', $record))
                ->openUrlInNewTab(),
            EditAction::make(),
        ];
    }
}
