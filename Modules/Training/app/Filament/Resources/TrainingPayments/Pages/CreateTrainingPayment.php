<?php

namespace Modules\Training\Filament\Resources\TrainingPayments\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Training\Filament\Resources\TrainingPayments\TrainingPaymentResource;

class CreateTrainingPayment extends CreateRecord
{
    protected static string $resource = TrainingPaymentResource::class;
}
