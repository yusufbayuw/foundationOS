<?php

namespace Modules\Library\Filament\Resources\BookReservations\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class BookReservationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('tenant.name'),
            TextEntry::make('organization.name'),
            TextEntry::make('book.title'),
            TextEntry::make('member.member_number'),
            TextEntry::make('queue_position'),
            TextEntry::make('status'),
            TextEntry::make('requested_at')->dateTime(),
            TextEntry::make('ready_at')->dateTime(),
            TextEntry::make('expires_at')->dateTime(),
            TextEntry::make('notes'),
        ]);
    }
}
