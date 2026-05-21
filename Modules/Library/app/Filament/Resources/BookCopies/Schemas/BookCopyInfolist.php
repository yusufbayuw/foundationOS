<?php

namespace Modules\Library\Filament\Resources\BookCopies\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class BookCopyInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Scope')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('tenant.name')
                            ->label(FilamentUi::text('Tenant')),
                        TextEntry::make('book.title')
                            ->label(FilamentUi::text('Book')),
                    ]),

                Section::make('Identification')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('copy_number')
                            ->label(FilamentUi::field('copy_number')),
                        TextEntry::make('barcode')
                            ->label(FilamentUi::field('barcode'))
                            ->placeholder('-'),
                    ]),

                Section::make('Acquisition')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('acquisition_date')
                            ->label(FilamentUi::field('acquisition_date'))
                            ->date()
                            ->placeholder('-'),
                        TextEntry::make('acquisition_source')
                            ->label(FilamentUi::field('acquisition_source'))
                            ->placeholder('-'),
                        TextEntry::make('price')
                            ->label(FilamentUi::field('price'))
                            ->money()
                            ->placeholder('-'),
                        TextEntry::make('condition')
                            ->label(FilamentUi::field('condition'))
                            ->placeholder('-'),
                    ]),

                Section::make('Status & Location')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('status')
                            ->label(FilamentUi::field('status')),
                        TextEntry::make('location_shelf')
                            ->label(FilamentUi::field('location_shelf'))
                            ->placeholder('-'),
                        TextEntry::make('notes')
                            ->label(FilamentUi::field('notes'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),

                Section::make('Timestamps')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('created_at')
                            ->label(FilamentUi::field('created_at'))
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('updated_at')
                            ->label(FilamentUi::field('updated_at'))
                            ->dateTime()
                            ->placeholder('-'),
                    ]),
            ]);
    }
}
