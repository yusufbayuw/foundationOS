<?php

namespace Modules\Cms\Filament\Resources\Testimonials\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Cms\Filament\Resources\Testimonials\TestimonialResource;

class CreateTestimonial extends CreateRecord
{
    protected static string $resource = TestimonialResource::class;
}
