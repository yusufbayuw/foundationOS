<?php

namespace Modules\Library\Filament\Resources\Books\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Facades\Filament;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Illuminate\Database\Eloquent\Builder;

class BookForm
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
                    ->helperText('Kosongkan untuk kebijakan perpustakaan tenant-wide (terpusat).'),
                Select::make('book_category_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('book_category_id'))
                    ->relationship('category', 'name', modifyQueryUsing: function (Builder $query): void {
                        app(\Modules\Library\Support\LibraryScopeResolver::class)->apply($query, auth()->user());
                    })
                    ->searchable()
                    ->preload(),
                Select::make('publisher_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('publisher_id'))
                    ->relationship('publisher', 'name', modifyQueryUsing: function (Builder $query): void {
                        app(\Modules\Library\Support\LibraryScopeResolver::class)->apply($query, auth()->user());
                    })
                    ->searchable()
                    ->preload()
                    ->helperText('Opsional. Jika diisi, nama penerbit akan mengikuti master data.'),
                TextInput::make('isbn')
                    ->label(\Modules\Core\Support\FilamentUi::field('isbn')),
                TextInput::make('isbn13')
                    ->label(\Modules\Core\Support\FilamentUi::field('isbn13')),
                TextInput::make('title')
                    ->label(\Modules\Core\Support\FilamentUi::field('title'))
                    ->required(),
                TextInput::make('subtitle')
                    ->label(\Modules\Core\Support\FilamentUi::field('subtitle')),
                Textarea::make('authors')
                    ->label(\Modules\Core\Support\FilamentUi::field('authors'))
                    ->required()
                    ->helperText('Pisahkan penulis dengan koma.')
                    ->columnSpanFull(),
                Select::make('authorItems')
                    ->label(\Modules\Core\Support\FilamentUi::text('Authors (Master Data)'))
                    ->relationship('authorItems', 'name', modifyQueryUsing: function (Builder $query): void {
                        app(\Modules\Library\Support\LibraryScopeResolver::class)->apply($query, auth()->user());
                    })
                    ->searchable()
                    ->multiple()
                    ->preload()
                    ->helperText('Opsional. Pilih penulis dari master data untuk konsistensi laporan.')
                    ->columnSpanFull(),
                TextInput::make('publisher')
                    ->label(\Modules\Core\Support\FilamentUi::field('publisher')),
                TextInput::make('publication_year')
                    ->label(\Modules\Core\Support\FilamentUi::field('publication_year')),
                TextInput::make('publication_place')
                    ->label(\Modules\Core\Support\FilamentUi::field('publication_place')),
                TextInput::make('edition')
                    ->label(\Modules\Core\Support\FilamentUi::field('edition')),
                TextInput::make('volume')
                    ->label(\Modules\Core\Support\FilamentUi::field('volume')),
                TextInput::make('series')
                    ->label(\Modules\Core\Support\FilamentUi::field('series')),
                TextInput::make('language')
                    ->label(\Modules\Core\Support\FilamentUi::field('language'))
                    ->required()
                    ->default('Indonesian'),
                Select::make('gmd_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('gmd_id'))
                    ->relationship('gmd', 'name', modifyQueryUsing: function (Builder $query): void {
                        app(\Modules\Library\Support\LibraryScopeResolver::class)->apply($query, auth()->user());
                    })
                    ->searchable()
                    ->preload()
                    ->helperText('General Material Designation (GMD).'),
                Select::make('collection_type_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('collection_type_id'))
                    ->relationship('collectionType', 'name', modifyQueryUsing: function (Builder $query): void {
                        app(\Modules\Library\Support\LibraryScopeResolver::class)->apply($query, auth()->user());
                    })
                    ->searchable()
                    ->preload(),
                Select::make('frequency_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('frequency_id'))
                    ->relationship('frequency', 'name', modifyQueryUsing: function (Builder $query): void {
                        app(\Modules\Library\Support\LibraryScopeResolver::class)->apply($query, auth()->user());
                    })
                    ->searchable()
                    ->preload()
                    ->helperText('Isi jika judul ini berjenis serial/jurnal.'),
                TextInput::make('pages')
                    ->label(\Modules\Core\Support\FilamentUi::field('pages'))
                    ->numeric(),
                TextInput::make('dimensions')
                    ->label(\Modules\Core\Support\FilamentUi::field('dimensions')),
                TextInput::make('weight_grams')
                    ->label(\Modules\Core\Support\FilamentUi::field('weight_grams'))
                    ->numeric(),
                TextInput::make('binding_type')
                    ->label(\Modules\Core\Support\FilamentUi::field('binding_type')),
                TextInput::make('classification_code')
                    ->label(\Modules\Core\Support\FilamentUi::field('classification_code')),
                Select::make('subjectItems')
                    ->label(\Modules\Core\Support\FilamentUi::text('Subjects (Master Data)'))
                    ->relationship('subjectItems', 'name', modifyQueryUsing: function (Builder $query): void {
                        app(\Modules\Library\Support\LibraryScopeResolver::class)->apply($query, auth()->user());
                    })
                    ->searchable()
                    ->multiple()
                    ->preload()
                    ->helperText('Opsional. Pilih topik/subject untuk klasifikasi laporan.')
                    ->columnSpanFull(),
                Textarea::make('keywords')
                    ->label(\Modules\Core\Support\FilamentUi::field('keywords'))
                    ->helperText('Pisahkan kata kunci dengan koma.')
                    ->columnSpanFull(),
                Textarea::make('synopsis')
                    ->label(\Modules\Core\Support\FilamentUi::field('synopsis'))
                    ->columnSpanFull(),
                FileUpload::make('cover_image')
                    ->label(\Modules\Core\Support\FilamentUi::field('cover_image'))
                    ->image(),
                TextInput::make('preview_url')
                    ->label(\Modules\Core\Support\FilamentUi::field('preview_url'))
                    ->url(),
                TextInput::make('purchase_price')
                    ->label(\Modules\Core\Support\FilamentUi::field('purchase_price'))
                    ->numeric()
                    ->prefix('Rp'),
                TextInput::make('source')
                    ->label(\Modules\Core\Support\FilamentUi::field('source')),
                TextInput::make('total_copies')
                    ->label(\Modules\Core\Support\FilamentUi::field('total_copies'))
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('available_copies')
                    ->label(\Modules\Core\Support\FilamentUi::field('available_copies'))
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('location_shelf')
                    ->label(\Modules\Core\Support\FilamentUi::field('location_shelf')),
                Toggle::make('is_active')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_active'))
                    ->default(true)
                    ->required(),
                Toggle::make('is_reference_only')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_reference_only'))
                    ->default(false)
                    ->required(),
            ]);
    }
}
