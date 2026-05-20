<?php

namespace Modules\Library\Filament\Resources\Books\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class BookInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Scope & Category')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('tenant.name')
                            ->label(FilamentUi::text('Tenant')),
                        TextEntry::make('organization.name')
                            ->label(FilamentUi::text('Organization')),
                        TextEntry::make('book_category_id')
                            ->label(FilamentUi::field('book_category_id'))
                            ->numeric()
                            ->placeholder('-'),
                    ]),

                Section::make('Identification')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('isbn')
                            ->label(FilamentUi::field('isbn'))
                            ->placeholder('-'),
                        TextEntry::make('isbn13')
                            ->label(FilamentUi::field('isbn13'))
                            ->placeholder('-'),
                        TextEntry::make('title')
                            ->label(FilamentUi::field('title')),
                        TextEntry::make('subtitle')
                            ->label(FilamentUi::field('subtitle'))
                            ->placeholder('-'),
                        TextEntry::make('authors')
                            ->label(FilamentUi::field('authors'))
                            ->columnSpanFull(),
                    ]),

                Section::make('Publication')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('publisher')
                            ->label(FilamentUi::field('publisher'))
                            ->placeholder('-'),
                        TextEntry::make('publication_year')
                            ->label(FilamentUi::field('publication_year'))
                            ->placeholder('-'),
                        TextEntry::make('publication_place')
                            ->label(FilamentUi::field('publication_place'))
                            ->placeholder('-'),
                        TextEntry::make('edition')
                            ->label(FilamentUi::field('edition'))
                            ->placeholder('-'),
                        TextEntry::make('volume')
                            ->label(FilamentUi::field('volume'))
                            ->placeholder('-'),
                        TextEntry::make('series')
                            ->label(FilamentUi::field('series'))
                            ->placeholder('-'),
                        TextEntry::make('language')
                            ->label(FilamentUi::field('language')),
                    ]),

                Section::make('Physical & Classification')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('pages')
                            ->label(FilamentUi::field('pages'))
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('dimensions')
                            ->label(FilamentUi::field('dimensions'))
                            ->placeholder('-'),
                        TextEntry::make('weight_grams')
                            ->label(FilamentUi::field('weight_grams'))
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('binding_type')
                            ->label(FilamentUi::field('binding_type'))
                            ->placeholder('-'),
                        TextEntry::make('classification_code')
                            ->label(FilamentUi::field('classification_code'))
                            ->placeholder('-'),
                        TextEntry::make('keywords')
                            ->label(FilamentUi::field('keywords'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('synopsis')
                            ->label(FilamentUi::field('synopsis'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),

                Section::make('Media & Acquisition')
                    ->columns(2)
                    ->schema([
                        ImageEntry::make('cover_image')
                            ->placeholder('-'),
                        TextEntry::make('preview_url')
                            ->label(FilamentUi::field('preview_url'))
                            ->placeholder('-'),
                        TextEntry::make('purchase_price')
                            ->label(FilamentUi::field('purchase_price'))
                            ->money()
                            ->placeholder('-'),
                        TextEntry::make('source')
                            ->label(FilamentUi::field('source'))
                            ->placeholder('-'),
                    ]),

                Section::make('Inventory & Status')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('total_copies')
                            ->label(FilamentUi::field('total_copies'))
                            ->numeric(),
                        TextEntry::make('available_copies')
                            ->label(FilamentUi::field('available_copies'))
                            ->numeric(),
                        TextEntry::make('location_shelf')
                            ->label(FilamentUi::field('location_shelf'))
                            ->placeholder('-'),
                        IconEntry::make('is_active')
                            ->boolean(),
                        IconEntry::make('is_reference_only')
                            ->boolean(),
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
