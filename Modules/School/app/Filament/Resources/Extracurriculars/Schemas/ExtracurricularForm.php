<?php

namespace Modules\School\Filament\Resources\Extracurriculars\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class ExtracurricularForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label(FilamentUi::field('name'))
                ->required(),
            TextInput::make('code')
                ->label(FilamentUi::field('code')),
            Select::make('advisor_user_id')
                ->label(FilamentUi::text('Advisor'))
                ->relationship('advisor', 'name')
                ->searchable(),
            Textarea::make('description')
                ->label(FilamentUi::field('description'))
                ->columnSpanFull(),
        ]);
    }
}
