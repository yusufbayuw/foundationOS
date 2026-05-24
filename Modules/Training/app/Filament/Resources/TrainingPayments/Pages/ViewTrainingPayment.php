<?php

namespace Modules\Training\Filament\Resources\TrainingPayments\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Training\Filament\Resources\TrainingPayments\TrainingPaymentResource;

class ViewTrainingPayment extends ViewRecord
{
    protected static string $resource = TrainingPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
