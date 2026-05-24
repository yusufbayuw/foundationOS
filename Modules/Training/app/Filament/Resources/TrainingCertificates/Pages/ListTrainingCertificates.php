<?php

namespace Modules\Training\Filament\Resources\TrainingCertificates\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Training\Filament\Resources\TrainingCertificates\TrainingCertificateResource;

class ListTrainingCertificates extends ListRecords
{
    protected static string $resource = TrainingCertificateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
