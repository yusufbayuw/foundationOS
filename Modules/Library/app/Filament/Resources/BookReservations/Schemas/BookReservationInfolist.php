<?php

namespace Modules\Library\Filament\Resources\BookReservations\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class BookReservationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(FilamentUi::text('Scope'))
                ->columns(2)
                ->schema([
                    TextEntry::make('tenant.name'),
                    TextEntry::make('organization.name'),
                ]),

            Section::make(FilamentUi::text('Reservation'))
                ->columns(2)
                ->schema([
                    TextEntry::make('book.title'),
                    TextEntry::make('member.member_number'),
                    TextEntry::make('queue_position'),
                    TextEntry::make('status'),
                ]),

            Section::make(FilamentUi::text('Timeline'))
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
