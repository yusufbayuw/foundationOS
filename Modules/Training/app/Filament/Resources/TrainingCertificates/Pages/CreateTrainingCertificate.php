<?php

namespace Modules\Training\Filament\Resources\TrainingCertificates\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Training\Filament\Resources\TrainingCertificates\TrainingCertificateResource;

class CreateTrainingCertificate extends CreateRecord
{
    protected static string $resource = TrainingCertificateResource::class;
}
