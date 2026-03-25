<?php

namespace Modules\Library\Filament\Resources\Books\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class BookInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Tenant')),
                TextEntry::make('organization.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Organization')),
                TextEntry::make('book_category_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('book_category_id'))
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('isbn')
                    ->label(\Modules\Core\Support\FilamentUi::field('isbn'))
                    ->placeholder('-'),
                TextEntry::make('isbn13')
                    ->label(\Modules\Core\Support\FilamentUi::field('isbn13'))
                    ->placeholder('-'),
                TextEntry::make('title')
                    ->label(\Modules\Core\Support\FilamentUi::field('title')),
                TextEntry::make('subtitle')
                    ->label(\Modules\Core\Support\FilamentUi::field('subtitle'))
                    ->placeholder('-'),
                TextEntry::make('authors')
                    ->label(\Modules\Core\Support\FilamentUi::field('authors'))
                    ->columnSpanFull(),
                TextEntry::make('publisher')
                    ->label(\Modules\Core\Support\FilamentUi::field('publisher'))
                    ->placeholder('-'),
                TextEntry::make('publication_year')
                    ->label(\Modules\Core\Support\FilamentUi::field('publication_year'))
                    ->placeholder('-'),
                TextEntry::make('publication_place')
                    ->label(\Modules\Core\Support\FilamentUi::field('publication_place'))
                    ->placeholder('-'),
                TextEntry::make('edition')
                    ->label(\Modules\Core\Support\FilamentUi::field('edition'))
                    ->placeholder('-'),
                TextEntry::make('volume')
                    ->label(\Modules\Core\Support\FilamentUi::field('volume'))
                    ->placeholder('-'),
                TextEntry::make('series')
                    ->label(\Modules\Core\Support\FilamentUi::field('series'))
                    ->placeholder('-'),
                TextEntry::make('language')
                    ->label(\Modules\Core\Support\FilamentUi::field('language')),
                TextEntry::make('pages')
                    ->label(\Modules\Core\Support\FilamentUi::field('pages'))
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('dimensions')
                    ->label(\Modules\Core\Support\FilamentUi::field('dimensions'))
                    ->placeholder('-'),
                TextEntry::make('weight_grams')
                    ->label(\Modules\Core\Support\FilamentUi::field('weight_grams'))
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('binding_type')
                    ->label(\Modules\Core\Support\FilamentUi::field('binding_type'))
                    ->placeholder('-'),
                TextEntry::make('classification_code')
                    ->label(\Modules\Core\Support\FilamentUi::field('classification_code'))
                    ->placeholder('-'),
                TextEntry::make('keywords')
                    ->label(\Modules\Core\Support\FilamentUi::field('keywords'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('synopsis')
                    ->label(\Modules\Core\Support\FilamentUi::field('synopsis'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                ImageEntry::make('cover_image')
                    ->placeholder('-'),
                TextEntry::make('preview_url')
                    ->label(\Modules\Core\Support\FilamentUi::field('preview_url'))
                    ->placeholder('-'),
                TextEntry::make('purchase_price')
                    ->label(\Modules\Core\Support\FilamentUi::field('purchase_price'))
                    ->money()
                    ->placeholder('-'),
                TextEntry::make('source')
                    ->label(\Modules\Core\Support\FilamentUi::field('source'))
                    ->placeholder('-'),
                TextEntry::make('total_copies')
                    ->label(\Modules\Core\Support\FilamentUi::field('total_copies'))
                    ->numeric(),
                TextEntry::make('available_copies')
                    ->label(\Modules\Core\Support\FilamentUi::field('available_copies'))
                    ->numeric(),
                TextEntry::make('location_shelf')
                    ->label(\Modules\Core\Support\FilamentUi::field('location_shelf'))
                    ->placeholder('-'),
                IconEntry::make('is_active')
                    ->boolean(),
                IconEntry::make('is_reference_only')
                    ->boolean(),
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
