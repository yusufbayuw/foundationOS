<?php

namespace Modules\Library\Filament\Resources\BookCopies\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class BookCopyInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Tenant')),
                TextEntry::make('book.title')
                    ->label(\Modules\Core\Support\FilamentUi::text('Book')),
                TextEntry::make('copy_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('copy_number')),
                TextEntry::make('barcode')
                    ->label(\Modules\Core\Support\FilamentUi::field('barcode'))
                    ->placeholder('-'),
                TextEntry::make('acquisition_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('acquisition_date'))
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('acquisition_source')
                    ->label(\Modules\Core\Support\FilamentUi::field('acquisition_source'))
                    ->placeholder('-'),
                TextEntry::make('price')
                    ->label(\Modules\Core\Support\FilamentUi::field('price'))
                    ->money()
                    ->placeholder('-'),
                TextEntry::make('condition')
                    ->label(\Modules\Core\Support\FilamentUi::field('condition'))
                    ->placeholder('-'),
                TextEntry::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status')),
                TextEntry::make('location_shelf')
                    ->label(\Modules\Core\Support\FilamentUi::field('location_shelf'))
                    ->placeholder('-'),
                TextEntry::make('notes')
                    ->label(\Modules\Core\Support\FilamentUi::field('notes'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('created_at'))
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('updated_at'))
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
