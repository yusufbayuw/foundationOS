<?php

namespace Modules\Campus\Filament\Resources\MbkmActivities\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MbkmActivityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->required(),
                Select::make('collage_student_id')
                    ->relationship('collageStudent', 'id'),
                TextInput::make('activity_type')
                    ->required(),
                TextInput::make('title')
                    ->required(),
                TextInput::make('sks_credited')
                    ->required()
                    ->numeric()
                    ->default(0),
                DatePicker::make('start_date'),
                DatePicker::make('end_date'),
                TextInput::make('status')
                    ->required()
                    ->default('active'),
            ]);
    }
}
