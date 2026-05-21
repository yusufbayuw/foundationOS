<?php

namespace Modules\Monitoring\Filament\Resources\FileUploads\Tables;

use App\Filament\Imports\FileUploadImporter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ImportTableActions;
use Modules\Core\Support\FilamentUi;

class FileUploadsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('uploaded_by')
                    ->label(FilamentUi::field('uploaded_by'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('organization.name')
                    ->label(FilamentUi::field('organization.name'))
                    ->searchable(),
                TextColumn::make('fileable_type')
                    ->label(FilamentUi::field('fileable_type'))
                    ->searchable(),
                TextColumn::make('fileable_id')
                    ->label(FilamentUi::field('fileable_id'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('collection_name')
                    ->label(FilamentUi::field('collection_name'))
                    ->searchable(),
                TextColumn::make('disk')
                    ->label(FilamentUi::field('disk'))
                    ->searchable(),
                TextColumn::make('directory')
                    ->label(FilamentUi::field('directory'))
                    ->searchable(),
                TextColumn::make('original_name')
                    ->label(FilamentUi::field('original_name'))
                    ->searchable(),
                TextColumn::make('stored_name')
                    ->label(FilamentUi::field('stored_name'))
                    ->searchable(),
                TextColumn::make('mime_type')
                    ->label(FilamentUi::field('mime_type'))
                    ->searchable(),
                TextColumn::make('extension')
                    ->label(FilamentUi::field('extension'))
                    ->searchable(),
                TextColumn::make('file_size')
                    ->label(FilamentUi::field('file_size'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('checksum')
                    ->label(FilamentUi::field('checksum'))
                    ->searchable(),
                IconColumn::make('is_public')
                    ->label(FilamentUi::field('is_public'))
                    ->boolean(),
                TextColumn::make('uploaded_at')
                    ->label(FilamentUi::field('uploaded_at'))
                    ->dateTime()
                    ->sortable(),
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
                SelectFilter::make('disk')
                    ->options([
                        'public' => 'Public',
                        'local' => 'Local',
                        's3' => 'S3',
                    ]),
                SelectFilter::make('extension')
                    ->options([
                        'pdf' => 'PDF',
                        'jpg' => 'JPG',
                        'jpeg' => 'JPEG',
                        'png' => 'PNG',
                        'doc' => 'DOC',
                        'docx' => 'DOCX',
                        'xls' => 'XLS',
                        'xlsx' => 'XLSX',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->headerActions([
                ...ImportTableActions::make(FileUploadImporter::class),
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
