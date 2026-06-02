<?php

namespace Modules\Donation\Filament\Resources\Donors\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;
use Modules\Donation\Models\Donor;

class DonorInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->label(FilamentUi::field('name')),
                TextEntry::make('email')
                    ->label(FilamentUi::field('email'))
                    ->placeholder('-'),
                TextEntry::make('phone')
                    ->label(FilamentUi::field('phone'))
                    ->placeholder('-'),
                IconEntry::make('is_anonymous')
                    ->label(FilamentUi::field('is_anonymous'))
                    ->boolean(),
                TextEntry::make('tags')
                    ->label(FilamentUi::field('tags'))
                    ->badge()
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->label(FilamentUi::field('created_at'))
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->label(FilamentUi::field('updated_at'))
                    ->dateTime(),
                TextEntry::make('deleted_at')
                    ->label(FilamentUi::field('deleted_at'))
                    ->dateTime()
                    ->visible(fn (Donor $record): bool => $record->trashed()),
            ]);
    }
}
