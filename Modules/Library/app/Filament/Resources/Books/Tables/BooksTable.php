<?php

namespace Modules\Library\Filament\Resources\Books\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\TrashedFilter;
use Modules\Core\Filament\Support\ImportTableActions;

class BooksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('organization.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('organization.name'))
                    ->searchable(),
                TextColumn::make('book_category_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('book_category_id'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('isbn')
                    ->label(\Modules\Core\Support\FilamentUi::field('isbn'))
                    ->searchable(),
                TextColumn::make('isbn13')
                    ->label(\Modules\Core\Support\FilamentUi::field('isbn13'))
                    ->searchable(),
                TextColumn::make('title')
                    ->label(\Modules\Core\Support\FilamentUi::field('title'))
                    ->searchable(),
                TextColumn::make('subtitle')
                    ->label(\Modules\Core\Support\FilamentUi::field('subtitle'))
                    ->searchable(),
                TextColumn::make('publisher')
                    ->label(\Modules\Core\Support\FilamentUi::field('publisher'))
                    ->searchable(),
                TextColumn::make('publication_year')
                    ->label(\Modules\Core\Support\FilamentUi::field('publication_year'))
                    ->searchable(),
                TextColumn::make('publication_place')
                    ->label(\Modules\Core\Support\FilamentUi::field('publication_place'))
                    ->searchable(),
                TextColumn::make('edition')
                    ->label(\Modules\Core\Support\FilamentUi::field('edition'))
                    ->searchable(),
                TextColumn::make('volume')
                    ->label(\Modules\Core\Support\FilamentUi::field('volume'))
                    ->searchable(),
                TextColumn::make('series')
                    ->label(\Modules\Core\Support\FilamentUi::field('series'))
                    ->searchable(),
                TextColumn::make('language')
                    ->label(\Modules\Core\Support\FilamentUi::field('language'))
                    ->searchable(),
                TextColumn::make('pages')
                    ->label(\Modules\Core\Support\FilamentUi::field('pages'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('dimensions')
                    ->label(\Modules\Core\Support\FilamentUi::field('dimensions'))
                    ->searchable(),
                TextColumn::make('weight_grams')
                    ->label(\Modules\Core\Support\FilamentUi::field('weight_grams'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('binding_type')
                    ->label(\Modules\Core\Support\FilamentUi::field('binding_type'))
                    ->searchable(),
                TextColumn::make('classification_code')
                    ->label(\Modules\Core\Support\FilamentUi::field('classification_code'))
                    ->searchable(),
                ImageColumn::make('cover_image')
                    ->label(\Modules\Core\Support\FilamentUi::field('cover_image')),
                TextColumn::make('preview_url')
                    ->label(\Modules\Core\Support\FilamentUi::field('preview_url'))
                    ->searchable(),
                TextColumn::make('purchase_price')
                    ->label(\Modules\Core\Support\FilamentUi::field('purchase_price'))
                    ->money()
                    ->sortable(),
                TextColumn::make('source')
                    ->label(\Modules\Core\Support\FilamentUi::field('source'))
                    ->searchable(),
                TextColumn::make('total_copies')
                    ->label(\Modules\Core\Support\FilamentUi::field('total_copies'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('available_copies')
                    ->label(\Modules\Core\Support\FilamentUi::field('available_copies'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('location_shelf')
                    ->label(\Modules\Core\Support\FilamentUi::field('location_shelf'))
                    ->searchable(),
                IconColumn::make('is_active')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_active'))
                    ->boolean(),
                IconColumn::make('is_reference_only')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_reference_only'))
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->headerActions([
                ...ImportTableActions::make(\App\Filament\Imports\BookImporter::class),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                                                        ]),
            ]);
    }
}
