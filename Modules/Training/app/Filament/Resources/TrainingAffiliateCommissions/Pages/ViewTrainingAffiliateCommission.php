<?php

namespace Modules\Training\Filament\Resources\TrainingAffiliateCommissions\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Training\Filament\Resources\TrainingAffiliateCommissions\TrainingAffiliateCommissionResource;

class ViewTrainingAffiliateCommission extends ViewRecord
{
    protected static string $resource = TrainingAffiliateCommissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
