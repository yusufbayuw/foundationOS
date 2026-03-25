<?php

namespace Modules\Finance\Filament\Resources\Payments\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Finance\Filament\Resources\Payments\PaymentResource;

class CreatePayment extends CreateRecord
{
    protected static string $resource = PaymentResource::class;
}
