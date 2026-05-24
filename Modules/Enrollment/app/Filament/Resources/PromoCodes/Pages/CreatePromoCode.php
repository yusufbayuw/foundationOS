<?php

namespace Modules\Enrollment\Filament\Resources\PromoCodes\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Enrollment\Filament\Resources\PromoCodes\PromoCodeResource;

class CreatePromoCode extends CreateRecord
{
    protected static string $resource = PromoCodeResource::class;
}
