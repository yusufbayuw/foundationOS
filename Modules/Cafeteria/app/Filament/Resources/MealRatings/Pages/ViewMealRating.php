<?php

namespace Modules\Cafeteria\Filament\Resources\MealRatings\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Cafeteria\Filament\Resources\MealRatings\MealRatingResource;

class ViewMealRating extends ViewRecord
{
    protected static string $resource = MealRatingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
