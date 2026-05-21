<?php

namespace Modules\Library\Filament\Resources\Books\Tables;

use App\Filament\Imports\BookImporter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ImportTableActions;
use Modules\Core\Support\FilamentUi;

class BooksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('organization.name')
                    ->label(FilamentUi::field('organization.name'))
                    ->searchable(),
                TextColumn::make('book_category_id')
                    ->label(FilamentUi::field('book_category_id'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('isbn')
                    ->label(FilamentUi::field('isbn'))
                    ->searchable(),
                TextColumn::make('isbn13')
                    ->label(FilamentUi::field('isbn13'))
                    ->searchable(),
                TextColumn::make('title')
                    ->label(FilamentUi::field('title'))
                    ->searchable(),
                TextColumn::make('subtitle')
                    ->label(FilamentUi::field('subtitle'))
                    ->searchable(),
                TextColumn::make('publisher')
                    ->label(FilamentUi::field('publisher'))
                    ->searchable(),
                TextColumn::make('publication_year')
                    ->label(FilamentUi::field('publication_year'))
                    ->searchable(),
                TextColumn::make('publication_place')
                    ->label(FilamentUi::field('publication_place'))
                    ->searchable(),
                TextColumn::make('edition')
                    ->label(FilamentUi::field('edition'))
                    ->searchable(),
                TextColumn::make('volume')
                    ->label(FilamentUi::field('volume'))
                    ->searchable(),
                TextColumn::make('series')
                    ->label(FilamentUi::field('series'))
                    ->searchable(),
                TextColumn::make('language')
                    ->label(FilamentUi::field('language'))
                    ->searchable(),
                TextColumn::make('pages')
                    ->label(FilamentUi::field('pages'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('dimensions')
                    ->label(FilamentUi::field('dimensions'))
                    ->searchable(),
                TextColumn::make('weight_grams')
                    ->label(FilamentUi::field('weight_grams'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('binding_type')
                    ->label(FilamentUi::field('binding_type'))
                    ->searchable(),
                TextColumn::make('classification_code')
                    ->label(FilamentUi::field('classification_code'))
                    ->searchable(),
                ImageColumn::make('cover_image')
                    ->label(FilamentUi::field('cover_image')),
                TextColumn::make('preview_url')
                    ->label(FilamentUi::field('preview_url'))
                    ->searchable(),
                TextColumn::make('purchase_price')
                    ->label(FilamentUi::field('purchase_price'))
                    ->money()
                    ->sortable(),
                TextColumn::make('source')
                    ->label(FilamentUi::field('source'))
                    ->searchable(),
                TextColumn::make('total_copies')
                    ->label(FilamentUi::field('total_copies'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('available_copies')
                    ->label(FilamentUi::field('available_copies'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('location_shelf')
                    ->label(FilamentUi::field('location_shelf'))
                    ->searchable(),
                IconColumn::make('is_active')
                    ->label(FilamentUi::field('is_active'))
                    ->boolean(),
                IconColumn::make('is_reference_only')
                    ->label(FilamentUi::field('is_reference_only'))
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label(FilamentUi::field('created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(FilamentUi::field('updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('language')
                    ->options([
                        'id' => 'Indonesian',
                        'en' => 'English',
                        'ar' => 'Arabic',
                        'other' => 'Other',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->headerActions([
                ...ImportTableActions::make(BookImporter::class),
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
