<?php

namespace Modules\Library\Filament\Resources\BookReservations\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BookReservationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Scope')
                ->columns(2)
                ->schema([
                    TextEntry::make('tenant.name'),
                    TextEntry::make('organization.name'),
                ]),

            Section::make('Reservation')
                ->columns(2)
                ->schema([
                    TextEntry::make('book.title'),
                    TextEntry::make('member.member_number'),
                    TextEntry::make('queue_position'),
                    TextEntry::make('status'),
                ]),

            Section::make('Timeline')
                ->columns(2)
                ->schema([
                    TextEntry::make('requested_at')->dateTime(),
                    TextEntry::make('ready_at')->dateTime(),
                    TextEntry::make('expires_at')->dateTime(),
                    TextEntry::make('notes'),
                ]),
        ]);
    }
}
