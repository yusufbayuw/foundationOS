<?php

namespace Modules\Core\Filament\Resources\PollResponses\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class PollResponseInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('poll.id')
                    ->label(FilamentUi::text('Poll')),
                TextEntry::make('user.name')
                    ->label(FilamentUi::text('User'))
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
