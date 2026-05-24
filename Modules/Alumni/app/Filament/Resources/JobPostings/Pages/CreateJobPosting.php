<?php

namespace Modules\Alumni\Filament\Resources\JobPostings\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Alumni\Filament\Resources\JobPostings\JobPostingResource;

class CreateJobPosting extends CreateRecord
{
    protected static string $resource = JobPostingResource::class;
}
