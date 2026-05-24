<?php

namespace Modules\Core\Filament\Resources\PollResponses\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PollResponseInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('poll.id')
                    ->label('Poll'),
                TextEntry::make('user.name')
                    ->label('User')
                    ->placeholder('-'),
                TextEntry::make('selected_option'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
