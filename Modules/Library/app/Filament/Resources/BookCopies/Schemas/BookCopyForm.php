<?php

namespace Modules\Library\Filament\Resources\BookCopies\Schemas;

use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;
use Modules\Library\Support\LibraryScopeResolver;

class BookCopyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('Scope'))
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        Select::make('organization_id')
                            ->label(FilamentUi::field('organization_id'))
                            ->relationship('organization', 'name', modifyQueryUsing: function (Builder $query): void {
                                if (Filament::getTenant()) {
                                    $query->where('tenant_id', Filament::getTenant()->getKey());
                                }
                            })
                            ->nullable()
                            ->helperText('Opsional. Kosongkan untuk copy tenant-wide.'),
                        Select::make('book_id')
                            ->label(FilamentUi::field('book_id'))
                            ->relationship('book', 'title', modifyQueryUsing: function (Builder $query): void {
                                app(LibraryScopeResolver::class)->apply($query, auth()->user());
                            })
                            ->searchable()
                            ->preload()
                            ->required(),
                    ]),

                Section::make(FilamentUi::text('Identification'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('copy_number')
                            ->label(FilamentUi::field('copy_number'))
                            ->required(),
                        TextInput::make('barcode')
                            ->label(FilamentUi::field('barcode')),
                    ]),

                Section::make(FilamentUi::text('Acquisition'))
                    ->columns(2)
                    ->schema([
                        DatePicker::make('acquisition_date')
                            ->label(FilamentUi::field('acquisition_date')),
                        TextInput::make('acquisition_source')
                            ->label(FilamentUi::field('acquisition_source')),
                        TextInput::make('price')
                            ->label(FilamentUi::field('price'))
                            ->numeric()
                            ->prefix('Rp'),
                        TextInput::make('condition')
                            ->label(FilamentUi::field('condition')),
                    ]),

                Section::make(FilamentUi::text('Classification & Status'))
                    ->columns(2)
                    ->schema([
                        Select::make('item_status_id')
                            ->label(FilamentUi::field('item_status_id'))
                            ->relationship('itemStatus', 'name', modifyQueryUsing: function (Builder $query): void {
                                app(LibraryScopeResolver::class)->apply($query, auth()->user());
                            })
                            ->searchable()
                            ->preload(),
                        Select::make('collection_type_id')
                            ->label(FilamentUi::field('collection_type_id'))
                            ->relationship('collectionType', 'name', modifyQueryUsing: function (Builder $query): void {
                                app(LibraryScopeResolver::class)->apply($query, auth()->user());
                            })
                            ->searchable()
                            ->preload(),
                        TextInput::make('status')
                            ->label(FilamentUi::field('status'))
                            ->required()
                            ->default('available'),
                    ]),

                Section::make(FilamentUi::text('Location & Notes'))
                    ->columns(2)
                    ->schema([
                        Select::make('location_id')
                            ->label(FilamentUi::field('location_id'))
                            ->relationship('location', 'name', modifyQueryUsing: function (Builder $query): void {
                                app(LibraryScopeResolver::class)->apply($query, auth()->user());
                            })
                            ->searchable()
                            ->preload(),
                        TextInput::make('location_shelf')
                            ->label(FilamentUi::field('location_shelf'))
                            ->helperText('Opsional. Isi jika lokasi belum terdaftar di master data.'),
                        Textarea::make('notes')
                            ->label(FilamentUi::field('notes'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
