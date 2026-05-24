<?php

namespace Modules\Cafeteria\Filament\Resources\MealRatings\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Cafeteria\Filament\Resources\MealRatings\MealRatingResource;

class ListMealRatings extends ListRecords
{
    protected static string $resource = MealRatingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
