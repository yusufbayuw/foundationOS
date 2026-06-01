<?php

namespace Modules\Boarding\Filament\Resources\BoardingLeavePermits\Pages;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Boarding\Filament\Resources\BoardingLeavePermits\BoardingLeavePermitResource;
use Modules\Boarding\Models\BoardingLeavePermit;
use Modules\Core\Support\FilamentUi;

class ViewBoardingLeavePermit extends ViewRecord
{
    protected static string $resource = BoardingLeavePermitResource::class;

    protected function getHeaderActions(): array
    {
        /** @var BoardingLeavePermit $record */
        $record = $this->getRecord();

        return [
            Action::make('downloadPdf')
                ->label(FilamentUi::text('Download PDF'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->visible(fn (): bool => $record->isPrintable())
                ->url(fn (): string => route('boarding.leave-permits.pdf', $record))
                ->openUrlInNewTab(),
            EditAction::make(),
        ];
    }
}
