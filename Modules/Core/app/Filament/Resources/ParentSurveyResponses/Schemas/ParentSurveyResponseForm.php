<?php

namespace Modules\Core\Filament\Resources\ParentSurveyResponses\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ParentSurveyResponseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('parent_survey_id')
                    ->required()
                    ->numeric(),
                Select::make('parent_user_id')
                    ->relationship('parentUser', 'name')
                    ->required(),
                Textarea::make('answers')
                    ->columnSpanFull(),
            ]);
    }
}
