<?php

namespace Modules\Legal\Filament\Resources\Contracts\Pages;

use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Modules\Core\Support\FilamentUi;
use Modules\Legal\Filament\Resources\Contracts\ContractResource;
use Modules\Legal\Models\Contract;

class ViewContract extends ViewRecord
{
    protected static string $resource = ContractResource::class;

    protected function getHeaderActions(): array
    {
        /** @var Contract $record */
        $record = $this->getRecord();

        return [
            Action::make('downloadPdf')
                ->label(FilamentUi::text('Download PDF'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->visible(fn (): bool => $record->isPrintable())
                ->url(fn (): string => route('legal.contracts.pdf', $record))
                ->openUrlInNewTab(),
        ];
    }
}
