<?php

namespace Modules\Monitoring\Filament\Resources\FileUploads\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class FileUploadInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Tenant'))
                    ->placeholder('-'),
                TextEntry::make('uploaded_by')
                    ->label(\Modules\Core\Support\FilamentUi::field('uploaded_by'))
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('organization.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Organization'))
                    ->placeholder('-'),
                TextEntry::make('fileable_type')
                    ->label(\Modules\Core\Support\FilamentUi::field('fileable_type'))
                    ->placeholder('-'),
                TextEntry::make('fileable_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('fileable_id'))
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('collection_name')
                    ->label(\Modules\Core\Support\FilamentUi::field('collection_name'))
                    ->placeholder('-'),
                TextEntry::make('disk')
                    ->label(\Modules\Core\Support\FilamentUi::field('disk')),
                TextEntry::make('directory')
                    ->label(\Modules\Core\Support\FilamentUi::field('directory'))
                    ->placeholder('-'),
                TextEntry::make('original_name')
                    ->label(\Modules\Core\Support\FilamentUi::field('original_name')),
                TextEntry::make('stored_name')
                    ->label(\Modules\Core\Support\FilamentUi::field('stored_name')),
                TextEntry::make('mime_type')
                    ->label(\Modules\Core\Support\FilamentUi::field('mime_type'))
                    ->placeholder('-'),
                TextEntry::make('extension')
                    ->label(\Modules\Core\Support\FilamentUi::field('extension'))
                    ->placeholder('-'),
                TextEntry::make('file_size')
                    ->label(\Modules\Core\Support\FilamentUi::field('file_size'))
                    ->numeric(),
                TextEntry::make('checksum')
                    ->label(\Modules\Core\Support\FilamentUi::field('checksum'))
                    ->placeholder('-'),
                IconEntry::make('is_public')
                    ->boolean(),
                TextEntry::make('metadata')
                    ->label(\Modules\Core\Support\FilamentUi::field('metadata'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('uploaded_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('uploaded_at'))
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('created_at'))
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('updated_at'))
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
