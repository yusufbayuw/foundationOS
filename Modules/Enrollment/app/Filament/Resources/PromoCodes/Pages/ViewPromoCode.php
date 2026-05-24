<?php

namespace Modules\Enrollment\Filament\Resources\PromoCodes\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Enrollment\Filament\Resources\PromoCodes\PromoCodeResource;

class ViewPromoCode extends ViewRecord
{
    protected static string $resource = PromoCodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
