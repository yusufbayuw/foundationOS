<?php

namespace Modules\Library\Filament\Resources\Books\Schemas;

use Filament\Facades\Filament;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;
use Modules\Library\Support\LibraryScopeResolver;

class BookForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('Scope & Category'))
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
                            ->helperText(FilamentUi::text('Leave blank for the tenant-wide library policy.')),
                        Select::make('book_category_id')
                            ->label(FilamentUi::field('book_category_id'))
                            ->relationship('category', 'name', modifyQueryUsing: function (Builder $query): void {
                                app(LibraryScopeResolver::class)->apply($query, auth()->user());
                            })
                            ->searchable()
                            ->preload(),
                        Select::make('publisher_id')
                            ->label(FilamentUi::field('publisher_id'))
                            ->relationship('publisher', 'name', modifyQueryUsing: function (Builder $query): void {
                                app(LibraryScopeResolver::class)->apply($query, auth()->user());
                            })
                            ->searchable()
                            ->preload()
                            ->helperText(FilamentUi::text('Optional. If filled, publisher name follows master data.')),
                    ]),

                Section::make(FilamentUi::text('Identification'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('isbn')
                            ->label(FilamentUi::field('isbn')),
                        TextInput::make('isbn13')
                            ->label(FilamentUi::field('isbn13')),
                        TextInput::make('title')
                            ->label(FilamentUi::field('title'))
                            ->required(),
                        TextInput::make('subtitle')
                            ->label(FilamentUi::field('subtitle')),
                        Textarea::make('authors')
                            ->label(FilamentUi::field('authors'))
                            ->required()
                            ->helperText(FilamentUi::text('Separate authors with commas.'))
                            ->columnSpanFull(),
                        Select::make('authorItems')
                            ->label(FilamentUi::text('Authors (Master Data)'))
                            ->relationship('authorItems', 'name', modifyQueryUsing: function (Builder $query): void {
                                app(LibraryScopeResolver::class)->apply($query, auth()->user());
                            })
                            ->searchable()
                            ->multiple()
                            ->preload()
                            ->helperText(FilamentUi::text('Optional. Select authors from master data for report consistency.'))
                            ->columnSpanFull(),
                    ]),

                Section::make(FilamentUi::text('Publication'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('publisher')
                            ->label(FilamentUi::field('publisher')),
                        TextInput::make('publication_year')
                            ->label(FilamentUi::field('publication_year')),
                        TextInput::make('publication_place')
                            ->label(FilamentUi::field('publication_place')),
                        TextInput::make('edition')
                            ->label(FilamentUi::field('edition')),
                        TextInput::make('volume')
                            ->label(FilamentUi::field('volume')),
                        TextInput::make('series')
                            ->label(FilamentUi::field('series')),
                        TextInput::make('language')
                            ->label(FilamentUi::field('language'))
                            ->required()
                            ->default('Indonesian'),
                    ]),

                Section::make(FilamentUi::text('Classification & Physical Details'))
                    ->columns(2)
                    ->schema([
                        Select::make('gmd_id')
                            ->label(FilamentUi::field('gmd_id'))
                            ->relationship('gmd', 'name', modifyQueryUsing: function (Builder $query): void {
                                app(LibraryScopeResolver::class)->apply($query, auth()->user());
                            })
                            ->searchable()
                            ->preload()
                            ->helperText(FilamentUi::text('General Material Designation (GMD).')),
                        Select::make('collection_type_id')
                            ->label(FilamentUi::field('collection_type_id'))
                            ->relationship('collectionType', 'name', modifyQueryUsing: function (Builder $query): void {
                                app(LibraryScopeResolver::class)->apply($query, auth()->user());
                            })
                            ->searchable()
                            ->preload(),
                        Select::make('frequency_id')
                            ->label(FilamentUi::field('frequency_id'))
                            ->relationship('frequency', 'name', modifyQueryUsing: function (Builder $query): void {
                                app(LibraryScopeResolver::class)->apply($query, auth()->user());
                            })
                            ->searchable()
                            ->preload()
                            ->helperText(FilamentUi::text('Fill if this title is a serial or journal.')),
                        TextInput::make('pages')
                            ->label(FilamentUi::field('pages'))
                            ->numeric(),
                        TextInput::make('dimensions')
                            ->label(FilamentUi::field('dimensions')),
                        TextInput::make('weight_grams')
                            ->label(FilamentUi::field('weight_grams'))
                            ->numeric(),
                        TextInput::make('binding_type')
                            ->label(FilamentUi::field('binding_type')),
                        TextInput::make('classification_code')
                            ->label(FilamentUi::field('classification_code')),
                        Select::make('subjectItems')
                            ->label(FilamentUi::text('Subjects (Master Data)'))
                            ->relationship('subjectItems', 'name', modifyQueryUsing: function (Builder $query): void {
                                app(LibraryScopeResolver::class)->apply($query, auth()->user());
                            })
                            ->searchable()
                            ->multiple()
                            ->preload()
                            ->helperText(FilamentUi::text('Optional. Select topics for report classification.'))
                            ->columnSpanFull(),
                        Textarea::make('keywords')
                            ->label(FilamentUi::field('keywords'))
                            ->helperText(FilamentUi::text('Separate keywords with commas.'))
                            ->columnSpanFull(),
                        Textarea::make('synopsis')
                            ->label(FilamentUi::field('synopsis'))
                            ->columnSpanFull(),
                    ]),

                Section::make(FilamentUi::text('Media & Acquisition'))
                    ->columns(2)
                    ->schema([
                        FileUpload::make('cover_image')
                            ->label(FilamentUi::field('cover_image'))
                            ->image(),
                        TextInput::make('preview_url')
                            ->label(FilamentUi::field('preview_url'))
                            ->url(),
                        TextInput::make('purchase_price')
                            ->label(FilamentUi::field('purchase_price'))
                            ->numeric()
                            ->prefix('Rp'),
                        TextInput::make('source')
                            ->label(FilamentUi::field('source')),
                    ]),

                Section::make(FilamentUi::text('Inventory & Status'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('total_copies')
                            ->label(FilamentUi::field('total_copies'))
                            ->required()
                            ->numeric()
                            ->default(0),
                        TextInput::make('available_copies')
                            ->label(FilamentUi::field('available_copies'))
                            ->required()
                            ->numeric()
                            ->default(0),
                        TextInput::make('location_shelf')
                            ->label(FilamentUi::field('location_shelf')),
                        Toggle::make('is_active')
                            ->label(FilamentUi::field('is_active'))
                            ->default(true)
                            ->required(),
                        Toggle::make('is_reference_only')
                            ->label(FilamentUi::field('is_reference_only'))
                            ->default(false)
                            ->required(),
                    ]),
            ]);
    }
}
