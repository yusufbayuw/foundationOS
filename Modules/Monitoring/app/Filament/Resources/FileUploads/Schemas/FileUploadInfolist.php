<?php

namespace Modules\Monitoring\Filament\Resources\FileUploads\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class FileUploadInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Context')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('tenant.name')
                            ->label(FilamentUi::text('Tenant'))
                            ->placeholder('-'),
                        TextEntry::make('uploaded_by')
                            ->label(FilamentUi::field('uploaded_by'))
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('organization.name')
                            ->label(FilamentUi::text('Organization'))
                            ->placeholder('-'),
                        TextEntry::make('fileable_type')
                            ->label(FilamentUi::field('fileable_type'))
                            ->placeholder('-'),
                        TextEntry::make('fileable_id')
                            ->label(FilamentUi::field('fileable_id'))
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('collection_name')
                            ->label(FilamentUi::field('collection_name'))
                            ->placeholder('-'),
                    ]),

                Section::make('File Details')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('original_name')
                            ->label(FilamentUi::field('original_name')),
                        TextEntry::make('stored_name')
                            ->label(FilamentUi::field('stored_name')),
                        TextEntry::make('mime_type')
                            ->label(FilamentUi::field('mime_type'))
                            ->placeholder('-'),
                        TextEntry::make('extension')
                            ->label(FilamentUi::field('extension'))
                            ->placeholder('-'),
                        TextEntry::make('file_size')
                            ->label(FilamentUi::field('file_size'))
                            ->numeric(),
                        TextEntry::make('checksum')
                            ->label(FilamentUi::field('checksum'))
                            ->placeholder('-'),
                    ]),

                Section::make('Storage')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('disk')
                            ->label(FilamentUi::field('disk')),
                        TextEntry::make('directory')
                            ->label(FilamentUi::field('directory'))
                            ->placeholder('-'),
                        IconEntry::make('is_public')
                            ->boolean(),
                        TextEntry::make('uploaded_at')
                            ->label(FilamentUi::field('uploaded_at'))
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('metadata')
                            ->label(FilamentUi::field('metadata'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),

                Section::make('Timestamps')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('created_at')
                            ->label(FilamentUi::field('created_at'))
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('updated_at')
                            ->label(FilamentUi::field('updated_at'))
                            ->dateTime()
                            ->placeholder('-'),
                    ]),
            ]);
    }
}
