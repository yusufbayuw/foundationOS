<?php

namespace Modules\Facility\Filament\Resources\FacilityRentals\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;
use Modules\Facility\Models\FacilityRental;

class FacilityRentalInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label(FilamentUi::text('Tenant')),
                TextEntry::make('room.name')
                    ->label(FilamentUi::text('Room')),
                TextEntry::make('customer.name')
                    ->label(FilamentUi::text('Customer'))
                    ->placeholder('-'),
                TextEntry::make('rental_type'),
                TextEntry::make('starts_at')
                    ->dateTime(),
                TextEntry::make('ends_at')
                    ->dateTime(),
                TextEntry::make('total_amount')
                    ->numeric(),
                TextEntry::make('status'),
                TextEntry::make('journalEntry.id')
                    ->label(FilamentUi::text('Journal entry'))
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (FacilityRental $record): bool => $record->trashed()),
            ]);
    }
}
