<?php

namespace Modules\Training\Filament\Resources\TrainingPayments\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Training\Filament\Resources\TrainingPayments\TrainingPaymentResource;

class ListTrainingPayments extends ListRecords
{
    protected static string $resource = TrainingPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
