<?php

namespace Modules\Library\Filament\Resources\BookCopies\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Facades\Filament;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class BookCopyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant_id'))
                    ->relationship('tenant', 'name')
                    ->default(Filament::getTenant()?->getKey())
                    ->disabled(Filament::getTenant() !== null)
                    ->dehydrated()
                    ->required(),
                Select::make('organization_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('organization_id'))
                    ->relationship('organization', 'name', modifyQueryUsing: function (Builder $query): void {
                        if (Filament::getTenant()) {
                            $query->where('tenant_id', Filament::getTenant()->getKey());
                        }
                    })
                    ->nullable()
                    ->helperText('Opsional. Kosongkan untuk copy tenant-wide.'),
                Select::make('book_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('book_id'))
                    ->relationship('book', 'title', modifyQueryUsing: function (Builder $query): void {
                        app(\Modules\Library\Support\LibraryScopeResolver::class)->apply($query, auth()->user());
                    })
                    ->searchable()
                    ->preload()
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
                    ->prefix('Rp'),
                TextInput::make('condition')
                    ->label(\Modules\Core\Support\FilamentUi::field('condition')),
                Select::make('item_status_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('item_status_id'))
                    ->relationship('itemStatus', 'name', modifyQueryUsing: function (Builder $query): void {
                        app(\Modules\Library\Support\LibraryScopeResolver::class)->apply($query, auth()->user());
                    })
                    ->searchable()
                    ->preload(),
                Select::make('collection_type_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('collection_type_id'))
                    ->relationship('collectionType', 'name', modifyQueryUsing: function (Builder $query): void {
                        app(\Modules\Library\Support\LibraryScopeResolver::class)->apply($query, auth()->user());
                    })
                    ->searchable()
                    ->preload(),
                TextInput::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status'))
                    ->required()
                    ->default('available'),
                Select::make('location_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('location_id'))
                    ->relationship('location', 'name', modifyQueryUsing: function (Builder $query): void {
                        app(\Modules\Library\Support\LibraryScopeResolver::class)->apply($query, auth()->user());
                    })
                    ->searchable()
                    ->preload(),
                TextInput::make('location_shelf')
                    ->label(\Modules\Core\Support\FilamentUi::field('location_shelf'))
                    ->helperText('Opsional. Isi jika lokasi belum terdaftar di master data.'),
                Textarea::make('notes')
                    ->label(\Modules\Core\Support\FilamentUi::field('notes'))
                    ->columnSpanFull(),
            ]);
    }
}
