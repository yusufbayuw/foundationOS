<?php

namespace Modules\Employee\Filament\Resources\EmploymentContracts\Pages;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Core\Support\FilamentUi;
use Modules\Employee\Filament\Resources\EmploymentContracts\EmploymentContractResource;
use Modules\Employee\Models\EmploymentContract;

class ViewEmploymentContract extends ViewRecord
{
    protected static string $resource = EmploymentContractResource::class;

    protected function getHeaderActions(): array
    {
        /** @var EmploymentContract $record */
        $record = $this->getRecord();

        return [
            Action::make('downloadPdf')
                ->label(FilamentUi::text('Download PDF'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->visible(fn (): bool => $record->isPrintable())
                ->url(fn (): string => route('employee.employment-contracts.pdf', $record))
                ->openUrlInNewTab(),
            EditAction::make(),
        ];
    }
}
