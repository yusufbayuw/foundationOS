<?php

namespace Modules\Campus\Filament\Resources\LecturerEvaluations\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class LecturerEvaluationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->required(),
                Select::make('lecturer_id')
                    ->relationship('lecturer', 'id')
                    ->required(),
                Select::make('course_offering_id')
                    ->relationship('courseOffering', 'id'),
                TextInput::make('score')
                    ->numeric(),
                Textarea::make('feedback')
                    ->columnSpanFull(),
            ]);
    }
}
