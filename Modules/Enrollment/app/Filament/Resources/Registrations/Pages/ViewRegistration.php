<?php

namespace Modules\Enrollment\Filament\Resources\Registrations\Pages;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Core\Support\FilamentUi;
use Modules\Enrollment\Filament\Resources\Registrations\RegistrationResource;
use Modules\Enrollment\Models\Registration;

class ViewRegistration extends ViewRecord
{
    protected static string $resource = RegistrationResource::class;

    protected function getHeaderActions(): array
    {
        /** @var Registration $record */
        $record = $this->getRecord();

        return [
            Action::make('downloadPdf')
                ->label(FilamentUi::text('Download PDF'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->visible(fn (): bool => $record->isPrintable())
                ->url(fn (): string => route('enrollment.registrations.pdf', $record))
                ->openUrlInNewTab(),
            EditAction::make(),
        ];
    }
}
