<?php

namespace Modules\Training\Filament\Resources\TrainingAffiliateCommissions\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Training\Filament\Resources\TrainingAffiliateCommissions\TrainingAffiliateCommissionResource;

class ListTrainingAffiliateCommissions extends ListRecords
{
    protected static string $resource = TrainingAffiliateCommissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
