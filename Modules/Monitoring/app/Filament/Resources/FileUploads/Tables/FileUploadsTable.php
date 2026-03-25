<?php

namespace Modules\Monitoring\Filament\Resources\FileUploads\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FileUploadsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('uploaded_by')
                    ->label(\Modules\Core\Support\FilamentUi::field('uploaded_by'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('organization.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('organization.name'))
                    ->searchable(),
                TextColumn::make('fileable_type')
                    ->label(\Modules\Core\Support\FilamentUi::field('fileable_type'))
                    ->searchable(),
                TextColumn::make('fileable_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('fileable_id'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('collection_name')
                    ->label(\Modules\Core\Support\FilamentUi::field('collection_name'))
                    ->searchable(),
                TextColumn::make('disk')
                    ->label(\Modules\Core\Support\FilamentUi::field('disk'))
                    ->searchable(),
                TextColumn::make('directory')
                    ->label(\Modules\Core\Support\FilamentUi::field('directory'))
                    ->searchable(),
                TextColumn::make('original_name')
                    ->label(\Modules\Core\Support\FilamentUi::field('original_name'))
                    ->searchable(),
                TextColumn::make('stored_name')
                    ->label(\Modules\Core\Support\FilamentUi::field('stored_name'))
                    ->searchable(),
                TextColumn::make('mime_type')
                    ->label(\Modules\Core\Support\FilamentUi::field('mime_type'))
                    ->searchable(),
                TextColumn::make('extension')
                    ->label(\Modules\Core\Support\FilamentUi::field('extension'))
                    ->searchable(),
                TextColumn::make('file_size')
                    ->label(\Modules\Core\Support\FilamentUi::field('file_size'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('checksum')
                    ->label(\Modules\Core\Support\FilamentUi::field('checksum'))
                    ->searchable(),
                IconColumn::make('is_public')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_public'))
                    ->boolean(),
                TextColumn::make('uploaded_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('uploaded_at'))
                    ->dateTime()
                    ->sortable(),
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
            ->filters([])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                                                        ]),
            ]);
    }
}
