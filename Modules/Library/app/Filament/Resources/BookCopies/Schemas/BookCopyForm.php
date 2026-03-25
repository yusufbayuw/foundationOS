<?php

namespace Modules\Library\Filament\Resources\BookCopies\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class BookCopyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant_id'))
                    ->relationship('tenant', 'name')
                    ->required(),
                Select::make('book_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('book_id'))
                    ->relationship('book', 'title')
                    ->required(),
                TextInput::make('copy_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('copy_number'))
                    ->required(),
                TextInput::make('barcode')
                    ->label(\Modules\Core\Support\FilamentUi::field('barcode')),
                DatePicker::make('acquisition_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('acquisition_date')),
                TextInput::make('acquisition_source')
                    ->label(\Modules\Core\Support\FilamentUi::field('acquisition_source')),
                TextInput::make('price')
                    ->label(\Modules\Core\Support\FilamentUi::field('price'))
                    ->numeric()
                    ->prefix('$'),
                TextInput::make('condition')
                    ->label(\Modules\Core\Support\FilamentUi::field('condition')),
                TextInput::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status'))
                    ->required()
                    ->default('available'),
                TextInput::make('location_shelf')
                    ->label(\Modules\Core\Support\FilamentUi::field('location_shelf')),
                Textarea::make('notes')
                    ->label(\Modules\Core\Support\FilamentUi::field('notes'))
                    ->columnSpanFull(),
            ]);
    }
}
