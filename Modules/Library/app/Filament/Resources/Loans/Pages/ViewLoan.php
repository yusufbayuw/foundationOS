<?php

namespace Modules\Library\Filament\Resources\Loans\Pages;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Core\Support\FilamentUi;
use Modules\Library\Filament\Resources\Loans\LoanResource;
use Modules\Library\Models\Loan;

class ViewLoan extends ViewRecord
{
    protected static string $resource = LoanResource::class;

    protected function getHeaderActions(): array
    {
        /** @var Loan $record */
        $record = $this->getRecord();

        return [
            Action::make('downloadPdf')
                ->label(FilamentUi::text('Download PDF'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->visible(fn (): bool => $record->isPrintable())
                ->url(fn (): string => route('library.loans.pdf', $record))
                ->openUrlInNewTab(),
            EditAction::make(),
        ];
    }
}
