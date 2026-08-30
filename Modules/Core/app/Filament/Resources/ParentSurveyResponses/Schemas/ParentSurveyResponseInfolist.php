<?php

namespace Modules\Core\Filament\Resources\ParentSurveyResponses\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class ParentSurveyResponseInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('parent_survey_id')
                    ->numeric(),
                TextEntry::make('parentUser.name')
                    ->label(FilamentUi::text('Parent user')),
                TextEntry::make('answers')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
