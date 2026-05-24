<?php

namespace Modules\Core\Filament\Resources\PollResponses\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PollResponseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('poll_id')
                    ->relationship('poll', 'id')
                    ->required(),
                Select::make('user_id')
                    ->relationship('user', 'name'),
                TextInput::make('selected_option')
                    ->required(),
            ]);
    }
}
